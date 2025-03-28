<?php

declare(strict_types=1);

namespace Doctrine\ORM\Query\AST\Functions;

use Doctrine\ORM\Query\AST\Node;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\SqlWalker;
use Doctrine\ORM\Query\TokenType;

use function sprintf;

/**
 * "UPPER" "(" StringPrimary ")"
 *
 * @link    www.doctrine-project.org
 */
class UpperFunction extends FunctionNode
{
    public Node $stringPrimary;

    public function getSql(SqlWalker $sqlWalker): string
    {
        return sprintf(
            'UPPER(%s)',
            $sqlWalker->walkSimpleArithmeticExpression($this->stringPrimary),
        );
    }

    public function parse(Parser $parser): void
    {
        $parser->match(TokenType::T_IDENTIFIER);
        $parser->match(TokenType::T_OPEN_PARENTHESIS);

        $this->stringPrimary = $parser->StringPrimary();

        $parser->match(TokenType::T_CLOSE_PARENTHESIS);
    }
}
