<?php
namespace Vimeo\MysqlEngine\Processor;

class Scope
{
    /**
     * @var array<string, mixed>
     */
    public $variables = [];

    /**
     * @var array<string|int, mixed>
     */
    public $parameters = [];

    /** @param array<string, mixed> $variables */
    public function __construct(array $parameters, array $variables = [])
    {
        $this->parameters = $parameters;
        $this->variables = $variables;
    }
}
