<?php

class UnexistentPKException extends DBLEException{
    public function __construct($message, $code, $previous) {
        parent::__construct($message, $code, $previous);
    }
}
