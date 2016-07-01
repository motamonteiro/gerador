<?php
if (!function_exists('preencherStub')) {
    function preencherStub($path, $nmeStub, $replaces){
        $stub = file_get_contents($path.$nmeStub.'.stub');

        foreach ($replaces as $search => $replace) {
          $stub = str_replace('$' . strtoupper($search) . '$', $replace, $stub);
        }

        return $stub;
    }
}

if (!function_exists('listarObjTabelas')) {
    function listarObjTabelas($app) {

        $nomesDasTabelas = listarTabelas($app);
        $tabelas = [];

        foreach ($nomesDasTabelas as $nomeDaTabela) {

            $tabela = new \MotaMonteiro\Gerador\Entities\Tabela($nomeDaTabela, $app['config']['table_prefix']);
            $nomesDasColunas = listarColunas($app, $nomeDaTabela);

            foreach ($nomesDasColunas as $nomeDaColuna) {
                $nulo = ($nomeDaColuna['Null'] == 'NO') ? 0 : 1;
                $auto_increment = ($nomeDaColuna['Extra'] == 'auto_increment') ? 1 : 0;
                $coluna = new \MotaMonteiro\Gerador\Entities\Coluna($nomeDaColuna['Field'], $nomeDaColuna['Type'], $nulo, $nomeDaColuna['Key'], $auto_increment);                

                if ($coluna->getChave() == 'MUL') {

                    $nomeDaTabelaEstrangeira = detalharTabelaEstrangeira($app, $nomeDaTabela, $coluna->getCampo());

                    if ($nomeDaTabelaEstrangeira == '') {
                        $coluna->setChave('');
                    } else {
                        $nomesDasColunasEstrangeiras = listarColunas($app, $nomeDaTabelaEstrangeira['tabela']);
                        $tabelaEstrangeira = new \MotaMonteiro\Gerador\Entities\Tabela($nomeDaTabelaEstrangeira['tabela'], $app['config']['table_prefix']);

                        foreach ($nomesDasColunasEstrangeiras as $nomeDaColunaEstrangeira) {
                            $nulo = ($nomeDaColunaEstrangeira['Null'] == 'NO') ? 0 : 1;
                            $auto_increment = ($nomeDaColunaEstrangeira['Extra'] == 'auto_increment') ? 1 : 0;
                            $colunaEstrangeira = new \MotaMonteiro\Gerador\Entities\Coluna($nomeDaColunaEstrangeira['Field'], $nomeDaColunaEstrangeira['Type'], $nulo, $nomeDaColunaEstrangeira['Key'], $auto_increment);
                            $coluna->setCampoTabelaEstrangeira($tabelaEstrangeira->getNome());
                            $coluna->setCampoChaveEstrangeira($nomeDaTabelaEstrangeira['coluna']);
                            $tabelaEstrangeira->addColuna($colunaEstrangeira);
                        }

                        $tabela->addTabelaEstrangeira($tabelaEstrangeira);
                    }
                }

                $tabela->addColuna($coluna);                
                
            }

            array_push($tabelas, $tabela);
        }

        return $tabelas;
    }
}

if (!function_exists('listarTabelas')) {
    function listarTabelas($app) {

        $stmt = $app['db']->query("SHOW TABLES FROM ".$app['config']['db_name']."");
        $result = $stmt->fetchAll(PDO::FETCH_COLUMN);
        return $result;
    }
}

if (!function_exists('listarColunas')) {
    function listarColunas($app, $nomeTabela) {
        $stmt = $app['db']->query("DESCRIBE ".$nomeTabela."");
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }
}

if (!function_exists('detalharTabelaEstrangeira')) {
    function detalharTabelaEstrangeira($app, $nomeTabela, $nomeColunaFk) {

        $sql = "select upper(REFERENCED_TABLE_NAME) tabela,upper(REFERENCED_COLUMN_NAME) coluna from INFORMATION_SCHEMA.KEY_COLUMN_USAGE where CONSTRAINT_SCHEMA='".$app['config']['db_name']."'  and TABLE_NAME='".$nomeTabela."'  AND COLUMN_NAME='".$nomeColunaFk."' and REFERENCED_TABLE_NAME is not null and REFERENCED_COLUMN_NAME is not null";
        $stmt = $app['db']->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if(!isset($result[0])) {
            return '';
        }

        return $result[0];

    }
}

if (!function_exists('criarArquivo')) {
    function criarArquivo($conteudo, $caminhoFisicoDestino) {

        $fp = fopen($caminhoFisicoDestino, "w");
        $escreve = fwrite($fp, $conteudo);
        fclose($fp);
        return true;

    }
}

if (!function_exists('limparDiretorios')) {

    function limparDiretorios($caminhoParaDiretorio)
    {
        $excluidos = array();
        // definindo um array para exibir os erros
        $erros = array();
        // definindo o objeto que faz a iteração do diretório
        $diretorio = new RecursiveDirectoryIterator ($caminhoParaDiretorio);
        // definindo o objeto que fará a iteração recursiva
        $arquivos = new RecursiveIteratorIterator ($diretorio, RecursiveIteratorIterator::CHILD_FIRST);
        // iterando o objeto
        foreach ($arquivos as $arquivo) {
            // verificando permissão, ou seja, se o arquivo pode ser modificado
            if ($arquivo->isWritable()) {
                // verificamos se a iteração atual é de um diretório
                if ($arquivo->isDir()) {
                    // se for, utilizamos rmdir para excluir
                    //rmdir ( $arquivo->getPathname() );
                    // senão, testamos se é um arquivo
                } elseif ($arquivo->isFile() && $arquivo->getFileName() != '.gitignore') {
                    // para arquivos, utilizamos o unlink
                    unlink($arquivo->getPathname());
                    array_push($excluidos, $arquivo->getPathname());
                }
                // caso o arquivo não possa ser modificado, gravamos na variável o nome do arquivo e a permissão do arquivo
            } else {
                $erros [] = 'O arquivo ' . $arquivo->getPathname() . ' tem permissões ' . $arquivo->getPerms() . ' e não pode ser excluído.';
            }
        }
        // caso existam erros, mostramos, ou exibimos mensagem de sucesso.
        if (count($erros)) {
            return implode('<br />', $erros);
        } else {
            return 'Arquivos excluídos com sucesso.<br>'.implode('<br>', $excluidos);
        }
    }
}


