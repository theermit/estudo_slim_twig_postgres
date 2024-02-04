<?php
namespace lib\middlewares;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class LoginMiddleware
{
    private $container;
    public function __construct(\Slim\Container $c) 
    {
        $this->container = $c;
    }
    public function __invoke(Request $request, Response $response, callable $next)
    {
        $erro = false;

        $parsedBody = $request->getParsedBody();

        if(!array_key_exists("email", $parsedBody))
            $erro = true;

        if(array_key_exists("email", $parsedBody))
        {
            if(!filter_var($parsedBody["email"], FILTER_VALIDATE_EMAIL))
                $erro = true;
        }

        if(!array_key_exists("senha", $parsedBody))
            $erro = true;

        if(array_key_exists("senha", $parsedBody))
        {
            if(preg_match("/^[^*]{8}$/", $parsedBody["senha"]) != 1)
                $erro = true;
        }
            
        if($erro)
        {
            return $this->container->view->render($response, 'index.twig', [
                "email" => $parsedBody["email"],
                "senha" => $parsedBody["senha"],
                "erro" => true
            ]);
        }

        $response = $next($request, $response);
        return $response;
    }
}
