<?php
namespace lib\middlewares;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class ContatoMiddleware
{
    private $container;
    public function __construct(\Slim\Container $c) 
    {
        $this->container = $c;
    }
    public function __invoke(Request $request, Response $response, callable $next)
    {
        $ok = true;
        $erro_nome = false;
        $erro_telefone = false;
        $parsedBody = $request->getParsedBody();

        if(!array_key_exists("nome", $parsedBody))
        {
            $ok = false;
            $erro_nome = true;
        }
        

        if(array_key_exists("nome", $parsedBody))
        {
            if($parsedBody["nome"] == "")
            {
                $ok = false;
                $erro_nome = true;
            }
                
        }

        if(!array_key_exists("telefone", $parsedBody))
        {
            $ok = false;
            $erro_telefone = true;
        }
            

        if(array_key_exists("telefone", $parsedBody))
        {
            if($parsedBody["telefone"] == "")
            {
                $ok = false;
                $erro_telefone = true;
            }
                
        }
            
        if(!$ok)
        {
            return $this->container->view->render($response, 'form_contato.twig', [
                "id" => isset($parsedBody['id']) ? $parsedBody['id'] : null,
                "incluir_contato" => !isset($parsedBody['id']),
                "alterar_contato" => isset($parsedBody['id']),
                "nome" => isset($parsedBody['nome']) ? $parsedBody['nome'] : '',
                "telefone" => isset($parsedBody['telefone']) ? $parsedBody['telefone'] : '',
                "erro_nome" => $erro_nome,
                "erro_telefone" => $erro_telefone
            ]);
        }

        $response = $next($request, $response);
        return $response;
    }
}