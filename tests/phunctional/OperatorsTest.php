<?php

namespace phunctional\operators;


class OperatorsTest extends \PHPUnit_Framework_TestCase {
    
    public function operatorDataProvider() {
        return [
            ['+', 2, 3, 5],
            ['-', 5, 3, 2],
            ['*', 2, 3, 6],
            ['/', 6, 3, 2],
            ['%', 7, 5, 2],
            ['&', 1, 0, 0],
            ['|', 1, 0, 1],
            ['^', 1, 2, 3],
            ['<<', 2, 4, 32],
            ['>>', 32, 4, 2],
            ['==', 2, '2', true],
            ['===', 2, '2', false],
            ['!=', 2, '2', false],
            ['!==', 2, '2', true],
            ['>', 5, 2, true],
            ['<', 2, 5, true],
            ['>=', 4, 4, true],
            ['<=', 5, 6, true],
            ['&&', false, true, false],
            ['||', false, true, true],
            ['xor', false, true, true],
            ['.', 'a', 'b', 'ab'],
            ['instanceof', new \stdclass, '\stdclass', true]
        ];
    }
    
    /**
     * @dataProvider operatorDataProvider
     */
    public function testOperator($operator, $op1, $op2, $expected) {
        $this->assertSame(
            $expected,
            \call_user_func(operator($operator), $op1, $op2)
        );
    }
    
    /**
     * @expectedException \InvalidArgumentException
     */
    public function testOperator_throwsExceptionForUnsupportedOperator() {
        operator('doesntExist');
    }
}
