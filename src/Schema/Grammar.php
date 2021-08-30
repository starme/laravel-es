<?php
namespace Starme\LaravelEs\Schema;


class Grammar
{

    protected $settings = [];
    protected $columns = [];

    public function compilePutTemplate($blueprint, $command): array
    {
        return [
            'name' => $blueprint->getTable(),
            'mappings' => [
                '_source' => [
                    'enabled' => false,
                ],
                'properties' => $this->getColumns($blueprint)
            ]
        ];
    }

    public function compileTemplateOrder($blueprint, $command): array
    {
        return [
            'order' => $command->order
        ];
    }

    public function compileTemplateMatch($blueprint, $command): array
    {
        return [
            'index_patterns' => $command->index_patterns
        ];
    }

    public function compileCreateAlias($blueprint, $command): array
    {
        return ['index' => $blueprint->getTable(), 'name' => $command->alias];
    }

    public function compileExistsAlias($blueprint, $command): array
    {
        return ['index' => $blueprint->getTable(), 'name' => $command->alias];
    }

    public function compileDropAlias($blueprint, $command): array
    {
        return ['index' => $blueprint->getTable(), 'name' => $command->alias];
    }

    public function compileGetAlias($blueprint, $command): array
    {
        return ['name' => $command->alias];
    }

    public function compileGetIndexAlias($blueprint, $command): array
    {
        return ['index' => $blueprint->getTable()];
    }

    public function compileCreateIndex($blueprint, $command): array
    {
        ///, 'mappings' => ['properties' => $this->getColumns($blueprint)]
        return ['index' => $blueprint->getTable()];
    }

    public function compileExistsIndex($blueprint, $command): array
    {
        return ['index' => $blueprint->getTable()];
    }

    public function compileDropIndex($blueprint, $command): array
    {
        return ['index' => $blueprint->getTable()];
    }

    public function compileCloneIndex($blueprint, $command): array
    {
        return ['index' => $blueprint->getTable(), 'target'=>$command->target];
    }

    public function compileIndex($blueprint, $command): array
    {
        return ['index' => $blueprint->getTable()];
    }

    public function compileSetting($blueprint, $command): array
    {
        $this->settings = array_merge($this->settings, $this->warpCommand($command));
        return ['settings' => ['index' => $this->settings]];
    }

    public function compileColumns($blueprint, $command): array
    {
        $this->columns = array_merge($this->columns, $this->warpColumns($command));
        return ['mappings' => ['properties' => $this->columns]];
    }

//    protected function getColumns($blueprint): array
//    {
//        if ($blueprint->getColumns()) {
//            return array_merge(...array_map([$this, 'warp'], $blueprint->getColumns()));
//        }
//        return [];
//    }

    protected function warpCommand($command): array
    {
        return [$command->type => $command->value];
    }

    protected function warpColumns($command): array
    {
        return [$command->column => array_diff_key($command->toArray(), array_flip(['column', 'name']))];
    }

//    public function warp($value): array
//    {
//        return [$value->column => array_diff_key($value->toArray(), array_flip(['column', 'name']))];
//    }

}
