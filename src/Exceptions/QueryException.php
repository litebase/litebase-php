<?php

namespace Litebase\Exceptions;

use Exception;
use Throwable;

class QueryException extends Exception
{
    /**
     * The parameters of the query.
     *
     * @var array
     */
    protected $parameters;

    /**
     * The statement of the query.
     *
     * @var string
     */
    protected $statement;

    /**
     * Create a new QueryException instance.
     */
    public function __construct(string $message, string $statement, array $parameters, Throwable $previous = null)
    {
        $this->statement = $statement;
        $this->parameters = $parameters;

        parent::__construct($message, 0, $previous);
    }
}
