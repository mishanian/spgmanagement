<?php
/**
 * This class is implemented by singleton pattern, to make sure only one conn instance within an independently memory space.
 * To achieve the db connection repeatedly used.
 * Usage:
 */
include_once ("dbconfig.php");

class Repo{
    # singleton instance
    private static $_repo;

    private $db;
    private $stmt;
    private $unique_id;


    /**
     * This method is to get the singleton instance. only this method is available to get the repo instance.
     */
    public static function getRepoConn(){
        
        if(is_null(self::$_repo)){
            
            self::$_repo = new Repo();
        }
        return self::$_repo;
    }


    /*
    * Function to get an object of a PDO class
    */
    public function getPdoObject($className){
        include_once("Class.Crud.php"); // Include the Crud class
        include_once("Class.$className.php"); // Include the Requested Class
        return new $className($this->db); // Return the Object for the class
    }

    /**
     * Private constructor is to avoid create new object outside the class.
     * The information about DB Connection is managed here.
     */
    private function __construct(){
        global $DB_con;
        // if (strpos($_SERVER['HTTP_HOST'],"localhost")!==false){
        //     $DB_host = "localhost";
        //     $DB_user = "iliveinx_admin";
        //     $DB_pswd = "n=*V=5T!8^1f";
        //     $DB_name = "iliveinx_property";
        // }
        // elseif (strpos($_SERVER['HTTP_HOST'],"beaveraittesting.site")!==false){
        //     $DB_host = "localhost";
        //     $DB_user = "iliveinx_admin";
        //     $DB_pswd = "n=*V=5T!8^1f";
        //     $DB_name = "iliveinx_property";
        // }
        // elseif (strpos($_SERVER['HTTP_HOST'],"ilivein.xyz")!==false){
        //     $DB_host = "localhost";
        //     $DB_user = "iliveinx_admin";
        //     $DB_pswd = "n=*V=5T!8^1f";
        //     $DB_name = "iliveinx_property";
        // }
        // else{
        //     die("Error");
        // }

        // //Setting DSN
        // $dsn = "mysql:host=$DB_host;dbname=$DB_name;";

        // //Setting options
        // $options = array(
        //     PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        //     PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        //     PDO::ATTR_EMULATE_PREPARES   => false
        // );

        // //Creating a new PDO instance
        // try{
        //     $DB_con = new PDO($dsn, $DB_user, $DB_pswd, $options);
        //     $DB_con->query("set names utf8");
        //     $this->db = $DB_con;
        // } catch (PDOException $e){
        //     echo $e->getMessage();
        // }

        $this->db = $DB_con;
        $this->unique_id = uniqid ("db_");
    }


    /**
     * to avoid clone the singleton instance
     */
    public function __clone(){
        trigger_error("Can not clone singleton instance!");
    }

    public function get_repo_id(){
        return $this->unique_id;
    }
}