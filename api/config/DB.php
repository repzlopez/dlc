<?php
class DB{
     private $host = 'localhost';
     private $dbas = 'diamondl_';
     private $un   = 'diamondl_dlcph';
     private $pw   = 'dlc0306@)!)';
     private $conn;

     public function connect($db) {
          $this->conn = null;

          try {
               $this->conn = new PDO(
                    'mysql:host='.$this->host.';dbname='.$this->dbas.$db,$this->un,$this->pw, [
                         PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                         PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                         PDO::ATTR_EMULATE_PREPARES => false,
                    ]
               );
          } catch(PDOException $e) { die($e->getMessage()); }

          return $this->conn;
     }
}
?>
