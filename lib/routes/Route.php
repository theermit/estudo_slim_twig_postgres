<?php 
namespace lib\routes;

/*
    autor: benhur (benhur.azevedo@hotmail.com)
    utilidade: seta as rotas da aplicação slim
*/
use lib\controllers\UsuarioController;
use lib\controllers\ContatoController;
use lib\middlewares\ContatoMiddleware;
use lib\controllers\TesteController;
class Route 
{
    public static function setRoutes(\Slim\App $app): void
    {
        // self::setUserRoutes($app);
        // self::setContatoRoutes($app);
        $app->get("/index", UsuarioController::class . ":exibirLogin")
        ->setName("index");

        $app->post("/logar", UsuarioController::class . ":logar")
        ->add(\lib\middlewares\LoginMiddleware::class)
        ->setName("logar");
        
        $app->get("/cadastro", UsuarioController::class . ":cadastro")
        ->setName("cadastro");

        $app->post("/cadastrar", UsuarioController::class . ":cadastrarUsuario")
        ->add(\lib\middlewares\CadastroMiddleware::class)
        ->setName("cadastrar");

        $app->group('', function(\Slim\App $app)
        {
            $app->get("/agenda", ContatoController::class . ":agenda")
            ->setName("agenda");

            $app->get("/sair", UsuarioController::class . ":sair")
            ->setName("sair");

            $app->get('/incluir_novo_contato', ContatoController::class . ":incluir_novo_contato")
            ->setName("incluir_novo_contato");

            $app->post('/criarContato', ContatoController::class . ":criarContato")
            ->add(\lib\middlewares\ContatoMiddleware::class)
            ->setName("criarContato");

            $app->get("/form_atualizar_contato/{id}", ContatoController::class . ":form_atualizar_contato")
            ->setName('form_atualizar_contato');

            $app->post("/atualizar_contato", ContatoController::class . ":atualizarContato")
            ->add(\lib\middlewares\ContatoMiddleware::class)
            ->setName('atualizarContato');

            $app->get("/form_excluir_contato/{id}", ContatoController::class . ":form_excluir_contato")
            ->setName('form_excluir_contato');
            
            $app->post("/excluir_contato", ContatoController::class . ":apagarContato")
            ->setName('apagarContato');
            
        })  
        ->add(\lib\middlewares\LogadoMiddleware::class);

        return;
    }
    private static function setUserRoutes(\Slim\App $app): void
    {
        #rota para consultar se o email (que é campo unique) já existe
        $app->post("/consultaremail", UsuarioController::class . ":consultarEmail");

        #rota para cadastrar o usuário
        $app->post("/cadastrarusuario", UsuarioController::class .":cadastrarUsuario")
            ->add(new \lib\middlewares\CadUsuarioMiddleware())
            ->add(new \lib\middlewares\EmailMiddleware());

        #rota para logar
        $app->post("/login", UsuarioController::class .":logar")
            ->add(new \lib\middlewares\LoginMiddleware());
    }
    private static function setContatoRoutes(\Slim\App $app): void
    {
        $app->group("", function (\Slim\App $app) 
        { 
            $app->post("/contato", ContatoController::class .":criarContato")
                ->add(new ContatoMiddleware());

            $app->put("/contato/{id}", ContatoController::class . ":atualizarContato")
                ->add(new ContatoMiddleware());

            $app->delete("/contato/{id}", ContatoController::class . ":apagarContato");

            $app->get("/contato/{id}", ContatoController::class . ":getContato");

            $app->get("/contato", ContatoController::class . ":listContatos");
        })->add(new \lib\middlewares\JWTMiddleware());
    }
}