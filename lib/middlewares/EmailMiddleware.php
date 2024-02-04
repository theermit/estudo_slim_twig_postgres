<?php
namespace lib\middlewares;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class EmailMiddleware
{
    public function __construct() 
    {
        $this->dbConn = \lib\services\DbService::DbConnFactory();
    }
    public function __invoke(Request $request, Response $response, callable $next)
    {
        $ok = true;

        $parsedBody = $request->getParsedBody();
        
        if(!array_key_exists("email", $parsedBody))
            $ok = false;

        try 
        {
            if(!$stmt = $this->dbConn->prepare(
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
            return $response->withStatus(500);
        }

        if(!$usuario)
            $ok = true;
        
        if(!$ok)
        {
            return $response->withStatus(400);
        }
        $response = $next($request, $response);
        return $response;
    }
}