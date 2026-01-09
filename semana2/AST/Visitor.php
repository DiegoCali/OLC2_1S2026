<?php
interface Visitor {
    public function visitExpression(Expression $expr);
    public function visitUnaryExpression(UnaryExpression $expr);
    public function visitBinaryExpression(BinaryExpression $expr);
    public function visitAgroupedExpression(AgroupedExpression $expr);
    public function visitNumberExpression(NumberExpression $expr);
}