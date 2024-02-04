<?php 
namespace lib\container;

/*
    autor: benhur (benhur.azevedo@hotmail.com)
    utilidade: seta o container de injeção
    de dependência
*/
/* use lib\controllers\UsuarioController;
use lib\controllers\ContatoController;
use lib\middlewares\ContatoMiddleware; */
class Container 
{
    public static function setContainer(\Slim\App $app): void
    {
        // Get container
        $container = $app->getContainer();

        // Register component on container
        $container['view'] = function ($container) 
        {
            $config = \lib\config\Config::getConfig();
            define("PATH_TO_TEMPLATES", "views");

            $cache = $config['cache_to_view'] ? 'cache' : false;
            $view = new \Slim\Views\Twig(PATH_TO_TEMPLATES, [
                'cache' => $cache
            ]);

            // Instantiate and add Slim specific extension
            $router = $container->get('router');
            $uri = \Slim\Http\Uri::createFromEnvironment(new \Slim\Http\Environment($_SERVER));
            $view->addExtension(new \Slim\Views\TwigExtension($router, $uri));

            return $view;
        };

        $container['db'] = function ($container) 
        {

            return \lib\services\DbService::DbConnFactory();
        };

        $container['fpdf'] = function ($container) 
        {

            return new \lib\services\PDFService();
        };

        $handler = new \lib\config\SysSession($container['db']);
        session_set_save_handler($handler, true);

        $app->add(new \Slim\Middleware\Session([
                'name' => 'dummy_session',
                'autorefresh' => true,
                'lifetime' => '1 hour',
            ]));
        
        $container['session'] = function ($c) 
        {
            return new \SlimSession\Helper();
        };

        $container[\lib\middlewares\LoginMiddleware::class] = function ($c) 
        {
            return new \lib\middlewares\LoginMiddleware($c);
        };

        $container[\lib\middlewares\LogadoMiddleware::class] = function ($c) 
        {
            return new \lib\middlewares\LogadoMiddleware($c);
        };

        
        $container[\lib\middlewares\ContatoMiddleware::class] = function ($c) 
        {
            return new \lib\middlewares\ContatoMiddleware($c);
        };


        $container[\lib\controllers\UsuarioController::class] = function ($c) 
        {
            return new \lib\controllers\UsuarioController($c);
        };

        $container[\lib\controllers\ContatoController::class] = function ($c) 
        {
            return new \lib\controllers\ContatoController($c);
        };

        
        
        

        return;
    }
}