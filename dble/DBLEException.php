<?php

class DBLEException extends Exception{
    public function __construct($message, $code, $previous) {
        parent::__construct($message, $code, $previous);
    }
}
