<?php 

use Context\ProgramContext;
use Context\PrintStatementContext;
use Context\VarDeclarationContext;
use Context\AssignmentStatementContext;
use Context\IfStatementContext;
use Context\WhileStatementContext;
use Context\ContinueStatementContext;
use Context\BreakStatementContext;
use Context\ReturnStatementContext;
use Context\FunctionDeclarationContext;
use Context\FunctionCallStatementContext;
use Context\ArrayAssignmentStatementContext;
use Context\BlockStatementContext;
use Context\EqualityExpressionContext;
use Context\InequalityExpressionContext;
use Context\AddExpressionContext;
use Context\ProductExpressionContext;
use Context\PrimaryExpressionContext;
use Context\UnaryExpressionContext;
use Context\GroupedExpressionContext;
use Context\IntExpressionContext;
use Context\ReferenceExpressionContext;
use Context\BoolExpressionContext;
use Context\FunctionCallExpressionContext;
use Context\ArrayExpressionContext;
use Context\ArrayAccessExpressionContext;
use Context\ParameterListContext;
use Context\ArgumentListContext;



class Compiler extends GrammarBaseVisitor {
    public $code;     
    public $r;

    public function __construct() {
        $this->code = new ASMGenerator();                
        $this->r = include __DIR__ . "/ARM/Constants.php";
    }

    public function visitProgram(ProgramContext $ctx) {                  
        foreach ($ctx->stmt() as $stmt) {            
            $this->visit($stmt);
        }
        $this->code->endProgram();
        return $this->code;
    }

    public function visitPrintStatement(PrintStatementContext $ctx) {        
        $this->visit($ctx->e());
        $this->code->pop($this->r["A0"]);
        $this->code->printInt($this->r["A0"]);
    }

    public function visitAddExpression(AddExpressionContext $ctx) {        
        $operable = $ctx->add() ? $this->visit($ctx->add()) : false;
        if (!$operable) {            
            $this->visit($ctx->prod());
            return;
        }   
        $this->visit($ctx->prod());        
        $this->code->pop($this->r["T0"]);
        $this->code->pop($this->r["T1"]);

        switch ($ctx->op->getText()) {
            case '+':
                $this->code->add($this->r["T0"], $this->r["T0"], $this->r["T1"]);
                $this->code->push($this->r["T0"]);
                break;
            case '-':
                $this->code->sub($this->r["T0"], $this->r["T0"], $this->r["T1"]);
                $this->code->push($this->r["T0"]);
                break;
            default:
                throw new Exception("Unknown operator: " . $ctx->op);
                break;
        }
    }

    public function visitProductExpression(ProductExpressionContext $ctx) {                
        $operable = $ctx->prod() ? $this->visit($ctx->prod()) : false;
        if (!$operable) {
            $this->visit($ctx->unary()); 
            return;
        }
        $this->visit($ctx->unary());
        
        $this->code->pop($this->r["T0"]);
        $this->code->pop($this->r["T1"]);

        if ($ctx->op === null) {
            $this->code->push($this->r["T0"]);
            return;
        }
        switch ($ctx->op->getText()) {
            case '*':
                $this->code->mul($this->r["T0"], $this->r["T0"], $this->r["T1"]);
                $this->code->push($this->r["T0"]);
                break;
            case '/':
                $this->code->div($this->r["T0"], $this->r["T0"], $this->r["T1"]);
                $this->code->push($this->r["T0"]);
                break;
            default:
                throw new Exception("Unknown operator: " . $ctx->op);
                break;
        }        
    }

    public function visitPrimaryExpression(PrimaryExpressionContext $ctx) {
        return $this->visit($ctx->primary());
    }

    public function visitUnaryExpression(UnaryExpressionContext $ctx) {        
        $this->visit($ctx->unary());
        $this->code->pop($this->r["T0"]);

        $this->code->sub($this->r["T0"], $this->r["ZERO"], $this->r["T0"]);
        $this->code->push($this->r["T0"]);
    }

    public function visitGroupedExpression(GroupedExpressionContext $ctx) {
        return $this->visit($ctx->e());
    }

    public function visitIntExpression(IntExpressionContext $ctx) {
        $number = intval($ctx->INT()->getText());
        $this->code->li($this->r["T0"], $number);
        $this->code->push();
    }      
}