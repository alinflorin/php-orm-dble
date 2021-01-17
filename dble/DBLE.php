<?php
abstract class DBLE {
    public abstract function connect();
    public abstract function exec($sql,$param=array());
    public abstract function select($sql,$param=array());
    public abstract function numrows($sql,$param=array());
    public abstract function createObject($table,$name="auto");
    public abstract function getColumns($table);
    protected abstract function doesTableExist($tablename);
    
}
