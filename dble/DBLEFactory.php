<?php

class DBLEFactory {
    public static function create($type,$hostname,$port,$user,$pass,$db) {
        switch ($type) {
            case 'mysql':
                return new MySQLDBLE($hostname,$port,$user,$pass,$db);
                
            case 'access-mdb':
                return new AccessDBLE($db,$user,$pass);
            default:
                return null;
        }
    }
    
    
}
