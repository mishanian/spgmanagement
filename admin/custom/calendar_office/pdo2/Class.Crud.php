<?php
/**
 * Created by PhpStorm.
 * User: Wenyu Gu
 * Date: 2017/6/10
 * Time: 4:33pm
 */

class Crud {
    private $db;
    private $stmt;

    //Constructor to pass the PDO
    public function __construct($DB_con){
        $this->db = $DB_con;
    }

    //---------------------------------Basic functions------------------------------------

    public function query($query){
        $this->stmt = $this->db->prepare($query);
    }

    public function bind($param, $value, $type = null){
        if(is_null($type)){
            switch (true){
                case is_int($type):
                    $type = PDO::PARAM_INT;
                    break;
                case is_bool($type):
                    $type = PDO::PARAM_BOOL;
                    break;
                case is_string($type):
                    $type = PDO::PARAM_STR;
                    break;
                default:
                    $type = PDO::PARAM_STR;
            }
        }
        $this->stmt->bindValue($param, $value, $type);
    }

    public function execute(){
        return $this->stmt->execute();
    }

    public function resultSet(){
        $this->execute();
        return $this->stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function resultSingle(){
        $this->execute();
        return $this->stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function rowCount(){
        return $this->stmt->rowCount();
    }

    public function lastInsertId(){
        return $this->db->lastInsertId();
    }


    //---------------------------------Advanced functions------------------------------------

    public function getAptInfo($id){
        try {
            $this->query("SELECT * FROM apartment_infos WHERE id=:id");
            $this->bind(':id', $id);
            $row = $this->resultSingle();
            return $row;
        }
        catch (PDOException $e){
            echo $e->getMessage();
        }
    }

    public function getBdInfo($id){
        try{
            $this->query("SELECT * FROM building_infos WHERE id=:id");
            $this->bind(':id', $id);
            $row = $this->resultSingle();
            return $row;
        }
        catch (PDOException $e){
            echo $e->getMessage();
        }
    }
}

