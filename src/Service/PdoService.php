<?php

namespace App\Service;

class PdoService
{
    private $db_host;
    private $db_name;
    private $db_user;
    private $db_password;

    public function __construct($db_host, $db_name, $db_user, $db_password)
    {
        $this->db_host = $db_host;
        $this->db_name = $db_name;
        $this->db_user = $db_user;
        $this->db_password = $db_password;
    }

    public function initPdoLocal()
    {
        try
        {
            $pdo = new \PDO('pgsql:host='.$this->db_host.';dbname='.$this->db_name, $this->db_user, $this->db_password);
            #$pdo = new \PDO('mysql:host='.$this->db_host.';dbname='.$this->db_name.';charset=utf8', $this->db_user, $this->db_password);
        }
        catch(Exception $e)
        {       
            $output->writeln($e->getMessage());
            die('Erreur : '.$e->getMessage());
        }
        
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        return $pdo;
    }
}
