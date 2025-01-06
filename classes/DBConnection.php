<?php
if(!defined('DB_SERVER')){
    require_once("../initialize.php");
}

class DBConnection {

    private $host = DB_SERVER;
    private $username = DB_USERNAME;
    private $password = DB_PASSWORD;
    private $database = DB_NAME;
    
    public $conn;

    public function __construct() {
        if (!isset($this->conn)) {
            // Tạo kết nối
            $this->conn = new mysqli($this->host, $this->username, $this->password, $this->database);

            // Kiểm tra kết nối
            if ($this->conn->connect_error) {
                die('Cannot connect to database server: ' . $this->conn->connect_error);
            }

            // Thiết lập mã hóa UTF-8 cho kết nối
            $this->conn->set_charset("utf8mb4");
        }
    }

    public function __destruct() {
        // Đóng kết nối khi đối tượng bị hủy
        if (isset($this->conn)) {
            $this->conn->close();
        }
    }
}
?>
