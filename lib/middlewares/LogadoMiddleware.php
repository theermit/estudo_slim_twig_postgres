<?php
namespace lib\middlewares;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class LogadoMiddleware
{
    private $container;
    public function __construct(\Slim\Container $c) 
    {
        $this->container = $c;
    }
    public function __invoke(Request $request, Response $response, callable $next)
    {
        
        $session = $this->container->session;
            
        if(!$session['logado'])
        {
            return $this->container->view->render($response, 'mensagem_app.twig', [
                "mensagem" => "Você não está logado. Faça login para poder entrar na agenda.",
                "nome_rota" => "index"
            ]);
        }

        $response = $next($request, $response);
        return $response;
    }
}
