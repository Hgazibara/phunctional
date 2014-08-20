![Build status](https://travis-ci.org/Hgazibara/phunctional.svg)

phunctional
===========

A simple set of functions making functional programming in PHP easier. Code is
inspired by Haskell and Python, especially Python's modules [itertools](https://docs.python.org/2/library/itertools.html),
[functools](https://docs.python.org/2/library/functools.html), and [toolz](https://pypi.python.org/pypi/toolz/).

Another important intention is to create a single interface for arrays and strings. Strings can be converted into iterator using `phunctional\iter\istring` and then passed to `map()`, `slice()`, `count()` or any other method which works with iterable sequences. In the near future, similar thing should be possible with resources, too.

API List
=========

Until better documentation is created, here's a list of exposed functions:

```
<?php

// From phunctional\iter

boolean function isIterable($sequence)
\Generator function filter(callable $callback, $sequence)
\Generator function filterFalse(callable $callback, $sequence)
\Generator function map(callable $callback, $sequence)
\Generator function reduce(callable $callback, $sequence, $start = 0)
\Generator function sum($sequence, $start = 0)
mixed function product($sequence, $start = 1)
boolean function any($sequence)
boolean function all($sequence)
\Generator function cycle($sequence)
\Generator function range($start, $stop=null, $step = 1)
\Generator function repeat($item, $times)
\Generator function istring($string, $encoding='UTF-8')
\Generator function iter($sequence)
mixed function next(\Iterator $iterator, $default=null)
array function toArray($target)
\Generator function zip(/* mixed[] $sequences */)
\Generator function zipLongest(/* mixed[] $sequences */)
\Generator function slice($sequence, $start = 0, $length = null, $step = 1)
\Generator function drop($length, $sequence)
\Generator function take($lenth, $sequence)
mixed function second($sequence)
mixed function last($sequence)
mixed function nth($which, $sequence)
mixed function count($sequence)

// From phunctional\func

callable function partial(callable $fn /* mixed[] $args */)

// From phunctional\operators
callable operator($operator)
callable id()
callable not()
```