<?php

namespace Starme\LaravelEs\Exceptions;

use Throwable;

class ParamsExcption extends \Exception
{
    /**
     * The SQL for the query.
     *
     * @var string
     */
    protected $code = 400;

}
