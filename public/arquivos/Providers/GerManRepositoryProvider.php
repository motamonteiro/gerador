<?php

namespace GerMan\Providers;

use Illuminate\Support\ServiceProvider;


/**
 * Class GerManRepositoryProvider
 * @package namespace GerMan\Providers;
 */
class GerManRepositoryProvider extends ServiceProvider
{

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
            $this->app->bind(
            \GerMan\Repositories\Interfaces\AnexoEmpreendimentoInterface::class,
            \GerMan\Repositories\Eloquent\AnexoEmpreendimentoRepository::class
        );

        $this->app->bind(
            \GerMan\Repositories\Interfaces\AnexoManutencaoInterface::class,
            \GerMan\Repositories\Eloquent\AnexoManutencaoRepository::class
        );

        $this->app->bind(
            \GerMan\Repositories\Interfaces\AnexoReformaInterface::class,
            \GerMan\Repositories\Eloquent\AnexoReformaRepository::class
        );

        $this->app->bind(
            \GerMan\Repositories\Interfaces\AnexoInterface::class,
            \GerMan\Repositories\Eloquent\AnexoRepository::class
        );

        $this->app->bind(
            \GerMan\Repositories\Interfaces\AtividadeInterface::class,
            \GerMan\Repositories\Eloquent\AtividadeRepository::class
        );

        $this->app->bind(
            \GerMan\Repositories\Interfaces\EmpreendimentoPessoaInterface::class,
            \GerMan\Repositories\Eloquent\EmpreendimentoPessoaRepository::class
        );

        $this->app->bind(
            \GerMan\Repositories\Interfaces\EmpreendimentoInterface::class,
            \GerMan\Repositories\Eloquent\EmpreendimentoRepository::class
        );

        $this->app->bind(
            \GerMan\Repositories\Interfaces\EnderecoInterface::class,
            \GerMan\Repositories\Eloquent\EnderecoRepository::class
        );

        $this->app->bind(
            \GerMan\Repositories\Interfaces\GarantiaInterface::class,
            \GerMan\Repositories\Eloquent\GarantiaRepository::class
        );

        $this->app->bind(
            \GerMan\Repositories\Interfaces\JobInterface::class,
            \GerMan\Repositories\Eloquent\JobRepository::class
        );

        $this->app->bind(
            \GerMan\Repositories\Interfaces\ManualInterface::class,
            \GerMan\Repositories\Eloquent\ManualRepository::class
        );

        $this->app->bind(
            \GerMan\Repositories\Interfaces\ManutencaoInterface::class,
            \GerMan\Repositories\Eloquent\ManutencaoRepository::class
        );

        $this->app->bind(
            \GerMan\Repositories\Interfaces\MigrationInterface::class,
            \GerMan\Repositories\Eloquent\MigrationRepository::class
        );

        $this->app->bind(
            \GerMan\Repositories\Interfaces\OauthAccesTokenScopeInterface::class,
            \GerMan\Repositories\Eloquent\OauthAccesTokenScopeRepository::class
        );

        $this->app->bind(
            \GerMan\Repositories\Interfaces\OauthAccesTokenInterface::class,
            \GerMan\Repositories\Eloquent\OauthAccesTokenRepository::class
        );

        $this->app->bind(
            \GerMan\Repositories\Interfaces\OauthAuthCodeScopeInterface::class,
            \GerMan\Repositories\Eloquent\OauthAuthCodeScopeRepository::class
        );

        $this->app->bind(
            \GerMan\Repositories\Interfaces\OauthAuthCodeInterface::class,
            \GerMan\Repositories\Eloquent\OauthAuthCodeRepository::class
        );

        $this->app->bind(
            \GerMan\Repositories\Interfaces\OauthClientEndpointInterface::class,
            \GerMan\Repositories\Eloquent\OauthClientEndpointRepository::class
        );

        $this->app->bind(
            \GerMan\Repositories\Interfaces\OauthClientGrantInterface::class,
            \GerMan\Repositories\Eloquent\OauthClientGrantRepository::class
        );

        $this->app->bind(
            \GerMan\Repositories\Interfaces\OauthClientScopeInterface::class,
            \GerMan\Repositories\Eloquent\OauthClientScopeRepository::class
        );

        $this->app->bind(
            \GerMan\Repositories\Interfaces\OauthClientInterface::class,
            \GerMan\Repositories\Eloquent\OauthClientRepository::class
        );

        $this->app->bind(
            \GerMan\Repositories\Interfaces\OauthGrantScopeInterface::class,
            \GerMan\Repositories\Eloquent\OauthGrantScopeRepository::class
        );

        $this->app->bind(
            \GerMan\Repositories\Interfaces\OauthGrantInterface::class,
            \GerMan\Repositories\Eloquent\OauthGrantRepository::class
        );

        $this->app->bind(
            \GerMan\Repositories\Interfaces\OauthRefreshTokenInterface::class,
            \GerMan\Repositories\Eloquent\OauthRefreshTokenRepository::class
        );

        $this->app->bind(
            \GerMan\Repositories\Interfaces\OauthScopeInterface::class,
            \GerMan\Repositories\Eloquent\OauthScopeRepository::class
        );

        $this->app->bind(
            \GerMan\Repositories\Interfaces\OauthSessionScopeInterface::class,
            \GerMan\Repositories\Eloquent\OauthSessionScopeRepository::class
        );

        $this->app->bind(
            \GerMan\Repositories\Interfaces\OauthSessionInterface::class,
            \GerMan\Repositories\Eloquent\OauthSessionRepository::class
        );

        $this->app->bind(
            \GerMan\Repositories\Interfaces\PasswordResetInterface::class,
            \GerMan\Repositories\Eloquent\PasswordResetRepository::class
        );

        $this->app->bind(
            \GerMan\Repositories\Interfaces\PessoaInterface::class,
            \GerMan\Repositories\Eloquent\PessoaRepository::class
        );

        $this->app->bind(
            \GerMan\Repositories\Interfaces\ReformaInterface::class,
            \GerMan\Repositories\Eloquent\ReformaRepository::class
        );

        $this->app->bind(
            \GerMan\Repositories\Interfaces\SistemaInterface::class,
            \GerMan\Repositories\Eloquent\SistemaRepository::class
        );

        $this->app->bind(
            \GerMan\Repositories\Interfaces\SubtopicoInterface::class,
            \GerMan\Repositories\Eloquent\SubtopicoRepository::class
        );

        $this->app->bind(
            \GerMan\Repositories\Interfaces\TelefoneInterface::class,
            \GerMan\Repositories\Eloquent\TelefoneRepository::class
        );

        $this->app->bind(
            \GerMan\Repositories\Interfaces\TipoAnexoAtividadeInterface::class,
            \GerMan\Repositories\Eloquent\TipoAnexoAtividadeRepository::class
        );

        $this->app->bind(
            \GerMan\Repositories\Interfaces\TipoAnexoSubtopicoInterface::class,
            \GerMan\Repositories\Eloquent\TipoAnexoSubtopicoRepository::class
        );

        $this->app->bind(
            \GerMan\Repositories\Interfaces\TipoAnexoInterface::class,
            \GerMan\Repositories\Eloquent\TipoAnexoRepository::class
        );

        $this->app->bind(
            \GerMan\Repositories\Interfaces\TipoAreaInterface::class,
            \GerMan\Repositories\Eloquent\TipoAreaRepository::class
        );

        $this->app->bind(
            \GerMan\Repositories\Interfaces\TipoResponsavelInterface::class,
            \GerMan\Repositories\Eloquent\TipoResponsavelRepository::class
        );

        $this->app->bind(
            \GerMan\Repositories\Interfaces\TipoUnidadeInterface::class,
            \GerMan\Repositories\Eloquent\TipoUnidadeRepository::class
        );

        $this->app->bind(
            \GerMan\Repositories\Interfaces\TopicoInterface::class,
            \GerMan\Repositories\Eloquent\TopicoRepository::class
        );

        $this->app->bind(
            \GerMan\Repositories\Interfaces\UserInterface::class,
            \GerMan\Repositories\Eloquent\UserRepository::class
        );

        $this->app->bind(
            \GerMan\Repositories\Interfaces\UsuarioInterface::class,
            \GerMan\Repositories\Eloquent\UsuarioRepository::class
        );


    }
}


