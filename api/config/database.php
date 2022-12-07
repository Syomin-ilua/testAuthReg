<?php 
    class Database {
        // данные о базе данных
        private $host = "localhost";
        private $db_name = "api_db";
        private $username = "root";
        private $password = "root";
        public $conn;

        // соединение с базой данных
        public function getConnection() {
            $this->conn = null;

            try {
                $this->conn = new PDO("mysql:host=" .$this->host. ";dbname=" .$this->db_name, $this->username, $this->password);
            } catch(PDOException $exception) {
                echo "Ошибка соединения: " . $exception->getMessage();
            }

            return $this->conn;
        }
    }
?>