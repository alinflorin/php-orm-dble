<?php

class AccdbAccessDBLE extends DBLE{
    public $con;
    private $user,$pass,$db;
    
    public function __construct($db,$user='',$pass='') {
        $this->pass = $pass;
        $this->db = $db;
        $this->user = $user;
        $this->connect();
    }
    
    public function connect() {
        try {
            $this->con = new PDO("odbc:DRIVER={Microsoft Access Driver (*.accdb)}; DBQ=".$this->db."; Uid=".$this->user."; Pwd=".$this->pass.";");
            $this->con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->con->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
            return true;
        } catch (PDOException $e) {
            echo "DBLE Error: " . $e->getMessage();
            return false;
        } 
    }

    
    protected function doesTableExist($tablename) {
        try {
            $rez = $this->select("SELECT 1 FROM " . $tablename);
            return true;
        } catch (Exception $ex) {
            return false;
        }
    }
    
    public function createObject($table, $name = "auto") {
        if ($this->doesTableExist($table)) {
            if ($name == 'auto') {
                $name = ucfirst($table);
            }
            $obj = new AccessDBLEObject($this, $table, $name);
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
        return; //FIXME //TODO 
    }

}
