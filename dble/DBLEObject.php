<?php

abstract class DBLEObject {
    protected $name;
    protected $table;
    protected $pk_column=null;
    protected $column_names = array();
    protected $column_ro = array();
    protected $column_visibility = array();
    public $code;
    protected $dble;
    
    public function __construct($dble,$table,$name) {
        $this->table = $table;
        $this->name = $name;
        $this->dble = $dble;
    }
    
    protected function generateAttributes() {
        $this->code .= 'private $dble;' . "\n";
        for ($i=0;$i<sizeof($this->column_names);$i++) {
		$v = $this->column_visibility[$i];
		$n = $this->column_names[$i];
                if ($this->column_names[$i] == $this->pk_column) {
                    $this->code .= "$v \$" . $n . "=null;\n";
                } else {
                    $this->code .= "$v \$" . $n . ";\n";
                }
	}
        $this->code .= "\n\n";
    }
    
        public function generateCode() {
        $this->code = 'class ' . $this->name . ' {' . "\n";  
        $this->generateAttributes();
        $this->generateOriginalConstructor();
        $this->generateEmptyConstructor();
        $this->generatePKConstructor();
        $this->generateDataConstructor();
        $this->generateGettersSetters();
        $this->generateSaveToDB();
        $this->code .= '}';
        //die(str_replace("\n","<br>",$this->code));
    }
    
    protected abstract function generatePKConstructor();
    protected abstract function generateDataConstructor();
    protected abstract function doesColumnExist($col);
    protected abstract function generateGettersSetters();
    protected abstract function generateSaveToDB();
    protected abstract function isColumnPK($col);
    
    protected function generateOriginalConstructor() {
        $this->code  .= 'public function __construct() { 
        $a = func_get_args(); 
        $i = func_num_args(); 
        if (method_exists($this,$f=\'__construct\'.$i)) { 
            call_user_func_array(array($this,$f),$a); 
        } 
        } ' . "\n\n";
    }
    
    protected function generateEmptyConstructor() {
        $this->code.='public function __construct1($dble) {$this->dble = $dble;}' . "\n";
    }
    
    public function addLink($col_vis,$col_name,$ispk) {
        if ($this->doesColumnExist($col_name)) {
            if ($col_vis == 'public' || $col_vis == 'private' || $col_vis == 'protected') {
                if (is_bool($ispk)) {
                    //ADD ATTR TO CLASS
                    array_push($this->column_names,$col_name);
                    array_push($this->column_visibility,$col_vis);
                    if ($ispk) {
                        if ($this->pk_column !== null) {
                            throw new AlreadyHasPKException("Already has a PK defined.",4,null);
                        } else {
                            //Verif daca intr-adevar e PK
                            if ($this->isColumnPK($col_name)) {
                                $this->pk_column = $col_name;
                            } else {
                                throw new NotPKException("The column provided is not a primary key in the table.",6,null);
                            }
                        }
                    }
                } else {
                    throw new NoBooleanGivenException("Boolean expected",3,null);
                }
            } else {
                throw new InvalidVisibilityKeywordException("Invalid visibility keyword: " + $col_vis,2,null);
            }
        } else {
            throw new ColumnNotFoundException("Column doesn't exist in the table " + $this->table,1,null);
        }
    }

    public function instantiate() {
        $this->generateCode();
        if (!class_exists($this->name)) {
            eval($this->code);
        }
        $temp = null;
        eval("\$temp = new " . $this->name . "(\$this->dble);");
        return $temp;
    }
    
    
    public function instantiateFromPK($pkvalue) {
        $this->generateCode();
        if (!class_exists($this->name)) {
            eval($this->code);
        }
        $temp = null;
        eval("\$temp = new " . $this->name . "(\$this->dble,$pkvalue);");
        return $temp;
    }
    
    
    public function instantiateFromData($data_array) {
        $this->generateCode();
        if (!class_exists($this->name)) {
            eval($this->code);
        }
        $temp = null;
        $cmd = "\$temp = " . $this->name . "::fromData(\$this->dble";
        foreach ($data_array as $data) {
            if (is_string($data)) {
                $data = str_replace('"','\\"',$data);
                $data = '"'.$data.'"';
            }
            $cmd .= "," . $data;
        }
        $cmd .= ");";
        eval($cmd);
        return $temp;
    }
    
    
    
}
