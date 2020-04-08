<?php

declare(strict_types=1);

namespace Emonkak\Enumerable\Iterator;

use Emonkak\Enumerable\EnumerableExtensions;
use Emonkak\Enumerable\EnumerableInterface;

/**
 * @implements EnumerableInterface<mixed>
 */
class EmptyIterator extends \EmptyIterator implements EnumerableInterface
{
    /**
     * @use EnumerableExtensions<mixed>
     */
    use EnumerableExtensions;
}
