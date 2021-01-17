<?php
class AccessDBLEObject extends DBLEObject{

    protected function generatePKConstructor() {
        if ($this->pk_column == null) {
            throw new UnexistentPKException("You must define a PK.",5,null);
        }
        $pkvarname = $this->pk_column;
        
        $con2 = 'public function __construct2($dble,$pk) {'."\n".'
                $this->dble = $dble;'."\n".'
                $this->'.$pkvarname.' = $pk;'."\n".'
                $rez = $dble->select("SELECT TOP 1 * FROM '.$this->table.' WHERE '.$pkvarname.'=?",array($pk));'."\n";
        $con2 .= 'if (!empty($rez)) {' . "\n";  
        foreach ($this->column_names as $col) {
            if ($col !== $pkvarname) {
                $con2 .= "\$this->$col=\$rez[0]['$col'];\n";
            }
        }
        $con2 .= "} else throw new Exception(\"Object data not found in DB\",1,null);\n";        
        $con2 .= "}\n";
        
        $this->code .= "\n" . $con2 . "\n\n";
    }
    
    protected function generateDataConstructor() {
        $pkvarname = $this->pk_column;
        $con3 = 'public static function fromData ($dble,';
        foreach ($this->column_names as $col) {
            if ($col !== $pkvarname) {
                $con3.='$' . $col . ",";
            }
        }
        $con3 = substr($con3,0,strlen($con3)-1) . ") {\n";
        $con3 .= '$obj = new '.$this->name.'($dble); '."\n";
        foreach ($this->column_names as $col) {
            if ($col !== $pkvarname) {
                $con3.='$obj->' . $col . "=" . '$' . $col . ";\n";
            }
        }
        $con3 .= "return \$obj;\n";
        $con3 .= "}\n";
        $this->code .= "\n\n" . $con3 . "\n\n";
    }
    
    protected function generateGettersSetters() {
        for ($i=0;$i<sizeof($this->column_names);$i++) {
            if ($this->column_visibility[$i] == "private") {
                //Getter
                $g = 'public function get' . ucfirst($this->column_names[$i]) . '() {' . "\n";
                $g .= 'return $this->' . $this->column_names[$i] . ";\n";
                $g .= '}' . "\n\n";
                $this->code .= $g;
                $s = 'public function set' . ucfirst($this->column_names[$i]) . '($val) {' . "\n";
                $s .= '$this->' . $this->column_names[$i] . ' = $val;' . "\n";
                $s .= '$this->toDB();' . "\n";
                $s .= '}' . "\n\n";
                $this->code .= $s;
            }
        }
    }

    protected function generateSaveToDB() {
        $arr = "array(";
        $sql = "UPDATE " . $this->table . " SET ";
        $values = "VALUES(";
        $sql2 = "INSERT INTO " . $this->table . "(";
        $arr2 = "array(";
        foreach ($this->column_names as $col) {
            if ($col !== $this->pk_column) {
                $sql .= $col . "=?,";
                $arr .= '$this->' . $col . ",";
                $arr2 .= '$this->' . $col . ",";
                $values .= '?,';
                $sql2 .= $col . ",";
            }
            
        }
        $arr .= '$this->' . $this->pk_column;
        $arr .= ")";
        $arr2 = substr($arr2,0,strlen($arr2)-1);
        $arr2 .= ")";
        $values = substr($values,0,strlen($values)-1);
        $values .= ")";
        $sql = substr($sql,0,strlen($sql)-1);
        $sql .= " WHERE " . $this->pk_column . "=?";
        $sql2 = substr($sql2,0,strlen($sql2)-1);
        $sql2 .= ") " . $values;
        
        
        
        $sdb = 'public function toDB() {' . "\n";
        $sdb .= 'if ($this->'.$this->pk_column.' !== null) {' . "\n";
        $sdb .= '$this->dble->exec("'.$sql.'",'.$arr.')' . ";\n";
        $sdb .= "} else {\n";
        $sdb .= 'if ($this->dble->exec("'.$sql2.'",'.$arr2.')' . ") {\n";
        $sdb .= '$rez=$this->dble->select("SELECT MAX('.$this->pk_column.') FROM '.$this->table.'")' . ";\n";
        $sdb .= '$this->'.$this->pk_column.' = $rez[0]["Expr1000"]' . ";\n";        
        $sdb .= "}\n";
        $sdb .= "}\n";
        $sdb .= "}\n";
        $this->code .= "\n\n" . $sdb . "\n\n";
    }
    
    
    protected function doesColumnExist($col) {
        return true; //FIXME //TODO
    }
    
    protected function isColumnPK($col) {
        return true; //FIXME //TODO
    }

}

