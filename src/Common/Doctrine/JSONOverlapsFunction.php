<?php

namespace AlAya\Common\Doctrine;

use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\SqlWalker;
use Doctrine\ORM\Query\TokenType;


class JSONOverlapsFunction extends FunctionNode
{
    public $field = null;
    public $value = null;

    public function parse(Parser $parser) :void
    {
        $parser->match(TokenType::T_IDENTIFIER);          // JSON_OVERLAPS
        $parser->match(TokenType::T_OPEN_PARENTHESIS);    // (
        $this->field = $parser->ArithmeticPrimary();  // first arg
        $parser->match(TokenType::T_COMMA);               // ,
        $this->value = $parser->ArithmeticPrimary();  // second arg
        $parser->match(TokenType::T_CLOSE_PARENTHESIS);   // )
    }

    public function getSql(SqlWalker $sqlWalker) : string
    {
        return sprintf(
            'JSON_OVERLAPS(%s, %s)',
            $this->field->dispatch($sqlWalker),
            $this->value->dispatch($sqlWalker)
        );
    }
}
