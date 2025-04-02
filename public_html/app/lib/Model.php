<?php

namespace app\lib;

use PDO;

class Model extends Config {

    private $con;

    public function __construct() {
        try {
            $this->con = new PDO("mysql:host=".self::DBHOST.";dbname=".self::DBNAME, self::DBUSER, self::DBPASS);
            $this->con->exec("set names utf8");
        } catch (\PDOException $e) {
            define('APP_ERROR', 'Erro na conexão com o banco de dados: <br><pre>'.$e->getMessage()."</pre>");
            include("./app/content/site/layouts/error.phtml");
            exit();
        }
    }

    public function set($host, $user, $pass, $db) {
        try {
            $this->con = new PDO("mysql:host=".$host.";dbname=".$db, $user, $pass);
            $this->con->exec("set names utf8");
        } catch (\PDOException $e) {
            define('APP_ERROR', 'Erro na conexão com o banco de dados: <br><pre>'.$e->getMessage()."</pre>");
            include("./app/content/site/layouts/error.phtml");
            exit();
        }
    }

    public function query($sql)
    {
        $stmt = $this->con->prepare($sql);
        if(!$stmt->execute())
        {
            $erros = "";
            foreach ($stmt->errorInfo() as $error)
            {
                $erros .= $error." - ";
            }
            define('APP_ERROR', 'Erro na conexão com o banco de dados: <br><pre>'.$erros."</pre>");
            include("./app/content/site/layouts/error.phtml");
            exit();
        }
    }

    public function select($sql, $select = 'single')
    {
        try {
            $state = $this->con->prepare($sql);
            $state->execute();

            if($select == 'single')
            {
                return $state->fetchObject();
            }
            return $state->fetchAll(PDO::FETCH_OBJ);
        }catch (\PDOException $e)
        {
            define('APP_ERROR', 'Erro na conexão com o banco de dados: <br><pre>'.$e->getMessage()."</pre>");
            include("./app/content/site/layouts/error.phtml");
            exit();
        }
    }

    public function count($table, $where)
    {
        try {
            $state = $this->con->prepare("SELECT * FROM {$table} WHERE {$where}");
            $state->execute();

            return $state->rowCount();
        }catch (\PDOException $e)
        {
            define('APP_ERROR', 'Erro na conexão com o banco de dados: <br><pre>'.$e->getMessage()."</pre>");
            include("./app/content/site/layouts/error.phtml");
            exit();
        }
    }

    public function countTable($table)
    {
        try {
            $state = $this->con->prepare("SELECT * FROM {$table}");
            $state->execute();

            return $state->rowCount();
        }catch (\PDOException $e)
        {
            define('APP_ERROR', 'Erro na conexão com o banco de dados: <br><pre>'.$e->getMessage()."</pre>");
            include("./app/content/site/layouts/error.phtml");
            exit();
        }
    }

    public function insert($obj, $table)
    {
        try {
            $sql = "INSERT INTO `{$table}` (".implode(",", array_keys((array) $obj)).") VALUES('".implode("','", array_values((array) $obj))."');";

            $state = $this->con->prepare($sql);
            $state->execute();
        }catch (\PDOException $e)
        {
            define('APP_ERROR', 'Erro na conexão com o banco de dados: <br><pre>'.$e->getMessage()."</pre>");
            include("./app/content/site/layouts/error.phtml");
            exit();
        }
        return [ 'success' => 'true', 'feedback'=>'', 'id'=>$this->last($table) ];
    }

    public function update($obj, $condition, $table)
    {
        try {
            $data = [];
            $where = [];

            foreach ($obj as $ind => $val)
            {
                $data[] = "`{$ind}` = ".(is_null($val) ? "NULL" : "'{$val}'");
            }
            foreach ($condition as $ind => $val)
            {
                $where[] = "`{$ind}` ".(is_null($val) ? "IS NULL" : " = '{$val}'");
            }

            $sql = "UPDATE `{$table}` SET ".implode(',', $data)." WHERE ".implode(' AND', $where);

            $state = $this->con->prepare($sql);
            $state->execute(array('widgets'));
        }catch (\PDOException $e)
        {
            define('APP_ERROR', 'Erro na conexão com o banco de dados: <br><pre>'.$e->getMessage()."</pre>");
            include("./app/content/site/layouts/error.phtml");
            exit();
        }

        return [ 'success'=>true, 'feedback'=>'' ];
    }

    public function delete($condition, $table)
    {
        try {
            $where = [];

            foreach ($condition as $ind => $val)
            {
                $where[] = "`{$ind}` ".(is_null($val) ? "IS NULL" : " = '{$val}'");
            }

            $sql = "DELETE FROM {$table} WHERE ".implode(' AND', $where);

            $state = $this->con->prepare($sql);
            $state->execute(array('widgets'));
        }catch (\PDOException $e)
        {
            define('APP_ERROR', 'Erro na conexão com o banco de dados: <br><pre>'.$e->getMessage()."</pre>");
            include("./app/content/site/layouts/error.phtml");
            exit();
        }
    }

    public function last($table)
    {
        try {
            $state = $this->con->prepare("SELECT last_insert_id() as last FROM `{$table}`");
            $state->execute([$table]);
            $state = $state->fetchObject();
        }catch (\PDOException $e)
        {
            define('APP_ERROR', 'Erro na conexão com o banco de dados: <br><pre>'.$e->getMessage()."</pre>");
            include("./app/content/site/layouts/error.phtml");
            exit();
        }
        return $state->last;
    }

    public function closeConnection() {
        $this->con = null;
    }

}