<?php

namespace Ziro\Entity;

use ZQuery\Query\QueryBuilder;

class User extends Model
{
    private const TABLE = 'users';
    private const PRIMARY_KEY = 'id';

    public static function query(): QueryBuilder
    {
        return static::zquery()->table(self::TABLE);
    }

    public static function find(int $id): ?array
    {
        return static::query()
            ->where(self::PRIMARY_KEY, '=', $id)
            ->first();
    }

    public static function all(): array
    {
        return static::query()->get();
    }

    public static function latest(int $limit = 10): array
    {
        return static::query()
            ->latest('created_at')
            ->limit($limit)
            ->get();
    }
}
