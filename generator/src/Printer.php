<?php
/**
 * @package    gendarme
 * @author     Michael Calcinai <michael@calcin.ai>
 */

namespace Calcinai\XeroPHP\Generator;

use PhpParser\Comment;
use PhpParser\Node\Stmt;
use PhpParser\PrettyPrinter\Standard;

class Printer extends Standard {

    public function pComments(array $node) {
        return sprintf("\n%s", parent::pComments($node));
    }

    public function pStmt_Property(Stmt\Property $node) {
        return sprintf("%s\n", parent::pStmt_Property($node));
    }

    public function pStmt_ClassMethod(Stmt\ClassMethod $node) {
        return sprintf("%s\n", parent::pStmt_ClassMethod($node));
    }
}