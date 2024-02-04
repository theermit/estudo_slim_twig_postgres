<?php 
namespace lib\controllers;

/*
    autor: benhur (benhur.azevedo@hotmail.com)
    utilidade: controller que fornece acesso ao 
    model de usuario alem de oferecer acesso ao
    login do app
*/

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class UsuarioController
{
    private $container;
    public function __construct(\Slim\Container $c) 
    {
        $this->container = $c;
    }
    public function exibirLogin(Request $request, Response $response) : Response
    {
        return $this->container->view->render($response, 'index.twig', []);
    }
    
    public function consultarEmail(Request $request, Response $response) : Response
    {
        $parsedBody = $request->getParsedBody();
        
        try 
        {
            if(!$stmt = $this->dbConn->prepare(
                "select id from usuario where email = :email"
            ))
                throw new \Exception("Falha na query");

            if(!$stmt->execute(["email" => $parsedBody["email"]]))
                throw new \Exception("Falha na query");

            $usuarios = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        }
        catch(\Exception $e)
        {
            return $response->withStatus(500);
        }
        
        $emailJahCadastrado = count($usuarios) > 0 ? "true" : "false";
        
        #$response = $response->withJson(["email_jah_cadastrado" => mb_convert_encoding($emailJahCadastrado, 'UTF-8', mb_list_encodings())]);
        $response->getBody()->write(json_encode(["email_jah_cadastrado" => mb_convert_encoding($emailJahCadastrado, 'UTF-8', mb_list_encodings())], JSON_NUMERIC_CHECK));
        return $response->withStatus(200);
    }
    public function cadastrarUsuario(Request $request, Response $response) : Response
    {
        $parsedBody = $request->getParsedBody();

        try 
        {
            if(!$stmt = $this->container->db->prepare(
                "insert into usuario (nome, email, senha) values (:nome, :email, :senha)"
            ))
                throw new \Exception("Falha na query!");

            if(!$stmt->execute([
                "nome" => $parsedBody["nome"],
                "email" => $parsedBody["email"],
                "senha" => md5($parsedBody["senha"])
            ]))
                throw new \Exception("Falha na query!");
        }
        catch(\Exception $e)
        {
            return $this->container->view->render($response, 'mensagem_app.twig', [
                "mensagem" => "Não foi possível realizar o cadastro do usuário.",
                "nome_rota" => "index"
            ]);
        }

        return $this->container->view->render($response, 'mensagem_app.twig', [
            "mensagem" => "Cadastro de Usuário realizado.",
            "nome_rota" => "index"
        ]);
    }
    public function cadastro(Request $request, Response $response) : Response
    {
        return $this->container->view->render($response, 'cadastro.twig', []);
    }
    public function logar(Request $request, Response $response) : Response
    {
        $parsedBody = $request->getParsedBody();
        
        try 
        {
            if(!$stmt = $this->container->db->prepare(
                "select id from usuario where email = :email and senha = :senha"
            ))
                throw new \Exception("Falha na query");

            if(!$stmt->execute([
                "email" => $parsedBody["email"],
                "senha"=> md5($parsedBody["senha"])
                ]))
                throw new \Exception("Falha na query");

            $usuarios = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        }
        catch(\Exception $e)
        {
            return $this->container->view->render($response, 'mensagem_app.twig', [
                "mensagem" => "Não foi possível realizar a verificação do login.",
                "nome_rota" => "index"
            ]);
        }

        if(count($usuarios) == 0)
        {
            return $this->container->view->render($response, 'index.twig', [
                "email" => $parsedBody["email"],
                "senha" => $parsedBody["senha"],
                "erro" => true
            ]);
        }
            

        $id = $usuarios[0]['id'];

        $session = $this->container->session;
        $session['id_usuario'] = $id;
        $session['logado'] = true;
        
        return $response->withRedirect('/agenda', 301);
    }  
    public function sair(Request $request, Response $response) : Response
    {
        $session = $this->container->session;
        $session['id'] = null;
        $session['logado'] = false;
        

        return $this->container->view->render($response, 'mensagem_app.twig', [
            "mensagem" => "Você saiu do aplicativo. Caso queira acessar novamente, volte à tela de login.",
            "nome_rota" => "index"
        ]);
    }  
}