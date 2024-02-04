<?php
namespace lib\middlewares;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class CadastroMiddleware
{
    private $container;
    public function __construct(\Slim\Container $c) 
    {
        $this->container = $c;
    }
    public function __invoke(Request $request, Response $response, callable $next)
    {
        $ok = true;

        $parsedBody = $request->getParsedBody();
        
        if(!array_key_exists("email", $parsedBody))
        {
            return $this->container->view->render($response, 'mensagem_app.twig', [
                "mensagem" => "Não foi informado o email. Impossível cadastrar usuário.",
                "nome_rota" => "cadastro"
            ]);
        }

        try 
        {
            if(!$stmt = $this->container->db->prepare(
                "select * from usuario where email = :email"
            ))
                throw new \Exception("Falha na query!");

            if(!$stmt->execute([
                "email" => $parsedBody["email"]
            ]))
                throw new \Exception("Falha na query!");
            
            $usuario = $stmt->fetch(\PDO::FETCH_ASSOC);
        }
        catch(\Exception $e)
        {
            return $this->container->view->render($response, 'mensagem_app.twig', [
                "mensagem" => "Não possível validar o email.",
                "nome_rota" => "cadastro"
            ]);
        }

        if(!$usuario)
        {
            $email_ja_usado = false;
            $email_disponivel = true;
        }
        else 
        {
            $email_ja_usado = true;
            $email_disponivel = false;
            $ok = false;
        }
        
        if(!array_key_exists("nome", $parsedBody))
        {
            return $this->container->view->render($response, 'mensagem_app.twig', [
                "mensagem" => "Não foi informado o nome. Impossível cadastrar usuário.",
                "nome_rota" => "cadastro"
            ]);
        }

        if(!array_key_exists("senha", $parsedBody))
        {
            return $this->container->view->render($response, 'mensagem_app.twig', [
                "mensagem" => "Não foi informado a senha. Impossível cadastrar usuário.",
                "nome_rota" => "cadastro"
            ]);
        }

        if(!array_key_exists("confirmar_senha", $parsedBody))
        {
            return $this->container->view->render($response, 'mensagem_app.twig', [
                "mensagem" => "Não foi informado a senha. Impossível cadastrar usuário.",
                "nome_rota" => "cadastro"
            ]);
        }

        if(array_key_exists("senha", $parsedBody) && array_key_exists("confirmar_senha", $parsedBody))
        {
            if(strlen($parsedBody["senha"]) != 8 || strlen($parsedBody["confirmar_senha"]) != 8)
            {
                return $this->container->view->render($response, 'mensagem_app.twig', [
                    "mensagem" => "Não foi informado a senha. Impossível cadastrar usuário.",
                    "nome_rota" => "cadastro"
                ]);
            }
            if($parsedBody["senha"] != $parsedBody["confirmar_senha"])
            {
                $conf_senha_falha = true;
                $conf_senha_ok = false;
                $ok = false;
            }
            else 
            {
                $conf_senha_falha = false;
                $conf_senha_ok = true;
            }
        }

        if(!$ok)
        {
            return $this->container->view->render($response, 'cadastro.twig', [
                "email_ja_usado" => $email_ja_usado,
                "email_disponivel" => $email_disponivel,
                "conf_senha_falha" => $conf_senha_falha,
                "conf_senha_ok" => $conf_senha_ok,
                "email" => $parsedBody["email"],
                "nome" => $parsedBody["nome"],
                "senha" => $parsedBody["senha"],
                "confirmar_senha" => $parsedBody["confirmar_senha"]
            ]);
        }
        $response = $next($request, $response);
        return $response;
    }
}