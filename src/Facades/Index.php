<?php

namespace Starme\LaravelEs\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static void create(string $index, \Closure $callback)
 * @method static void exists(string $index)
 * @method static void drop(string $index)
 * @method static void dropIfExists(string $index)
 *
 * @method static void alias(string $index, string $alias)
 * @method static void existsAlias(string $index, string $alias)
 * @method static void dropAlias(string $index, string $alias)
 * @method static array getAlias(string $alias)
 * @method static array getIndexAlias(string $index)
 * @method static void toggleAlias(string $alias, string $oldIndex, string $newIndex)
 *
 * @see \Starme\LaravelEs\ConnectionResolver
 * @see \Starme\LaravelEs\Connection
 */
class Index extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'es.schema';
    }
}
