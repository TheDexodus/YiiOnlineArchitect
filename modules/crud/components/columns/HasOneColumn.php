<?php

declare(strict_types=1);

namespace crud\components\columns;

/**
 * Class HasOneColumn
 */
class HasOneColumn extends AbstractRelationColumn
{
    public const METHOD_CREATE = 'create';
    public const METHOD_SELECT = 'select';

    /**
     * @var string
     */
    protected $method = self::METHOD_CREATE;

    /**
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * @param string $method
     */
    public function setMethod(string $method): void
    {
        $this->method = $method;
    }
}
