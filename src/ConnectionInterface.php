<?php
namespace Starme\Laravel\Es;

use Elasticsearch\ClientBuilder;
use Psr\Log\LoggerInterface;
use Starme\Laravel\Es\Query\Builder as QueryBuilder;
use Starme\Laravel\Es\Query\Grammar as QueryGrammar;
use Starme\Laravel\Es\Schema\Builder as SchemaBuilder;

interface ConnectionInterface
{

    /**
     * Set the query grammar to the default implementation.
     *
     * @return void
     */
    public function useDefaultQueryGrammar();

    /**
     * Set the query grammar to the default implementation.
     *
     * @return void
     */
    public function useDefaultSchemaGrammar();

    /**
     * Set the es client to the default implementation.
     *
     * @return void
     */
    public function useDefaultClient();

    /**
     * Get a schema builder instance for the connection.
     *
     * @return \Starme\Laravel\Es\Schema\Builder
     */
//    public function getSchemaBuilder()
//    {
//        if (is_null($this->schemaGrammar)) {
//            $this->useDefaultSchemaGrammar();
//        }
//
//        return new SchemaBuilder($this);
//    }

    /**
     * Get a schema builder instance for the connection.
     *
     * @param $table
     * @return \Starme\Laravel\Es\Query\Builder
     */
    public function table($table): QueryBuilder;

    /**
     * Get a new query builder instance.
     *
     * @return \Starme\Laravel\Es\Query\Builder
     */
    public function query(): QueryBuilder;

    /**
     * Run a select statement against the elasticsearch.
     *
     * @params array $params
     * @throws \Starme\Laravel\Es\Exceptions\QueryException
     */
    public function select(array $params);

    /**
     * Run a insert statement against the elasticsearch.
     *
     * @params array $params
     * @throws \Starme\Laravel\Es\Exceptions\QueryException
     */
    public function insert(array $params);

    /**
     * Run a update statement against the elasticsearch.
     *
     * @params array $params
     * @throws \Starme\Laravel\Es\Exceptions\QueryException
     */
    public function update(array $params, $by_query=false);

    /**
     * Run a delete statement against the elasticsearch.
     *
     * @params array $params
     * @throws \Starme\Laravel\Es\Exceptions\QueryException
     */
    public function delete(array $params);


    public function getQueryGrammar(): QueryGrammar;

}