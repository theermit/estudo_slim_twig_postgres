<?php 
namespace lib\controllers;

/*
    autor: benhur (benhur.azevedo@hotmail.com)
    utilidade: controller que fornece acesso ao 
    model de contato
*/

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class ContatoController
{
    private $container;
    public function __construct(\Slim\Container $c) 
    {
        $this->container = $c;
    }
    public function incluir_novo_contato (Request $request, Response $response) : Response
    {
        return $this->container->view->render($response, 'form_contato.twig', [
            "incluir_contato" => true
        ]);
    }
    public function criarContato(Request $request, Response $response) : Response
    {
        $parsedBody = $request->getParsedBody();

        try 
        {
            if(!$stmt = $this->container->db->prepare(
                "insert into contato (nome, telefone, id_usuario) values (:nome, :telefone, :id_usuario)"
            ))
                throw new \Exception("Falha na query!");
            $session = $this->container->session;
            if(!$stmt->execute([
                "nome" => $parsedBody["nome"],
                "telefone" => $parsedBody["telefone"],
                "id_usuario" => $session['id_usuario']
            ]))
                throw new \Exception("Falha na query!");
        }
        catch(\Exception $e)
        {
            return $this->container->view->render($response, 'mensagem_app.twig', [
                "mensagem" => "Não foi possível incluir o contato.",
                "nome_rota" => "agenda"
            ]);
        }

        return $this->container->view->render($response, 'mensagem_app.twig', [
            "mensagem" => "Contato incluído com sucesso.",
            "nome_rota" => "agenda"
        ]);
    }
    public function form_atualizar_contato(Request $request, Response $response, array $args) : Response
    {
        $id = $args['id'];
        $id_usuario = $session = $this->container->session['id_usuario'];

        $contato = $this->getContato($id, $id_usuario);
        if(!$contato)
        {
            return $this->container->view->render($response, 'mensagem_app.twig', [
                "mensagem" => "Contato não encontrado.",
                "nome_rota" => "agenda"
            ]);
        }
        return $this->container->view->render($response, 'form_contato.twig', [
            "alterar_contato" => true,
            "id" => $contato['id'],
            "nome" => $contato['nome'],
            "telefone" => $contato['telefone']
        ]);
    }
    public function atualizarContato(Request $request, Response $response, array $args) : Response
    {
        $parsedBody = $request->getParsedBody();

        try 
        {
            if(!$stmt = $this->container->db->prepare(
                "select * from contato where id = :id"
            ))
                throw new \Exception("Falha na query!");

            if(!$stmt->execute([
                "id" => $parsedBody['id']
            ]))
                throw new \Exception("Falha na query!");
            
            $contato = $stmt->fetch(\PDO::FETCH_ASSOC);
        }
        catch(\Exception $e)
        {
            return $this->container->view->render($response, 'mensagem_app.twig', [
                "mensagem" => "Não foi possível consultar o contato.",
                "nome_rota" => "agenda"
            ]);
        }
        
        if(!$contato)
        {
            return $this->container->view->render($response, 'mensagem_app.twig', [
                "mensagem" => "Contato não encontrado.",
                "nome_rota" => "agenda"
            ]);
        }
        $session = $this->container->session;
        $usuarioId = $session["id_usuario"];
        if($contato['id_usuario'] != $usuarioId)
            return $this->container->view->render($response, 'mensagem_app.twig', [
                "mensagem" => "Contato não encontrado.",
                "nome_rota" => "agenda"
            ]);

        try 
        {
            if(!$stmt = $this->container->db->prepare(
                "update contato set nome = :nome, telefone = :telefone where id = :id"
            ))
                throw new \Exception("Falha na query!");

            if(!$stmt->execute([
                "nome" => $parsedBody["nome"],
                "telefone" => $parsedBody["telefone"],
                "id" => $parsedBody['id']
            ]))
                throw new \Exception("Falha na query!");
        }
        catch(\Exception $e)
        {
            return $this->container->view->render($response, 'mensagem_app.twig', [
                "mensagem" => "Não foi possível atualizar o contato.",
                "nome_rota" => "agenda"
            ]);
        }

        return $this->container->view->render($response, 'mensagem_app.twig', [
            "mensagem" => "Contato atualizado com sucesso.",
            "nome_rota" => "agenda"
        ]);
    }
    public function form_excluir_contato(Request $request, Response $response, array $args) : Response
    {
        $id = $args['id'];
        $id_usuario = $this->container->session['id_usuario'];

        $contato = $this->getContato($id, $id_usuario);
        if(!$contato)
        {
            return $this->container->view->render($response, 'mensagem_app.twig', [
                "mensagem" => "Contato não encontrado.",
                "nome_rota" => "agenda"
            ]);
        }
        return $this->container->view->render($response, 'form_contato.twig', [
            "apagar_contato" => true,
            "id" => $contato['id'],
            "nome" => $contato['nome'],
            "telefone" => $contato['telefone']
        ]);
    }
    public function apagarContato(Request $request, Response $response, array $args) : Response
    {
        $parsedBody = $request->getParsedBody();
        try 
        {
            if(!$stmt = $this->container->db->prepare(
                "select * from contato where id = :id"
            ))
                throw new \Exception("Falha na query!");

            if(!$stmt->execute([
                "id" => $parsedBody['id']
            ]))
                throw new \Exception("Falha na query!");
            
            $contato = $stmt->fetch(\PDO::FETCH_ASSOC);
        }
        catch(\Exception $e)
        {
            return $this->container->view->render($response, 'mensagem_app.twig', [
                "mensagem" => "Não foi possível consultar o contato.",
                "nome_rota" => "agenda"
            ]);
        }
        
        if(!$contato)
        {
            return $this->container->view->render($response, 'mensagem_app.twig', [
                "mensagem" => "Contato não encontrado.",
                "nome_rota" => "agenda"
            ]);
        }
        
        $usuarioId = $this->container->session['id_usuario'];;
        if($contato['id_usuario'] != $usuarioId)
            return $this->container->view->render($response, 'mensagem_app.twig', [
                "mensagem" => "Contato não encontrado.",
                "nome_rota" => "agenda"
            ]);
        
        try 
        {
            if(!$stmt = $this->container->db->prepare(
                "delete from contato where id = :id"
            ))
                throw new \Exception("Falha na query!");

            if(!$stmt->execute([
                "id" => $parsedBody['id']
            ]))
                throw new \Exception("Falha na query!");
        }
        catch(\Exception $e)
        {
            return $this->container->view->render($response, 'mensagem_app.twig', [
                "mensagem" => "Não foi possível excluir o contato.",
                "nome_rota" => "agenda"
            ]);
        }
        
        return $this->container->view->render($response, 'mensagem_app.twig', [
            "mensagem" => "Contato excluído com sucesso.",
            "nome_rota" => "agenda"
        ]);
    }
    private function getContato(int $id, int $id_usuario) : array
    {
        try 
        {
            if(!$stmt = $this->container->db->prepare(
                "select * from contato where id = :id and id_usuario = :id_usuario"
            ))
                throw new \Exception("Falha na query!");

            if(!$stmt->execute([
                "id" => $id,
                "id_usuario" => $id_usuario
            ]))
                throw new \Exception("Falha na query!");
            
            $contato = $stmt->fetch(\PDO::FETCH_ASSOC);
        }
        catch(\Exception $e)
        {
            return [];
        }
        
        return $contato;
    }
    public function agenda(Request $request, Response $response, array $args) : Response
    {
        try 
        {
            if(!$stmt = $this->container->db->prepare(
                #"select * from contato where id_usuario = :id_usuario"
                "select 
                    usuario.nome as nome_usuario, 
                    contato.id, 
                    contato.id_usuario, 
                    contato.nome, 
                    contato.telefone 
                    from 
                        contato RIGHT JOIN usuario 
                        on usuario.id = contato.id_usuario 
                    WHERE 
                        usuario.id = :id_usuario"
            ))
                throw new \Exception("Falha na query!");

            if(!$stmt->execute([
                "id_usuario" => $this->container->session["id_usuario"]
            ]))
                throw new \Exception("Falha na query!");
            
            $resultados = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            $resultado = [];
            $resultado['nome_usuario'] = null;
            $resultado['contatos'] = [];
            if(count($resultados) > 0)
            {
                for($cont = 0; $cont < count($resultados); $cont++)
                {
                    if($cont == 0)
                    {
                        $resultado['nome_usuario'] = $resultados[$cont]['nome_usuario'];
                    }
                    if($resultados[$cont]['id'] != null)
                    {
                        $temp = $resultados[$cont];
                        unset($temp['nome_usuario']);
                        array_push($resultado['contatos'], $temp);
                    }
                }
            }
        }
        catch(\Exception $e)
        {
            return $this->container->view->render($response, 'mensagem_app.twig', [
                "mensagem" => "Não foi possível consultar a agenda.",
                "nome_rota" => "index"
            ]);
        }

        return $this->container->view->render($response, 'agenda.twig', $resultado);
    }
    
    public function listContatosPDF(Request $request, Response $response, array $args) : Response
    {
        try 
        {
            if(!$stmt = $this->dbConn->prepare(
                "select * from usuario where id = :id_usuario"
            ))
                throw new \Exception("Falha na query!");

            if(!$stmt->execute([
                "id_usuario" => $request->getAttribute("id_usuario")
            ]))
                throw new \Exception("Falha na query!");
            
            $usuario = $stmt->fetch(\PDO::FETCH_ASSOC);

            if(!$stmt = $this->dbConn->prepare(
                "select * from contato where id_usuario = :id_usuario"
            ))
                throw new \Exception("Falha na query!");

            if(!$stmt->execute([
                "id" => $request->getAttribute("id_usuario")
            ]))
                throw new \Exception("Falha na query!");
            
            $contato = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        }
        catch(\Exception $e)
        {
            return $response->withStatus(500);
        }
        
        $data = $usuario;
        $data['contatos'] = $contato;
        $pdf = \lib\services\PDFService::geraListaContatosPDF($data);
        $newResponse = $response->withHeader('Content-type', 'application/pdf');
        $newResponse->getBody()->write($pdf->Output());
        return $newResponse;
    }
}
