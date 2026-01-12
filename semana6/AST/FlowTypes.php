<?php
class FlowType {
    public function __tostring() {
        return "FlowType";
    }
}

class ContinueType extends FlowType{
    public function __tostring() {
        return "ContinueType";
    }
}

class BreakType extends FlowType{
    public function __tostring() {
        return "BreakType";
    }
}

class ReturnType extends FlowType {
    public $retVal;
    public function __construct($retVal = null) {
        $this->retVal = $retVal;
    }
    public function __tostring() {
        return "ReturnType";
    }
}