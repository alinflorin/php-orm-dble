<?php

class MySQLDBLE extends DBLE{
    private $con;
    private $hostname,$port,$user,$pass,$db;
    
    public function __construct($hostname,$port,$user,$pass,$db) {
        $this->hostname = $hostname;
        $this->port = $port;
        $this->user = $user;
        $this->pass = $pass;
        $this->db = $db;
        $this->connect();
    }
    
    public function connect() {
        try {
            $this->con = new PDO("mysql:host=" . $this->hostname . ";dbname=". $this->db .";charset=utf8", $this->user, $this->pass);
            $this->con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->con->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
            return true;
        } catch (PDOException $e) {
            echo "DBLE Error: " . $e->getMessage();
            return false;
        } 
    }

    
    protected function doesTableExist($tablename) {
        if ($this->numrows("SHOW TABLES LIKE '$tablename'")<=0) {
            return false;
        }
        return true;
    }
    
    public function createObject($table, $name = "auto") {
        if ($this->doesTableExist($table)) {
            if ($name == 'auto') {
                $name = ucfirst($table);
            }
            $obj = new MySQLDBLEObject($this, $table, $name);
            return $obj;
        } else {
            throw new TableNotFoundException("Table $table wasn't found.");
        }
    }

    public function exec($sql, $param = array()) {
	$stmt = $this->con->prepare($sql);
	if ($stmt->execute($param)) {
		return true;
	}
	return false;
    }
    
    
    public function insert($sql,$param=array()) {
	$stmt = $this->con->prepare($sql);
	if ($stmt->execute($param)) {
		return $this->con->lastInsertId();
	}
	return false;
    }
    

    public function select($sql, $param = array()) {
        $stmt = $this->con->prepare($sql);
	$stmt->execute($param);
	return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function numrows($sql, $param = array()) {
        $stmt = $this->con->prepare($sql);
        $stmt->execute($param);
        return $stmt->rowCount();
    }
    
    public function getDB() {
        return $this->db;
    }

    public function getColumns($table) {
        $q = $this->con->prepare("DESCRIBE $table");
        $q->execute();
        return $q->fetchAll(PDO::FETCH_COLUMN);
    }

}
