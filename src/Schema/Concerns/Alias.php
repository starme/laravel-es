<?php
namespace Starme\LaravelEs\Schema\Concerns;

use Starme\LaravelEs\Schema\Blueprint;

trait Alias
{
    /**
     *
     * @param string $table
     * @param string $alias
     * @return void
     */
    public function alias(string $table, string $alias)
    {
        $body = $this->build(tap($this->createBlueprint($table), function ($blueprint) use ($alias) {
            $blueprint->alias($alias);
        }));

        return $this->connection->alias('put', $body);
    }

    /**
     * Alias is exists.
     *
     * @param string $table
     * @param string $alias
     * @return void
     */
    public function existsAlias(string $table, string $alias)
    {
        $body = $this->build(tap($this->createBlueprint($table), function ($blueprint) use ($alias) {
            $blueprint->existsAlias($alias);
        }));

        return $this->connection->alias('exists', $body);
    }

    /**
     * Drop alias of the index.
     *
     * @param string $table
     * @param string $alias
     * @return void
     */
    public function dropAlias(string $table, string $alias)
    {
        $body = $this->build(tap($this->createBlueprint($table), function (Blueprint $blueprint) use ($alias) {
            $blueprint->dropAlias($alias);
        }));

        return $this->connection->alias('delete', $body);
    }

    /**
     * Index under alias.
     *
     * @param string $alias
     * @return void
     */
    public function getAlias(string $alias)
    {
        $body = $this->build(tap($this->createBlueprint(''), function ($blueprint) use ($alias) {
            $blueprint->getAlias($alias);
        }));

        return $this->connection->alias('get', $body);
    }

    /**
     * Get aliases of index name.
     *
     * @param string $table
     * @return void
     */
    public function getIndexAlias(string $table)
    {
        $body = $this->build(tap($this->createBlueprint($table), function ($blueprint) {
            $blueprint->getIndexAlias();
        }));

        return $this->connection->alias('get', $body);
    }

    /**
     * Toggle alias of old index to new index. (old->new)
     *
     * @param string $alias
     * @param string $old old index name.
     * @param string $new new index name.
     * @return void
     */
    public function toggleAlias(string $alias, string $old, string $new)
    {
        $blueprint = $this->createBlueprint($old);
        $body['actions'][]['remove'] = [
            'index' => $blueprint->getTab∂le(), 'alias' => $alias
        ];

        $blueprint = $this->createBlueprint($new);
        $body['actions'][]['add'] = [
            'index' => $blueprint->getTable(), 'alias' => $alias
        ];

        return $this->connection->alias('toggle', compact('body'));
    }

}
