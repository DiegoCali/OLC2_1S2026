<?php

class Instruction {

    public $instr;
    public $op1;
    public $op2;
    public $op3;

    public function __construct($instr, $op1=null, $op2=null, $op3=null) {
        $this->instr = $instr;
        $this->op1 = $op1;
        $this->op2 = $op2;
        $this->op3 = $op3;
    }

    public function __toString() {

        $ops = [];

        if ($this->op1 !== null) $ops[] = $this->op1;
        if ($this->op2 !== null) $ops[] = $this->op2;
        if ($this->op3 !== null) $ops[] = $this->op3;

        if (count($ops) === 0) return $this->instr;

        return $this->instr . " " . implode(", ", $ops);
    }
}

class ASMGenerator {

    private $instr = [];
    private $r;

    public function __construct() {        
        $this->r = include __DIR__ . "/Constants.php";
    }

    public function add($rd, $rs1, $rs2) {
        $this->instr[] = new Instruction("add", $rd, $rs1, $rs2);
    }

    public function sub($rd, $rs1, $rs2) {
        $this->instr[] = new Instruction("sub", $rd, $rs1, $rs2);
    }

    public function mul($rd, $rs1, $rs2) {
        $this->instr[] = new Instruction("mul", $rd, $rs1, $rs2);
    }

    public function div($rd, $rs1, $rs2) {
        $this->instr[] = new Instruction("sdiv", $rd, $rs1, $rs2);
    }

    public function addi($rd, $rs1, $imm) {
        $this->instr[] = new Instruction("add", $rd, $rs1, "#".$imm);
    }

    public function subi($rd, $rs1, $imm) {
        $this->instr[] = new Instruction("sub", $rd, $rs1, "#".$imm);
    }

    public function str($rs, $base, $offset=0) {
        $this->instr[] = new Instruction(
            "str",
            $rs,
            "[".$base.", #".$offset."]"
        );
    }

    public function ldr($rd, $base, $offset=0) {
        $this->instr[] = new Instruction(
            "ldr",
            $rd,
            "[".$base.", #".$offset."]"
        );
    }

    public function li($rd, $imm) {
        $this->instr[] = new Instruction("mov", $rd, "#".$imm);
    }

    public function mov($rd, $rs) {
        $this->instr[] = new Instruction("mov", $rd, $rs);
    }

    public function push($rd=null) {

        if ($rd === null) $rd = $this->r["T0"];

        $this->subi($this->r["SP"], $this->r["SP"], 8);
        $this->str($rd, $this->r["SP"]);
    }

    public function pop($rd=null) {

        if ($rd === null) $rd = $this->r["T0"];

        $this->ldr($rd, $this->r["SP"]);
        $this->addi($this->r["SP"], $this->r["SP"], 8);
    }

    public function syscall() {
        $this->instr[] = new Instruction("svc", "#0");
    }

    public function printInt($rd = null) {
        if ($rd === null) $rd = $this->r["A0"];

        // Si usamos otro registro temporal, salvamos A0
        if ($rd !== $this->r["A0"]) {
            $this->push($this->r["A0"]);
            $this->add($this->r["A0"], $rd, $this->r["ZERO"]);
        }

        // Reservar 8 bytes en stack para usar como buffer
        $this->sub($this->r["SP"], $this->r["SP"], 8);
        // Convertir dígito a ASCII y guardarlo en stack
        $this->add($this->r["T0"], $this->r["A0"], 0x30);
        $this->str($this->r["T0"], $this->r["SP"], 0);

        // Preparamos syscall write
        $this->li($this->r["SYS"], 64);       // write
        $this->mov($this->r["A0"], 1);        // stdout
        $this->mov($this->r["A1"], $this->r["SP"]); // puntero al buffer en stack
        $this->li($this->r["A2"], 1);         // longitud = 1 byte
        $this->syscall();

        // Liberar stack
        $this->add($this->r["SP"], $this->r["SP"], 8);

        // Restaurar A0 si usamos otro registro
        if ($rd !== $this->r["A0"]) {
            $this->pop($this->r["A0"]);
        }
    }

    public function endProgram() {

        $this->li($this->r["SYS"], 93);  // exit syscall
        $this->syscall();
    }

    public function comment($text) {
        $this->instr[] = new Instruction("// ".$text);
    }

    public function toString() {

        $out = ".text\n";
        $out .= ".global _start\n";
        $out .= "_start:\n";

        foreach ($this->instr as $inst) {
            $out .= "    ".$inst."\n";
        }

        return $out;
    }
}