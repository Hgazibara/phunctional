<?php

namespace phunctional\operators {
    
    function operator($operator) {
        $operators = [
            '+' => function ($a, $b) { return $a + $b; },
            '-' => function ($a, $b) { return $a - $b; },
            '*' => function ($a, $b) { return $a * $b; },
            '/' => function ($a, $b) { return $a / $b; },
            '%' => function ($a, $b) { return $a % $b; },
            '&' => function ($a, $b) { return $a & $b; },
            '|' => function ($a, $b) { return $a | $b; },
            '^' => function ($a, $b) { return $a ^ $b; },
            '<<' => function ($a, $b) { return $a << $b; },
            '>>' => function ($a, $b) { return $a >> $b; },
            '==' => function ($a, $b) { return $a == $b; },
            '===' => function ($a, $b) { return $a === $b; },
            '!=' => function ($a, $b) { return $a != $b; },
            '!==' => function ($a, $b) { return $a !== $b; },
            '>' => function ($a, $b) { return $a > $b; },
            '<' => function ($a, $b) { return $a < $b; },
            '>=' => function ($a, $b) { return $a >= $b; },
            '<=' => function ($a, $b) { return $a <= $b; },
            '&&' => function ($a, $b) { return $a && $b; },
            '||' => function ($a, $b) { return $a || $b; },
            'xor' => function ($a, $b) { return $a xor $b; },
            '.' => function ($a, $b) { return $a . $b; },
            'instanceof' => function ($a, $b) { return $a instanceof $b; },
            '!' => function ($a) { return !$a; }
        ];

        if (!\array_key_exists($operator, $operators)) {
            throw new \InvalidArgumentException("Unsupported operator '$operator'");
        }

        return $operators[$operator];
    }

    function neg() {
        return function ($a) { return -$a; };
    }

    function id() {
        return function ($a) { return $a; };
    }
}
