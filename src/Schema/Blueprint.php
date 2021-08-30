<?php

namespace Starme\LaravelEs\Schema;

use Closure;
use Illuminate\Support\Fluent;

class Blueprint
{

    use Concerns\Columns;
    
    /**
     * The table the blueprint describes.
     *
     * @var string
     */
    protected $table;

    /**
     * The prefix of the table.
     *
     * @var string
     */
    protected $prefix;

    /**
     * The commands that should be run for the table.
     *
     * @var \Illuminate\Support\Fluent[]
     */
    protected $commands = [];

    /**
     * The collation that should be used for the table.
     *
     * @var string
     */
    public $collation;

    /**
     * Create a new schema blueprint.
     *
     * @param string $table
     * @param  \Closure|null  $callback
     * @param  string  $prefix
     * @return void
     */
    public function __construct(string $table, Closure $callback = null, $prefix = '')
    {
        $this->prefix = $prefix;
        $this->table = $table;

        if (! is_null($callback)) {
            $callback($this);
        }
    }

    public function build($grammar): array
    {
        $statements = [];

//        dd($this->commands);
        foreach ($this->commands as $command) {
            $method = 'compile' . ucfirst($command->name);

            if (method_exists($grammar, $method)) {
                if (! is_null($sql = $grammar->$method($this, $command))) {
                    $statements = array_merge($statements, (array) $sql);
                }
            }
        }
        dd($statements);
        return $statements;
    }

    public function index()
    {
        return $this->addCommand('Index');
    }

    /**
     * Add create index command.
     *
     * @return Fluent
     */
    public function createIndex()
    {
        return $this->addCommand('CreateIndex');
    }

    public function existsIndex()
    {
        return $this->addCommand('ExistsIndex');
    }

    public function dropIndex()
    {
        return $this->addCommand('DropIndex');
    }

    public function cloneIndex(string $target)
    {
        return $this->addCommand('CloneIndex', ['target'=>$target]);
    }

    /**
     * @return \Illuminate\Support\Fluent
     */
    public function putTemplate(): Fluent
    {
        return $this->addCommand('PutTemplate');
    }

    /**
     * @param $name
     * @return \Illuminate\Support\Fluent
     */
    public function alias($name): Fluent
    {
        return $this->addCommand('CreateAlias', ['alias'=>$name]);
    }

    /**
     * @param $name
     * @return Fluent
     */
    public function existsAlias($name): Fluent
    {
        return $this->addCommand('ExistsAlias', ['alias'=>$name]);
    }

    /**
     * @param $name
     * @return Fluent
     */
    public function dropAlias($name): Fluent
    {
        return $this->addCommand('DropAlias', ['alias'=>$name]);
    }

    /**
     * @param $name
     * @return Fluent
     */
    public function getAlias($name): Fluent
    {
        return $this->addCommand('GetAlias', ['alias'=>$name]);
    }

    /**
     * @return Fluent
     */
    public function getIndexAlias(): Fluent
    {
        return $this->addCommand('GetIndexAlias');
    }

    public function order(int $number)
    {
        return $this->addCommand('TemplateOrder', ['order'=>$number]);
    }

    public function index_patterns(string $match)
    {
        return $this->addCommand('TemplateMatch', ['index_patterns'=>$match]);
    }

    /**
     * Specify shards number for the index.
     *
     * @param int $number
     * @return \Illuminate\Support\Fluent
     */
    public function shards(int $number): Fluent
    {
        return $this->settingCommand('number_of_shards', $number);
    }

    /**
     * Specify shards number for the index.
     *
     * @param int $number
     * @return \Illuminate\Support\Fluent
     */
    public function replicas(int $number): Fluent
    {
        return $this->settingCommand('number_of_replicas', $number);
    }

    /**
     * Specify max result window for the index.
     *
     * @param int $number
     * @return \Illuminate\Support\Fluent
     */
    public function results(int $number): Fluent
    {
        return $this->settingCommand('max_result_window', $number);
    }

    /**
     * Specify refresh interval for the index.
     *
     * @param int $number
     * @return \Illuminate\Support\Fluent
     */
    public function refreshInterval(int $number): Fluent
    {
        return $this->settingCommand('refresh_interval', $number);
    }


    /**
     * Add a new setting command to the blueprint.
     *
     * @param string $type
     * @param  string|array  $value
     * @return \Illuminate\Support\Fluent
     */
    protected function settingCommand(string $type, $value): Fluent
    {
        return $this->addCommand(
            'setting', compact('type', 'value')
        );
    }

    /**
     * Add a new command to the blueprint.
     *
     * @param string $name
     * @param array|object $command
     * @return \Illuminate\Support\Fluent
     */
    protected function addCommand(string $name, $command=[]): Fluent
    {
        $this->commands[] = $command = $this->createCommand($name, $command);;
        return $command;
    }

    /**
     * Create a new Fluent command.
     *
     * @param string $name
     * @param array|object $command
     * @return \Illuminate\Support\Fluent
     */
    protected function createCommand(string $name, $command = []): Fluent
    {
        if ($command instanceof Fluent) {
            return $command->name($name);
        }
        return new Fluent(array_merge(compact('name'), $command));
    }

    /**
     * Get the table the blueprint describes.
     *
     * @return string
     */
    public function getTable(): string
    {
        return $this->prefix . $this->table;
    }

    /**
     * Get the columns on the blueprint.
     *
     * @return \Starme\LaravelEs\Schema\ColumnDefinition[]
     */
    public function getColumns(): array
    {
        return $this->columns;
    }

    /**
     * Get the commands on the blueprint.
     *
     * @return \Illuminate\Support\Fluent[]
     */
    public function getCommands(): array
    {
        return $this->commands;
    }

}
