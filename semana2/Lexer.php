<?php
class Lexer {
    private $input;
    private $pos = 0;
    private $length;

    public function __construct($input) {
        $this->input = $input;
        $this->length = strlen($input);
    }

    public function nextToken() {
        while ($this->pos < $this->length) {
            if (preg_match('/\G\s+/A', $this->input, $m, 0, $this->pos)) {
                $lexeme = $m[0];
                $this->pos += strlen($lexeme);
                continue;
            }
            if (preg_match('/\G[0-9]+(\.[0-9]+)?/A', $this->input, $m, 0, $this->pos)) {
                $lexeme = $m[0];
                $this->pos += strlen($lexeme);
                return array('num', $lexeme);
            }
            if (preg_match('/\G\+/A', $this->input, $m, 0, $this->pos)) {
                $lexeme = $m[0];
                $this->pos += strlen($lexeme);
                return array("'+'", $lexeme);
            }
            if (preg_match('/\G\-/A', $this->input, $m, 0, $this->pos)) {
                $lexeme = $m[0];
                $this->pos += strlen($lexeme);
                return array("'-'", $lexeme);
            }
            if (preg_match('/\G\*/A', $this->input, $m, 0, $this->pos)) {
                $lexeme = $m[0];
                $this->pos += strlen($lexeme);
                return array("'*'", $lexeme);
            }
            if (preg_match('/\G\(/A', $this->input, $m, 0, $this->pos)) {
                $lexeme = $m[0];
                $this->pos += strlen($lexeme);
                return array("'('", $lexeme);
            }
            if (preg_match('/\G\)/A', $this->input, $m, 0, $this->pos)) {
                $lexeme = $m[0];
                $this->pos += strlen($lexeme);
                return array("')'", $lexeme);
            }
            throw new Exception('Lexical error at position ' . $this->pos);
        }
        return array('EOF', null);
    }
}
