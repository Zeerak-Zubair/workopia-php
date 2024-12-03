<?php

namespace Framework;

use PDO;
use PDOException;
use Exception;

class Database{
    public $conn;

    /**
     * Constructor for the database class
     * @param array $config
     */
    public function __construct($config){
        $dsn = "mysql:host={$config['host']};port={$config['port']};dbname={$config['dbname']};charset=utf8";
        
        //to throw exceptions and throw errors
        //vary between `PDO::FETCH_ASSOC` & ``
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ
        ];

        try{
            $this->conn = new PDO($dsn, $config['username'], $config['password'], $options);

        }catch(PDOException $e){
            throw new Exception("Database Connection failed: {$e->getMessage()}");
        }
    }

    //inspectAndDie($uri);// `http:localhost:8000/listing?id=2` -> string(8) "/listing"

    /*
     * Query the Database
     * @param string $query
     * 
     * @return PDOStatement
     * @throws PDOException
     */
    public function query($query, $params = []){
        try{
            $sth = $this->conn->prepare($query);

            //bind named params
            foreach($params as $param => $value){
                $sth->bindValue(':'.$param, $value);
            }

            $sth->execute();
            return $sth;
        }catch(PDOException $e){
            throw new Exception("Query failed to execue: {$e->getMessage()}");
        }
    }

}

?>