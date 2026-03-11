<?php
namespace App\Core;

/**
 * Database Class
 * Singleton pattern untuk koneksi database dengan PDO
 * Menerapkan dependency injection dan error handling
 */
class Database {
    private static $instance = null;
    private $host = DB_HOST;
    private $user = DB_USER;
    private $pass = DB_PASS;
    private $dbName = DB_NAME;
    
    private $dbh; // Database handler
    private $stmt; // Statement
    
    /**
     * Constructor - Private untuk implement Singleton Pattern
     * Melakukan koneksi ke database dengan PDO
     */
    private function __construct() {
        $dsn = 'mysql:host=' . $this->host . ';dbname=' . $this->dbName;
        
        $options = [
            \PDO::ATTR_PERSISTENT => true,
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC
        ];
        
        try {
            $this->dbh = new \PDO($dsn, $this->user, $this->pass, $options);
        } catch(\PDOException $e) {
            die("Database Connection Error: " . $e->getMessage());
        }
    }
    
    /**
     * Get Database Instance (Singleton Pattern)
     * Memastikan hanya ada satu instance dari Database
     * 
     * @return Database
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Prepare SQL Query
     * Menggunakan prepared statement untuk mencegah SQL Injection
     * 
     * @param string $query SQL query string
     * @return void
     */
    public function query($query) {
        $this->stmt = $this->dbh->prepare($query);
    }
    
    /**
     * Bind Parameter to Query
     * Menggunakan type detection otomatis untuk binding
     * 
     * @param string $param Parameter name
     * @param mixed $value Parameter value
     * @param int|null $type PDO parameter type
     * @return void
     */
    public function bind($param, $value, $type = null) {
        // Type detection jika tidak dispesifikasi (control structure: if-else)
        if (is_null($type)) {
            switch (true) {
                case is_int($value):
                    $type = \PDO::PARAM_INT;
                    break;
                case is_bool($value):
                    $type = \PDO::PARAM_BOOL;
                    break;
                case is_null($value):
                    $type = \PDO::PARAM_NULL;
                    break;
                default:
                    $type = \PDO::PARAM_STR;
            }
        }
        
        $this->stmt->bindValue($param, $value, $type);
    }
    
    /**
     * Execute Prepared Statement
     * 
     * @return bool Execution status
     */
    public function execute() {
        return $this->stmt->execute();
    }
    
    /**
     * Get Result Set as Array
     * 
     * @return array Result set
     */
    public function resultSet() {
        $this->execute();
        return $this->stmt->fetchAll();
    }
    
    /**
     * Get Single Result
     * 
     * @return array|false Single row result
     */
    public function single() {
        $this->execute();
        return $this->stmt->fetch();
    }
    
    /**
     * Get Row Count
     * 
     * @return int Number of affected rows
     */
    public function rowCount() {
        return $this->stmt->rowCount();
    }
    
    /**
     * Get Last Insert ID
     * 
     * @return string Last insert ID
     */
    public function lastInsertId() {
        return $this->dbh->lastInsertId();
    }
    
    /**
     * Begin Transaction
     */
    public function beginTransaction() {
        return $this->dbh->beginTransaction();
    }
    
    /**
     * Commit Transaction
     */
    public function commit() {
        return $this->dbh->commit();
    }
    
    /**
     * Rollback Transaction
     */
    public function rollback() {
        return $this->dbh->rollBack();
    }
}
