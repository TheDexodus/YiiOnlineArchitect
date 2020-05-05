<?php

declare(strict_types=1);

namespace app\models;

/**
 * Interface ImportableInterface
 */
interface ImportableInterface
{
    /**
     * @return array
     */
    public function getRelationMaps(): array;
}
