<?php

declare(strict_types=1);

namespace MergeInc\Sort\Dependencies\DI;

use MergeInc\Sort\Dependencies\Psr\Container\NotFoundExceptionInterface;

/**
 * Exception thrown when a class or a value is not found in the container.
 */
class NotFoundException extends \Exception implements NotFoundExceptionInterface
{
}
