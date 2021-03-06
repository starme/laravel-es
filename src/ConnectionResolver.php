<?php

namespace Starme\LaravelEs;

use Illuminate\Support\Arr;
use InvalidArgumentException;

class ConnectionResolver implements ConnectionResolverInterface
{

    protected $app;

    /**
     * All of the registered connections.
     *
     * @var array
     */
    protected $connections = [];

    /**
     * The default connection name.
     *
     * @var string
     */
    protected $default = 'default';

    /**
     * The reconnector instance for the connection.
     *
     * @var callable
     */
    protected $reconnector;

    /**
     * Create a new connection resolver instance.
     *
     * @param $app
     * @return void
     */
    public function __construct($app)
    {
        $this->app = $app;

        $this->reconnector = function ($connection) {
            $this->reconnect($connection->getName());
        };
    }

    /**
     * Get a database connection instance.
     *
     * @param  string|null  $name
     * @return \Starme\LaravelEs\ConnectionInterface
     */
    public function connection($name = null): ConnectionInterface
    {
        if (is_null($name)) {
            $name = $this->getDefaultConnection();
        }

        if (! isset($this->connections[$name])) {
            $this->connections[$name] = $this->makeConnection($name);
        }

        return $this->connections[$name];
    }

    /**
     * Add a connection to the resolver.
     *
     * @param string $name
     * @param \Starme\LaravelEs\ConnectionInterface $connection
     * @return void
     */
    public function addConnection(string $name, ConnectionInterface $connection)
    {
        $this->connections[$name] = $connection;
    }

    /**
     * Check if a connection has been registered.
     *
     * @param string $name
     * @return bool
     */
    public function hasConnection(string $name): bool
    {
        return isset($this->connections[$name]);
    }

    /**
     * Disconnect from the given database.
     *
     * @param  string|null  $name
     * @return void
     */
    public function disconnect($name = null)
    {
        if ($this->hasConnection($name = $name ?: $this->getDefaultConnection())) {
            $this->connections[$name]->disconnect();
        }
    }

    /**
     * Reconnect to the given database.
     *
     * @param  string|null  $name
     * @return \Starme\LaravelEs\ConnectionInterface
     */
    public function reconnect($name = null): ConnectionInterface
    {
        $this->disconnect($name = $name ?: $this->getDefaultConnection());

        if (! isset($this->connections[$name])) {
            return $this->connection($name);
        }

        return $this->refreshConnections($name);
    }

    /**
     * Refresh the PDO connections on a given connection.
     *
     * @param string $name
     * @return \Starme\LaravelEs\Connection
     */
    protected function refreshConnections(string $name): Connection
    {
        $fresh = $this->makeConnection($name);

        return $this->connections[$name]->setClient($fresh->getClient());
    }

    /**
     * Get the default connection name.
     *
     * @return string
     */
    public function getDefaultConnection(): string
    {
        return $this->default;
    }

    /**
     * Set the default connection name.
     *
     * @param string $name
     * @return void
     */
    public function setDefaultConnection(string $name)
    {
        $this->default = $name;
    }

    /**
     * Make the database connection instance.
     *
     * @param string $name
     * @return \Starme\LaravelEs\Connection
     */
    protected function makeConnection(string $name): Connection
    {
        $config = $this->configuration($name);

        $connection = new Connection(
            $config,
            $this->app['log']->channel($config['logger'])
        );
        return $connection->setEvents($this->app['events'])->setReconnector($this->reconnector);
    }

    /**
     * Get the configuration for a connection.
     *
     * @param string $name
     * @return array
     *
     * @throws \InvalidArgumentException
     */
    protected function configuration(string $name): array
    {
        $name = $name ?: $this->getDefaultConnection();

        // To get the database connection configuration, we will just pull each of the
        // connection configurations and get the configurations for the given name.
        // If the configuration doesn't exist, we'll throw an exception and bail.
        $connections = $this->app['config']['es.connections'];

        if (is_null($config = Arr::get($connections, $name))) {
            throw new InvalidArgumentException("Database connection [{$name}] not configured.");
        }

        return array_merge(
            $config, Arr::except($this->app['config']['es'], ['default', 'connections']), compact('name')
        );
    }

    /**
     * Prepare the database connection instance.
     *
     * @param \Starme\LaravelEs\Connection $connection
     * @param string $type
     * @return \Starme\LaravelEs\Connection
     */
    protected function configure(Connection $connection, string $type): Connection
    {
        return $connection;
    }

    /**
     * Dynamically pass methods to the default connection.
     *
     * @param string $method
     * @param array $parameters
     * @return mixed
     */
    public function __call(string $method, array $parameters)
    {
        return $this->connection()->$method(...$parameters);
    }

}
