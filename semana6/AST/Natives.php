<?php

class Time extends Invocable {   
    public function invoke($interpreter, $args) {
        date_default_timezone_set("UTC");
        return date("d-M-Y");
    }
    public function get_arity() {
        return 0;
    }
}

return $embeded = array(
    'time' => new Time()    
);