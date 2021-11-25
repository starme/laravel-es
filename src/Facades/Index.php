<?php

namespace Starme\LaravelEs\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static array create(string $index, \Closure $callback)
 * @method static bool exists(string $index)
 * @method static array drop(string $index)
 * @method static array dropIfExists(string $index)
 *
 * @method static array alias(string $index, string $alias)
 * @method static bool existsAlias(string $index, string $alias)
 * @method static array dropAlias(string $index, string $alias)
 * @method static array getAlias(string $alias)
 * @method static array getIndexAlias(string $index)
 * @method static array toggleAlias(string $alias, string $oldIndex, string $newIndex)
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
