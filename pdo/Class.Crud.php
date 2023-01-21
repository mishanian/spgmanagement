<?php
#namespace PHPMaker2019\spgmanagement;
#use \PDO;

class Crud
{

    private $db;

    private $stmt;



    //Constructor to pass the PDO

    public function __construct($DB_con)
    {

        $this->db = $DB_con;
    }



    //---------------------------------Basic functions------------------------------------



    public function query($query)
    {
        try {
            $this->stmt = $this->db->prepare($query);
            // echo "tried $query<br><hr>";
        } catch (PDOException $e) {
            echo "<hr>" . $e->getMessage() . " - Sql: " . $this->stmt->queryString . "<hr>";
        }
    }



    public function bind($param, $value, $type = null)
    {

        if (is_null($type)) {

            switch (true) {

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



    public function execute()
    {
        try {
            return $this->stmt->execute();
        } catch (PDOException $e) {
            echo "<hr>" . $e->getMessage() . " - Sql: " . $this->stmt->queryString . "<hr>";
        }
    }



    public function resultSet()
    {
        try {
            $this->execute();
            return $this->stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "<hr>" . $e->getMessage() . " - Sql: " . $this->stmt->queryString . "<hr>";
        }
    }

    public function resultGetName()
    {
        try {
            $this->execute();
            $rows = $this->stmt->fetchAll(PDO::FETCH_ASSOC);
            $result = array();
            foreach ($rows as $row) {
                $result[$row['id']] = $row['name'];
            }
            return $result;
        } catch (PDOException $e) {
            echo "<hr>" . $e->getMessage() . " - Sql: " . $this->stmt->queryString . "<hr>";
        }
    }

    public function resultGetId()
    {
        try {
            $this->execute();
            $rows = $this->stmt->fetchAll(PDO::FETCH_ASSOC);
            $result = array();
            foreach ($rows as $row) {
                $result[$row['name']] = $row['id'];
            }
            return $result;
        } catch (PDOException $e) {
            echo "<hr>" . $e->getMessage() . " - Sql: " . $this->stmt->queryString . "<hr>";
        }
    }
    public function resultSingle()
    {
        try {
            $this->execute();
            return $this->stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "<hr>" . $e->getMessage() . " - Sql: " . $this->stmt->queryString . "<hr>";
        }
    }



    public function resultField()
    {
        try {
            $this->execute();
            return $this->stmt->fetch(PDO::FETCH_NUM)[0];
        } catch (PDOException $e) {
            echo "<hr>" . $e->getMessage() . " - Sql: " . $this->stmt->queryString . "<hr>";
        }
    }

    public function resultArray()
    {
        try {
            $this->execute();
            return $this->stmt->fetchAll(PDO::FETCH_COLUMN, 0);
        } catch (PDOException $e) {
            echo "<hr>" . $e->getMessage() . " - Sql: " . $this->stmt->queryString . "<hr>";
        }
    }

    public function resultJson()
    {
        try {
            $this->execute();
            return json_encode($this->stmt->fetchAll(PDO::FETCH_COLUMN, 0));
        } catch (PDOException $e) {
            echo "<hr>" . $e->getMessage() . " - Sql: " . $this->stmt->queryString . "<hr>";
        }
    }

    public function resultAssoc()
    {
        try {
            $this->execute();
            $rows = $this->stmt->fetchAll(\PDO::FETCH_NUM);
            $arr = array();
            foreach ($rows as $row) {
                ${$row[0]} = $row[1];
                $arr[$row[0]] = $row[1];
            }
            return $arr;
        } catch (PDOException $e) {
            echo "<hr>" . $e->getMessage() . " - Sql: " . $this->stmt->queryString . "<hr>";
        }
    }

    public function resultArrayFields($field1, $field2, $table, $where)
    {
        try {
            if (empty($where)) {
                $where = 'true';
            }
            $arr = [];
            $query = "select $field1, $field2 from  $table where $where";
            query($query);
            $this->execute();
            $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            foreach ($rows as $row) {
                ${$row[$field1]} = $row[$field2];
                $arr[$row[$field1]] = $row[$field2];
            }
            return $arr;
        } catch (PDOException $e) {
            echo "<hr>" . $e->getMessage() . " - Sql: " . $this->stmt->queryString . "<hr>";
        }
    }

    public function rowCount()
    {
        try {
            return $this->stmt->rowCount();
        } catch (PDOException $e) {
            echo "<hr>" . $e->getMessage() . " - Sql: " . $this->stmt->queryString . "<hr>";
        }
    }



    public function lastInsertId()
    {
        try {
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            echo "<hr>" . $e->getMessage() . " - Sql: " . $this->stmt->queryString . "<hr>";
        }
    }




    public function insertAllPost($table, $array, $notInclude, $dateFields)
    {
        try {
            //$lastId=insertAllPost("credit_check", $_POST,["form_submit"],['banking_movingdate']);
            $query = "INSERT INTO " . $table;
            $fis = array();
            $vas = array();
            foreach ($array as $field => $val) {
                if (!in_array($field, $notInclude)) {
                    $fis[] = "`$field`";
                    if (in_array($field, $dateFields) && $val == null) {
                        $vas[] = "'0000-00-00'";
                    } else {
                        $vas[] = "'" . $val . "'";
                    }
                }
            }
            $query .= " (" . implode(", ", $fis) . ") VALUES (" . implode(", ", $vas) . ")";

            query($query);
            if ($stmt->execute())
                return lastInsertId();
            else return false;
        } catch (PDOException $e) {
            echo "<hr>" . $e->getMessage() . " - Sql: " . $this->stmt->queryString . "<hr>";
        }
    }

    public function update($table, $data, $where)
    {
        try {
            $param = [];
            $query = 'UPDATE `' . $table . '` SET ';

            foreach ($data as $key => $value) {
                $query .= '`' . $key . '` = :' . $key . ',';
                $param[":$key"] = $value;
            }
            $query = substr($query, 0, -1);
            if (count($where) > 0) {
                $query .= ' WHERE ';
                foreach ($where as $key => $value) {
                    $query .= '`' . $key . '` = :' . $key . ',';
                    $param[":$key"] = $value;
                }
                $query = substr($query, 0, -1);
            }

            $update = $this->db->prepare($query);
            $update->execute($param);
        } catch (PDOException $e) {
            echo "<hr>" . $e->getMessage() . " - Sql: " . $this->stmt->queryString . "<hr>";
        }
    }
}