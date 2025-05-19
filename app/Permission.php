<?php

namespace App;

use Closure;
use Illuminate\Support\Arr;
use InvalidArgumentException;

class Permission
{
    protected static array $permissions = [];
    protected static array $groupStack = [];
    protected static array $structure = [];
    protected static string $currentType = '';

    public static function group(array $attributes, Closure $callback): void
    {
        static::addToStack('group', $attributes);
        $callback();
        array_pop(static::$groupStack);
    }

    public static function resource(array $attributes, Closure $callback): void
    {
        static::addToStack('resource', $attributes);
        $callback();
        array_pop(static::$groupStack);
    }

    public static function action(string $action, string $description = ''): void
    {
        if (count(static::$groupStack) < 2) {
            throw new InvalidArgumentException('Action must be inside a resource');
        }

        $group = static::$groupStack[0]['name'];
        $resources = collect(static::$groupStack)
            ->slice(1)
            ->map(fn($item) => $item['name'])
            ->implode('.');

        static::$permissions[] = "{$group}:{$resources}:{$action}";

        $path = static::getCurrentPath();
        Arr::set(static::$structure, $path . '.actions.' . $action, $description);
    }

    protected static function addToStack(string $type, array $attributes): void
    {
        $name = array_key_first($attributes);
        $description = $attributes[$name];

        static::$groupStack[] = [
            'type' => $type,
            'name' => $name,
            'description' => $description
        ];

        $path = static::getCurrentPath();
        if ($path) {
            Arr::set(static::$structure, $path, [
                'type' => $type,
                'name' => $name,
                'description' => $description,
                'actions' => [],
                'children' => []
            ]);
        }
    }

    protected static function getCurrentPath(): string
    {
        if (empty(static::$groupStack)) {
            return '';
        }

        return collect(static::$groupStack)
            ->map(fn($item) => $item['name'])
            ->implode('.children.');
    }

    public static function all(): array
    {
        return static::$permissions;
    }

    public static function structure(): array
    {
        return static::$structure;
    }

    public static function clear(): void
    {
        static::$permissions = [];
        static::$groupStack = [];
        static::$structure = [];
    }
}
