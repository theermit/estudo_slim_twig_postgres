<?php
namespace lib\config;
class Config 
{
    public static function getConfig() : array
    {
        // gera a configuração com base no arquivo .env
        $dotenv = \Dotenv\Dotenv::createImmutable(__DIR__);
        $dotenv->load();

        $config = [ 'db' => [], 'ambiente_app' => '', 'JWT' => [], 'cache_to_view' => ''];

        $config['db']['SGDB'] = $_ENV['SGDB'];
        switch($_ENV['SGDB'])
        {
            case 'SQLITE':
                $config['db']['DSN'] = "sqlite:" . $_ENV['DSN'];
                break;
            case 'POSTGRESQL':
                $config['db']['DSN'] = "pgsql:" . $_ENV['DSN'];
                break;
        }
        $config['ambiente_app'] = $_ENV['AMBIENTE'];
        $config['base_path'] = $_ENV['BASE_PATH'];
        $config['cache_to_view'] = $_ENV['CACHE_TO_VIEW'] == 'true';

        return $config;
    }
}