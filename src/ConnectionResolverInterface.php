<?php

namespace Starme\Laravel\Es;

interface ConnectionResolverInterface
{
    /**
     * Get a database connection instance.
     *
     * @param  string|null  $name
     * @return \Starme\Laravel\Es\ConnectionInterface
     */
    public function connection($name = null): ConnectionInterface;

    /**
     * Get the default connection name.
     *
     * @return string
     */
    public function getDefaultConnection(): string;

    /**
     * Set the default connection name.
     *
     * @param string $name
     * @return void
     */
    public function setDefaultConnection(string $name);

}