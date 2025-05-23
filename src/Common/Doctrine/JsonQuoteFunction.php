<?php

namespace AlAya\Common\Doctrine;

use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\AST\InputParameter;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\SqlWalker;
use Doctrine\ORM\Query\TokenType;

class JsonQuoteFunction extends FunctionNode
{
    public $string = null;
    public InputParameter $value;

    public function parse(Parser $parser): void
    {
        $parser->match(TokenType::T_IDENTIFIER); // This is the fix
        $parser->match(TokenType::T_OPEN_PARENTHESIS);
        $this->value = $parser->InputParameter(); // Accepting parameter like :allFlag
        $parser->match(TokenType::T_CLOSE_PARENTHESIS);
    }

    public function getSql(SqlWalker $sqlWalker): string
    {
        return sprintf('JSON_QUOTE(%s)', $this->value->dispatch($sqlWalker));
    }
}
