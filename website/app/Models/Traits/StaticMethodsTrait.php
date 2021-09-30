<?php

namespace App\Models\Traits;

trait StaticMethodsTrait
{

    /**
     * Get the table name associated to model
     *
     * @return string
     */
    public static function getMetaTableName(): string
    {
        $originClass = static::class;

        return (new $originClass)->getTable();
    }

    /**
     * Get the primary key name associated to model
     *
     * @return string
     */
    public static function getMetaPrimaryKeyName(): string
    {
        $originClass = static::class;

        return (new $originClass)->getKeyName();
    }

    public static function fqcnColumnName(string $columnName, bool $withBackTicks = false): string
    {
        return $withBackTicks ?
            '`' . static::getMetaTableName() . '`.`'. $columnName . '`'
            : static::getMetaTableName() . '.'. $columnName;
    }
}
