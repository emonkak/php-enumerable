<?php

namespace Emonkak\Enumerable;

use Emonkak\Enumerable\Internal\Converters;
use Emonkak\Enumerable\Internal\Errors;
use Emonkak\Enumerable\Iterator\BufferIterator;
use Emonkak\Enumerable\Iterator\CatchIterator;
use Emonkak\Enumerable\Iterator\ConcatIterator;
use Emonkak\Enumerable\Iterator\DefaultIfEmptyIterator;
use Emonkak\Enumerable\Iterator\DistinctIterator;
use Emonkak\Enumerable\Iterator\DistinctUntilChangedIterator;
use Emonkak\Enumerable\Iterator\DoIterator;
use Emonkak\Enumerable\Iterator\DoWhileIterator;
use Emonkak\Enumerable\Iterator\EmptyIterator;
use Emonkak\Enumerable\Iterator\ExceptIterator;
use Emonkak\Enumerable\Iterator\FinallyIterator;
use Emonkak\Enumerable\Iterator\GroupByIterator;
use Emonkak\Enumerable\Iterator\GroupJoinIterator;
use Emonkak\Enumerable\Iterator\IntersectIterator;
use Emonkak\Enumerable\Iterator\JoinIterator;
use Emonkak\Enumerable\Iterator\MemoizeIterator;
use Emonkak\Enumerable\Iterator\OnErrorResumeNextIterator;
use Emonkak\Enumerable\Iterator\OrderByIterator;
use Emonkak\Enumerable\Iterator\OuterJoinIterator;
use Emonkak\Enumerable\Iterator\RepeatIterator;
use Emonkak\Enumerable\Iterator\RetryIterator;
use Emonkak\Enumerable\Iterator\ReverseIterator;
use Emonkak\Enumerable\Iterator\ScanIterator;
use Emonkak\Enumerable\Iterator\SelectIterator;
use Emonkak\Enumerable\Iterator\SelectManyIterator;
use Emonkak\Enumerable\Iterator\SkipIterator;
use Emonkak\Enumerable\Iterator\SkipLastIterator;
use Emonkak\Enumerable\Iterator\SkipWhileIterator;
use Emonkak\Enumerable\Iterator\StartWithIterator;
use Emonkak\Enumerable\Iterator\TakeIterator;
use Emonkak\Enumerable\Iterator\TakeLastIterator;
use Emonkak\Enumerable\Iterator\TakeWhileIterator;
use Emonkak\Enumerable\Iterator\UnionIterator;
use Emonkak\Enumerable\Iterator\WhereIterator;
use Emonkak\Enumerable\Iterator\WhileIterator;
use Emonkak\Enumerable\Iterator\ZipIterator;

trait EnumerableExtensions
{
    /**
     * @param mixed    $seed
     * @param callable $func
     * @return mixed
     */
    public function aggregate($seed, callable $func)
    {
        $result = $seed;
        foreach ($this->getSource() as $element) {
            $result = $func($result, $element);
        }
        return $result;
    }

    /**
     * @param callable|null $predicate
     * @return boolean
     */
    public function all(callable $predicate = null)
    {
        $predicate = $predicate ?: $this->identityFunction();
        foreach ($this->getSource() as $element) {
            if (!$predicate($element)) {
                return false;
            }
        }
        return true;
    }

    /**
     * @param callable|null $predicate
     * @return boolean
     */
    public function any(callable $predicate = null)
    {
        $predicate = $predicate ?: $this->identityFunction();
        foreach ($this->getSource() as $element) {
            if ($predicate($element)) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param callable|null $selector
     * @return boolean
     */
    public function average(callable $selector = null)
    {
        $selector = $selector ?: $this->identityFunction();
        $sum = 0;
        $count = 0;
        foreach ($this->getSource() as $element) {
            $sum += $selector($element);
            $count++;
        }
        if ($count === 0) {
            throw Errors::noElements();
        }
        return $sum / $count;
    }

    /**
     * @param integer      $count
     * @param integer|null $skip
     * @return EnumerableInterface
     */
    public function buffer($count, $skip = null)
    {
        if ($skip === null) {
            $skip = $count;
        }
        if ($count <= 0) {
            throw Errors::argumentOutOfRange('count');
        }
        if ($skip <= 0) {
            throw Errors::argumentOutOfRange('skip');
        }
        return new BufferIterator($this->getSource(), $count, $skip);
    }

    /**
     * @param callable $handler
     * @return EnumerableInterface
     */
    public function _catch(callable $handler)
    {
        return new CatchIterator($this->getSource(), $handler);
    }

    /**
     * @param array|\Traversable $second
     * @return EnumerableInterface
     */
    public function concat($second)
    {
        return new ConcatIterator([$this->getSource(), $second]);
    }

    /**
     * @param callable $predicate
     * @return integer
     */
    public function count(callable $predicate = null)
    {
        $source = $this->getSource();
        if ($predicate !== null) {
            $count = 0;
            foreach ($source as $element) {
                if ($predicate($element)) {
                    $count++;
                }
            }
            return $count;
        } else {
            if (is_array($source) || $source instanceof \Countable) {
                return count($source);
            }
            return iterator_count($source);
        }
    }

    /**
     * @param mixed $defaultValue
     * @return EnumerableInterface
     */
    public function defaultIfEmpty($defaultValue)
    {
        return new DefaultIfEmptyIterator($this->getSource(), $defaultValue);
    }

    /**
     * @param callable|null                  $keySelector
     * @param EqualityComparerInterface|null $comparer
     * @return EnumerableInterface
     */
    public function distinct(callable $keySelector = null, EqualityComparer $comparer = null)
    {
        $keySelector = $keySelector ?: $this->identityFunction();
        $comparer = $comparer ?: EqualityComparer::getInstance();
        return new DistinctIterator($this->getSource(), $keySelector, $comparer);
    }

    /**
     * @param callable|null $keySelector
     * @return EnumerableInterface
     */
    public function distinctUntilChanged(callable $keySelector = null)
    {
        $keySelector = $keySelector ?: $this->identityFunction();
        return new DistinctUntilChangedIterator($this->getSource(), $keySelector);
    }

    /**
     * @param callable $action
     * @return EnumerableInterface
     */
    public function _do(callable $action)
    {
        return new DoIterator($this->getSource(), $action);
    }

    /**
     * @param callable $condition
     * @return EnumerableInterface
     */
    public function doWhile(callable $condition)
    {
        return new DoWhileIterator($this->getSource(), $condition);
    }

    /**
     * @param integer $index
     * @return mixed
     */
    public function elementAt($index)
    {
        $source = $this->getSource();
        if (is_array($source) && isset($source[0])) {
            if ($index < count($source)) {
                return $source[$index];
            }
        } else {
            foreach ($source as $element) {
                if ($index === 0) {
                    return $element;
                }
                $index--;
            }
        }
        throw Errors::noElements();
    }

    /**
     * @param integer $index
     * @param mixed   $defaultValue
     * @return mixed
     */
    public function elementAtOrDefault($index, $defaultValue = null)
    {
        $source = $this->getSource();
        if (is_array($source) && isset($source[0])) {
            if ($index < count($source)) {
                return $source[$index];
            }
        } else {
            foreach ($source as $element) {
                if ($index === 0) {
                    return $element;
                }
                $index--;
            }
        }
        return $defaultValue;
    }

    /**
     * @param array|\Traversable             $second
     * @param EqualityComparerInterface|null $comparer
     * @return EnumerableInterface
     */
    public function except($second, EqualityComparerInterface $comparer = null)
    {
        $comparer = $comparer ?: EqualityComparer::getInstance();
        return new ExceptIterator($this->getSource(), $second, $comparer);
    }

    /**
     * @param callable $finallyAction
     * @return EnumerableInterface
     */
    public function _finally(callable $finallyAction)
    {
        return new FinallyIterator($this->getSource(), $finallyAction);
    }

    /**
     * @param callable|null $predicate
     * @return mixed
     */
    public function first(callable $predicate = null)
    {
        if ($predicate) {
            foreach ($this->getSource() as $element) {
                if ($predicate($element)) {
                    return $element;
                }
            }
        } else {
            foreach ($this->getSource() as $element) {
                return $element;
            }
        }
        throw Errors::noElements();
    }

    /**
     * @param callable|null $predicate
     * @param mixed         $defaultValue
     * @return mixed
     */
    public function firstOrDefault(callable $predicate = null, $defaultValue = null)
    {
        if ($predicate) {
            foreach ($this->getSource() as $element) {
                if ($predicate($element)) {
                    return $element;
                }
            }
        } else {
            foreach ($this->getSource() as $element) {
                return $element;
            }
        }
        return $defaultValue;
    }

    /**
     * @param callable|null $action
     */
    public function _forEach(callable $action)
    {
        foreach ($this->getSource() as $element) {
            $action($element);
        }
    }

    /**
     * @param callable $keySelector
     * @param callable $elementSelector
     * @param callable $resultSelector
     * @return EnumerableInterface
     */
    public function groupBy(callable $keySelector, callable $elementSelector = null, callable $resultSelector = null)
    {
        $elementSelector = $elementSelector ?: $this->identityFunction();
        $resultSelector = $resultSelector ?: function($k, $vs) {
            return [$k, $vs];
        };
        return new GroupByIterator($this->getSource(), $keySelector, $elementSelector, $resultSelector);
    }

    /**
     * @param array|\Traversable $inner
     * @param callable           $outerKeySelector
     * @param callable           $innerKeySelector
     * @param callable           $resultSelector
     * @return EnumerableInterface
     */
    public function groupJoin($inner, callable $outerKeySelector, callable $innerKeySelector, callable $resultSelector)
    {
        return new GroupJoinIterator($this->getSource(), $inner, $outerKeySelector, $innerKeySelector, $resultSelector);
    }

    /**
     * @return EnumerableInterface
     */
    public function ignoreElements()
    {
        return new EmptyIterator();
    }

    /**
     * @param array|\Traversable             $second
     * @param EqualityComparerInterface|null $comparer
     * @return EnumerableInterface
     */
    public function intersect($second, EqualityComparerInterface $comparer = null)
    {
        $comparer = $comparer ?: EqualityComparer::getInstance();
        return new IntersectIterator($this->getSource(), $second, $comparer);
    }

    /**
     * @return boolean
     */
    public function isEmpty()
    {
        foreach ($this->getSource() as $_) {
            return false;
        }
        return true;
    }

    /**
     * @param array|\Traversable $inner
     * @param callable           $outerKeySelector
     * @param callable           $innerKeySelector
     * @param callable           $resultSelector
     * @return EnumerableInterface
     */
    public function join($inner, callable $outerKeySelector, callable $innerKeySelector, callable $resultSelector)
    {
        return new JoinIterator($this->getSource(), $inner, $outerKeySelector, $innerKeySelector, $resultSelector);
    }

    /**
     * @param callable|null $predicate
     * @return mixed
     */
    public function last(callable $predicate = null)
    {
        if ($predicate) {
            $hasValue = false;
            $value = null;
            foreach ($this->getSource() as $element) {
                if ($predicate($element)) {
                    $value = $element;
                    $hasValue = true;
                }
            }
            if ($hasValue) {
                return $value;
            }
        } else {
            $hasValue = false;
            $value = null;
            foreach ($this->getSource() as $element) {
                $hasValue = true;
                $value = $element;
            }
            if ($hasValue) {
                return $element;
            }
        }
        throw Errors::noElements();
    }

    /**
     * @param callable|null $predicate
     * @param mixed         $defaultValue
     * @return mixed
     */
    public function lastOrDefault(callable $predicate = null, $defaultValue = null)
    {
        if ($predicate) {
            $hasValue = false;
            $value = null;
            foreach ($this->getSource() as $element) {
                if ($predicate($element)) {
                    $value = $element;
                    $hasValue = true;
                }
            }
            if ($hasValue) {
                return $value;
            }
        } else {
            $hasValue = false;
            $value = null;
            foreach ($this->getSource() as $element) {
                $hasValue = true;
                $value = $element;
            }
            if ($hasValue) {
                return $element;
            }
        }
        return $defaultValue;
    }

    /**
     * @param callable|null $selector
     * @return integer
     */
    public function max(callable $selector = null)
    {
        $selector = $selector ?: $this->identityFunction();
        $max = -INF;
        foreach ($this->getSource() as $element) {
            $value = $selector($element);
            if ($max < $value) {
                $max = $value;
            }
        }
        return $max;
    }

    /**
     * @param callable $selector
     * @return mixed[]
     */
    public function maxBy(callable $keySelector)
    {
        $result = [];

        $iterator = $this->toIterator();
        $iterator->rewind();

        if ($iterator->valid()) {
            $element = $iterator->current();
            $resultKey = $keySelector($element);
            $result[] = $element;
            $iterator->next();

            while ($iterator->valid()) {
                $element = $iterator->current();
                $key = $keySelector($element);
                if ($key == $resultKey) {
                    $result[] = $element;
                } elseif ($key > $resultKey) {
                    $resultKey = $key;
                    $result = [$element];
                }
                $iterator->next();
            }
        }

        return $result;
    }

    /**
     * @return EnumerableInterface
     */
    public function memoize()
    {
        return new MemoizeIterator($this->toIterator());
    }

    /**
     * @param callable|null $selector
     * @return integer
     */
    public function min(callable $selector = null)
    {
        $selector = $selector ?: $this->identityFunction();
        $max = INF;
        foreach ($this->getSource() as $element) {
            $value = $selector($element);
            if ($max > $value) {
                $max = $value;
            }
        }
        return $max;
    }

    /**
     * @param callable $selector
     * @return mixed[]
     */
    public function minBy(callable $keySelector)
    {
        $result = [];

        $iterator = $this->toIterator();
        $iterator->rewind();

        if ($iterator->valid()) {
            $element = $iterator->current();
            $resultKey = $keySelector($element);
            $result[] = $element;
            $iterator->next();

            while ($iterator->valid()) {
                $element = $iterator->current();
                $key = $keySelector($element);
                if ($key == $resultKey) {
                    $result[] = $element;
                } elseif ($key < $resultKey) {
                    $resultKey = $key;
                    $result = [$element];
                }
                $iterator->next();
            }
        }

        return $result;
    }

    /**
     * @param array[]|\Traversable[] $sources
     * @return EnumerableInterface
     */
    public function onErrorResumeNext($second)
    {
        return new OnErrorResumeNextIterator([$this->getSource(), $second]);
    }

    /**
     * @param array|\Traversable $inner
     * @param callable           $outerKeySelector
     * @param callable           $innerKeySelector
     * @param callable           $resultSelector
     * @return EnumerableInterface
     */
    public function outerJoin($inner, callable $outerKeySelector, callable $innerKeySelector, callable $resultSelector)
    {
        return new OuterJoinIterator($this->getSource(), $inner, $outerKeySelector, $innerKeySelector, $resultSelector);
    }

    /**
     * @param callable|null $keySelector
     * @return OrderedEnumerableInterface
     */
    public function orderBy(callable $keySelector = null)
    {
        $keySelector = $keySelector ?: $this->identityFunction();
        return new OrderByIterator($this->getSource(), $keySelector, false);
    }

    /**
     * @param callable|null $keySelector
     * @return OrderedEnumerableInterface
     */
    public function orderByDescending(callable $keySelector = null)
    {
        $keySelector = $keySelector ?: $this->identityFunction();
        return new OrderByIterator($this->getSource(), $keySelector, true);
    }

    /**
     * @param integer|null $count
     * @return EnumerableInterface
     */
    public function repeat($count = null)
    {
        return new RepeatIterator($this->getSource(), $count);
    }

    /**
     * @param integer|null $retryCount
     * @return EnumerableInterface
     */
    public function retry($retryCount = null)
    {
        return new RetryIterator($this->getSource(), $retryCount);
    }

    /**
     * @return EnumerableInterface
     */
    public function reverse()
    {
        return new ReverseIterator($this->getSource());
    }

    /**
     * @param mixed    $seed
     * @param callable $func
     * @return mixed
     */
    public function scan($seed, callable $func)
    {
        return new ScanIterator($this->getSource(), $seed, $func);
    }

    /**
     * @param callable $selector
     * @return EnumerableInterface
     */
    public function select(callable $selector)
    {
        return new SelectIterator($this->getSource(), $selector);
    }

    /**
     * @param callable $collectionSelector
     * @return EnumerableInterface
     */
    public function selectMany(callable $collectionSelector)
    {
        return new SelectManyIterator($this->getSource(), $collectionSelector);
    }

    /**
     * @param callable|null $predicate
     * @return mixed
     */
    public function single(callable $predicate = null)
    {
        $source = $this->getSource();
        if ($predicate !== null) {
            $value = null;
            $hasValue = false;

            foreach ($source as $element) {
                if ($predicate($element)) {
                    if ($hasValue) {
                        throw Errors::moreThanOneMatch();
                    }
                    $value = $element;
                    $hasValue = true;
                }
            }

            if ($hasValue) {
                return $value;
            }
        } else {
            if (is_array($source)) {
                switch (count($source)) {
                    case 0:
                        throw Errors::noElements();
                    case 1:
                        return reset($source);
                    default:
                        throw Errors::moreThanOneMatch();
                }
            } else {
                $value = null;
                $hasValue = false;

                foreach ($source as $element) {
                    if ($hasValue) {
                        throw Errors::moreThanOneMatch();
                    }
                    $value = $element;
                    $hasValue = true;
                }

                if ($hasValue) {
                    return $value;
                }
            }
        }
        throw Errors::noElements();
    }

    /**
     * @param callable|null $predicate
     * @return mixed
     */
    public function singleOrDefault(callable $predicate = null, $defaultValue = null)
    {
        $source = $this->getSource();
        if ($predicate !== null) {
            $value = null;
            $hasValue = false;

            foreach ($source as $element) {
                if ($predicate($element)) {
                    if ($hasValue) {
                        return $defaultValue;
                    }
                    $value = $element;
                    $hasValue = true;
                }
            }

            if ($hasValue) {
                return $value;
            }
        } else {
            if (is_array($source)) {
                switch (count($source)) {
                    case 0:
                        return $defaultValue;
                    case 1:
                        return reset($source);
                    default:
                        return $defaultValue;
                }
            } else {
                $value = null;
                $hasValue = false;

                foreach ($source as $element) {
                    if ($hasValue) {
                        return $defaultValue;
                    }
                    $value = $element;
                    $hasValue = true;
                }

                if ($hasValue) {
                    return $value;
                }
            }
        }
        return $defaultValue;
    }

    /**
     * @param integer $count
     * @return EnumerableInterface
     */
    public function skip($count)
    {
        if ($count <= 0) {
            return $this;
        }
        return new SkipIterator($this->getSource(), $count);
    }

    /**
     * @param integer $count
     * @return EnumerableInterface
     */
    public function skipLast($count)
    {
        if ($count <= 0) {
            return $this;
        }
        return new SkipLastIterator($this->getSource(), $count);
    }

    /**
     * @param callable $predicate
     * @return EnumerableInterface
     */
    public function skipWhile(callable $predicate)
    {
        return new SkipWhileIterator($this->getSource(), $predicate);
    }

    /**
     * @param mixed[] ...$elements
     * @return EnumerableInterface
     */
    public function startWith(...$elements)
    {
        return new StartWithIterator($this->getSource(), $elements);
    }

    /**
     * @param callable|null $selector
     * @return integer
     */
    public function sum(callable $selector = null)
    {
        $selector = $selector ?: $this->identityFunction();
        $sum = 0;
        foreach ($this->getSource() as $element) {
            $sum += $selector($element);
        }
        return $sum;
    }

    /**
     * @param integer $count
     * @return EnumerableInterface
     */
    public function take($count)
    {
        return new TakeIterator($this->getSource(), $count);
    }

    /**
     * @param integer $count
     * @return EnumerableInterface
     */
    public function takeLast($count)
    {
        return new TakeLastIterator($this->getSource(), $count);
    }

    /**
     * @param callable $predicate
     * @return EnumerableInterface
     */
    public function takeWhile(callable $predicate)
    {
        return new TakeWhileIterator($this->getSource(), $predicate);
    }

    /**
     * @return mixed[]
     */
    public function toArray()
    {
        return Converters::toArray($this->getSource());
    }

    /**
     * @param callable      $keySelector
     * @param callable|null $elementSelector
     * @return array
     */
    public function toDictionary(callable $keySelector, callable $elementSelector = null)
    {
        $elementSelector = $elementSelector ?: $this->identityFunction();
        return Converters::toDictionary($this->getSource(), $keySelector, $elementSelector);
    }

    /**
     * @param callable      $keySelector
     * @param callable|null $elementSelector
     * @return array
     */
    public function toLookup(callable $keySelector, callable $elementSelector = null)
    {
        $elementSelector = $elementSelector ?: $this->identityFunction();
        return Converters::toLookup($this->getSource(), $keySelector, $elementSelector);
    }

    /**
     * @return \Iterator
     */
    public function toIterator()
    {
        return Converters::toIterator($this->getSource());
    }

    /**
     * @param array|\Traversable             $second
     * @param EqualityComparerInterface|null $comparer
     * @return EnumerableInterface
     */
    public function union($second, EqualityComparerInterface $comparer = null)
    {
        $comparer = $comparer ?: EqualityComparer::getInstance();
        return new UnionIterator($this->getSource(), $second, $comparer);
    }

    /**
     * @param callable $predicate
     * @return EnumerableInterface
     */
    public function where(callable $predicate)
    {
        return new WhereIterator($this->getSource(), $predicate);
    }

    /**
     * @param callable $condition
     * @return EnumerableInterface
     */
    public function _while(callable $condition)
    {
        return new WhileIterator($this->getSource(), $condition);
    }

    /**
     * @param array|\Traversable $second
     * @param callable           $resultSelector
     * @return EnumerableInterface
     */
    public function zip($second, callable $resultSelector)
    {
        return new ZipIterator($this->getSource(), $second, $resultSelector);
    }

    /**
     * @return array|\Traversable
     */
    public function getSource()
    {
        return $this;
    }

    /**
     * @return callable
     */
    protected function identityFunction()
    {
        static $f;

        if (!isset($f)) {
            $f = function($x) {
                return $x;
            };
        }

        return $f;
    }
}
