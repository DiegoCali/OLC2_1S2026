<?php
class Expression {
    
    public function __construct($location) {
        $this->location = $location;
    }

    public function accept(Visitor $visitor) {
        return $visitor->visitExpression($this);
    }

    public function __toString() {
        return "Soy una expresión genérica";
    }
}

class UnaryExpression extends Expression {
    public $operator;
    public $operand;

    public function __construct($operator, $operand, $location) {
        parent::__construct($location);
        $this->operator = $operator;
        $this->operand = $operand;
    }

    public function accept(Visitor $visitor) {
        return $visitor->visitUnaryExpression($this);
    }

    public function __toString() {
        return "UnaryExpression(" . $this->operator . ", " . $this->operand . ")";
    }
}

class BinaryExpression extends Expression {
    public $left;
    public $operator;
    public $right;

    public function __construct($left, $operator, $right, $location) {
        parent::__construct($location);
        $this->left = $left;
        $this->operator = $operator;
        $this->right = $right;
    }

    public function accept(Visitor $visitor) {
        return $visitor->visitBinaryExpression($this);
    }

    public function __toString() {
        return "BinaryExpression(" . $this->left . " " . $this->operator . " " . $this->right . ")";
    }
}

class AgroupedExpression extends Expression {
    public $expression;

    public function __construct($expression, $location) {
        parent::__construct($location);
        $this->expression = $expression;
    }

    public function accept(Visitor $visitor) {
        return $visitor->visitAgroupedExpression($this);
    }

    public function __toString() {
        return "AgroupedExpression(" . $this->expression . ")";
    }
}

class NumberExpression extends Expression {
    public $value;

    public function __construct($value, $location) {
        parent::__construct($location);
        $this->value = $value;
    }

    public function accept(Visitor $visitor) {
        return $visitor->visitNumberExpression($this);
    }

    public function __toString() {
        return "NumberExpression(" . $this->value . ")";
    }
}