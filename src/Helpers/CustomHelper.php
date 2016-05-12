<?php

if (!function_exists('listarObjTabelas')) {
    function listarObjTabelas($app) {

        $nomesDasTabelas = listarTabelas($app);
        $tabelas = [];
        
        foreach ($nomesDasTabelas as $nomeDaTabela) {

            $tabela = new \MotaMonteiro\Gerador\Entities\Tabela($nomeDaTabela, $app['config']['table_prefix']);
            $nomesDasColunas = listarColunas($app, $nomeDaTabela);

            foreach ($nomesDasColunas as $nomeDaColuna) {
                
                $nulo = ($nomeDaColuna['Null'] == 'NO') ? false : true;
                $auto_increment = ($nomeDaColuna['Extra'] == 'auto_increment') ? true : false;
                $coluna = new \MotaMonteiro\Gerador\Entities\Coluna($nomeDaColuna['Field'], $nomeDaColuna['Type'], $nulo, $nomeDaColuna['Key'], $auto_increment);

                if ($coluna->getChave() == 'MUL') {

                    $nomeDaTabelaEstrangeira = detalharTabelaEstrangeira($app, $nomeDaTabela, $coluna->getCampo());

                    if ($nomeDaTabelaEstrangeira == '') {
                        $coluna->setChave('');
                    } else {
                        $nomesDasColunasEstrangeiras = listarColunas($app, $nomeDaTabelaEstrangeira['tabela']);
                        $tabelaEstrangeira = new \MotaMonteiro\Gerador\Entities\Tabela($nomeDaTabelaEstrangeira['tabela'], $app['config']['table_prefix']);

                        foreach ($nomesDasColunasEstrangeiras as $nomeDaColunaEstrangeira) {
                            $nulo = ($nomeDaColunaEstrangeira['Null'] == 'NO') ? false : true;
                            $auto_increment = ($nomeDaColunaEstrangeira['Extra'] == 'auto_increment') ? true : false;
                            $colunaEstrangeira = new \MotaMonteiro\Gerador\Entities\Coluna($nomeDaColunaEstrangeira['Field'], $nomeDaColunaEstrangeira['Type'], $nulo, $nomeDaColunaEstrangeira['Key'], $auto_increment);
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


