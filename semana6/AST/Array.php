<?php
class ArrayValue {
    private $elements = [];

    public function __construct($elements = []) {
        $this->elements = $elements;
    }

    public function get(array $indexes) {
        if (empty($indexes)) {
            return $this;
        }

        $i = array_shift($indexes);

        if (!isset($this->elements[$i])) {
            throw new Exception("Índice fuera de rango");
        }

        $value = $this->elements[$i];

        if ($value instanceof ArrayValue) {
            return $value->get($indexes);
        }

        if (!empty($indexes)) {
            throw new Exception("Acceso a no-array");
        }

        return $value;
    }

    public function set(array $indexes, $value) {
        $i = array_shift($indexes);

        if (empty($indexes)) {
            $this->elements[$i] = $value;
            return;
        }

        if (!isset($this->elements[$i]) || !($this->elements[$i] instanceof ArrayValue)) {
            throw new Exception("Acceso a no-array");
        }

        $this->elements[$i]->set($indexes, $value);
    }
}
