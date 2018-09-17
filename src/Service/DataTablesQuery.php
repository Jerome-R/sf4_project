<?php

namespace App\Service;

use App\Service\PdoService;
use App\Service\MailerBuilder;

class DataTablesQuery
{

    /**
    * @var PdoService
    */
    private $pdo;

    /**
    * @var MailBuilder
    */
    private $mail_builder;

    public function __construct(PdoService $pdo, MailBuilder $mail_builder)
    {
        $this->pdo = $pdo->initPdoLocal();
        $this->mail_builder = $mail_builder;
    }

    public function getUsers(array $fields)
    {
        $headers = implode(",", $fields);

        $sql = "SELECT $headers from app_users";

        try {
            $this->pdo->beginTransaction();
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            $this->pdo->commit();

        } catch (\PDOException $e) {
            $this->pdo->rollBack();
            $exception = $e->getMessage();

            throw new \Exception($exception);
            
        }

        $i = 0;
        $results = array();

        while ($user = $stmt->fetch(\PDO::FETCH_ASSOC)) {

            $user[$fields[4]] = new \Datetime($user[$fields[4]]);
            $user[$fields[5]] = new \Datetime($user[$fields[5]]);
            $user[$fields[6]] = new \Datetime($user[$fields[6]]);
            $user[$fields[7]] = preg_replace('/[\["\]]/', '',$user[$fields[7]]);


            $results[$i] = array(
                $user[$fields[0]] ? $user[$fields[0]] : "",
                $user[$fields[1]] ? $user[$fields[1]] : "",
                $user[$fields[2]] ? $user[$fields[2]] : "",
                $user[$fields[3]] ? $user[$fields[3]] : "",
                $user[$fields[4]] ? $user[$fields[4]]->format('Y-m-d H:i:s') : "",
                $user[$fields[5]] ? $user[$fields[5]]->format('Y-m-d H:i:s') : "",
                $user[$fields[6]] ? $user[$fields[6]]->format('Y-m-d H:i:s') : "",
                $user[$fields[7]] ? $user[$fields[7]] : ""
            );

            $i++;
        }

        $stmt->closeCursor();

        return $results;
    }

}
