<?php

declare(strict_types=1);

namespace crud\components\helpers;

/**
 * Class NameHelper
 */
class NameHelper
{
    /**
     * @param string $columnName
     *
     * @return string
     */
    public static function getColumnTitle(string $columnName): string
    {
        return implode(' ', array_map('ucfirst', explode('_', $columnName)));
    }

    /**
     * @param string $columnName
     *
     * @return string
     */
    public static function getRelationColumnName(string $columnName): string
    {
        $parts = explode('_', $columnName);

        return array_shift($parts).implode(
                '',
                array_map(
                    function ($part) {
                        return ucfirst($part);
                    },
                    $parts
                )
            );
    }

    /**
     * @param string $role
     *
     * @return string
     */
    public static function getRoleName(string $role): string
    {
        return strtoupper($role);
    }
}
