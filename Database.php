<?php

class Database{
    public $conn;

    /**
     * Constructor for the database class
     * @param array $config
     */
    public function __construct($config){
        $dsn = "mysql:host={$config['host']};port={$config['port']};dbname={$config['dbname']};charset=utf8";
        
        //to throw exceptions and throw errors
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ];

        try{
            $this->conn = new PDO($dsn, $config['username'], $config['password'], $options);

        }catch(PDOException $e){
            throw new Exception("Database Connection failed: {$e->getMessage()}");
        }
    }


}

?>