<?php

namespace App;

use Closure;
use Illuminate\Support\Arr;
use InvalidArgumentException;

/**
 * Class Permission
 *
 * Manage permissions in a hierarchical structure: Group > Resource > Action
 * Permission format: group:resource.resource...:action
 */
class Permission
{
    /**
     * Array containing all registered actions
     *
     * @var array<string>
     */
    protected static array $actionMap = [
        'browse' => 'Xem danh sách',
        'read' => 'Xem chi tiết',
        'create' => 'Tạo',
        'update' => 'Cập nhật',
        'delete' => 'Xóa',
        'clone' => 'Sao chép',
        // 'import' => 'Nhập',
        // 'export' => 'Xuất',
        // 'print' => 'In',
//        'restore' => 'Restore',
//        'forceDelete' => 'Permanently delete',
//        'approve' => 'Approve',
//        'reject' => 'Reject',
//        'upload' => 'Upload',
    ];

    /**
     * Array containing all registered permissions
     *
     * @var array<string>
     */
    protected static array $permissions = [];

    /**
     * Stack to track the current group and resource
     *
     * @var array<array{type: string, name: string, description: string}>
     */
    protected static array $groupStack = [];

    /**
     * Hierarchical structure of permissions
     *
     * @var array
     */
    protected static array $structure = [];

    /**
     * Define a permission group
     *
     * @param array<string, string> $attributes Array in the format ['name' => 'description']
     * @param Closure $callback Function containing child resources/actions
     * @return void
     */
    public static function group(array $attributes, Closure $callback): void
    {
        static::validateAttributesFormat($attributes);
        $name = array_key_first($attributes);
        $description = $attributes[$name];

        // Add group to structure
        static::$structure[$name] = [
            'type' => 'group',
            'name' => $name,
            'description' => $description,
            'actions' => [],
            'children' => [],
        ];

        // Add to stack
        static::$groupStack[] = [
            'type' => 'group',
            'name' => $name,
            'description' => $description
        ];

        // Execute callback
        $callback();

        // Remove from stack
        array_pop(static::$groupStack);
    }

    /**
     * Define a resource
     *
     * @param array<string, string> $attributes Array in the format ['name' => 'description']
     * @param array|Closure $callbackOrActions Function containing actions or array of action names
     * @param array<string, string> $customDescriptions Optional array of custom descriptions
     * @return void
     */
    public static function resource(array $attributes, array|Closure $callbackOrActions = [], array $customDescriptions = []): void
    {
        static::validateAttributesFormat($attributes);
        $name = array_key_first($attributes);
        $description = $attributes[$name];

        if (count(static::$groupStack) < 1) {
            throw new InvalidArgumentException('Resource must be defined inside a group or another resource');
        }

        // Add to stack
        static::$groupStack[] = [
            'type' => 'resource',
            'name' => $name,
            'description' => $description
        ];

        // Get current path for structure
        $currentPath = static::getCurrentStructurePath();

        // Initialize resource in structure
        Arr::set(static::$structure, $currentPath, [
            'type' => 'resource',
            'name' => $name,
            'description' => $description,
            'actions' => [],
            'children' => [],
        ]);

        // Execute callback or register actions
        if (is_array($callbackOrActions)) {
            static::actions(
                empty($callbackOrActions) ? collect(static::$actionMap)->keys()->all() : $callbackOrActions,
                $customDescriptions
            );
        } elseif ($callbackOrActions instanceof Closure) {
            $callbackOrActions();
        } else {
            throw new InvalidArgumentException('Second parameter must be a Closure or an array of action names');
        }

        // Remove from stack
        array_pop(static::$groupStack);
    }

    /**
     * Define an action on a resource
     *
     * @param string $action Action name
     * @param string $description Action description
     * @return void
     * @throws InvalidArgumentException
     */
    public static function action(string $action, string $description = ''): void
    {
        if (count(static::$groupStack) < 2) {
            throw new InvalidArgumentException('Action must be defined inside a resource');
        }

        // Validate action format
        if (!preg_match('/^[a-zA-Z0-9_-]+$/', $action)) {
            throw new InvalidArgumentException("Action name '$action' is invalid. Only letters, numbers, hyphens, and underscores are allowed");
        }

        // Create permission format
        $permission = static::buildPermissionString($action);
        static::$permissions[] = $permission;

        // Add action to structure
        $currentPath = static::getCurrentStructurePath();
        Arr::set(static::$structure, $currentPath . '.actions.' . $action, [
            'name' => $action,
            'description' => $description,
            'permission' => $permission,
            'type' => 'action',
        ]);
    }

    /**
     * Define multiple actions on a resource at once
     *
     * @param array<string> $actionNames Array of action names
     * @param array<string, string> $customDescriptions Optional array of custom descriptions
     * @return void
     * @throws InvalidArgumentException
     */
    public static function actions(array $actionNames, array $customDescriptions = []): void
    {
        if (count(static::$groupStack) < 2) {
            throw new InvalidArgumentException('Actions must be defined inside a resource');
        }

        $resourceDescription = end(static::$groupStack)['description'];

        foreach ($actionNames as $action) {
            // Get description from custom descriptions or use default from $actions array
            $description = $customDescriptions[$action] ?? (
                static::$actionMap[$action] ?? ''
            );

            // Replace placeholder in description if exists
            if (str_contains($description, ':resource')) {
                $description = str_replace(':resource', $resourceDescription, $description);
            }

            // Register the action
            static::action($action, $description);
        }
    }

    /**
     * Create a permission string from the current stack and action
     *
     * @param string $action Action name
     * @return string Permission string
     */
    protected static function buildPermissionString(string $action): string
    {
        $group = static::$groupStack[0]['name'];

        $resources = collect(static::$groupStack)
            ->slice(1)
            ->map(fn($item) => $item['name'])
            ->implode('.');

        if ($group === 'default') {
            return strtolower("{$resources}:{$action}");
        }

        return strtolower("{$group}:{$resources}:{$action}");
    }

    /**
     * Get the current path in the structure array
     *
     * @return string
     */
    protected static function getCurrentStructurePath(): string
    {
        $path = '';

        foreach (static::$groupStack as $index => $item) {
            if ($index === 0) {
                $path = $item['name'];
            } else {
                $path .= '.children.' . $item['name'];
            }
        }

        return $path;
    }

    /**
     * Validate the format of attributes
     *
     * @param array $attributes Attributes to validate
     * @return void
     * @throws InvalidArgumentException
     */
    protected static function validateAttributesFormat(array $attributes): void
    {
        if (count($attributes) !== 1) {
            throw new InvalidArgumentException('Attributes must have the format [\'name\' => \'description\']');
        }

        $name = array_key_first($attributes);

        // Validate name format
        if (!preg_match('/^[a-zA-Z0-9_-]+$/', $name)) {
            throw new InvalidArgumentException("Name '{$name}' is invalid. Only letters, numbers, hyphens, and underscores are allowed");
        }
    }

    /**
     * Get all registered permissions
     *
     * @return array<string> Array of permission strings
     */
    public static function all(): array
    {
        return static::$permissions;
    }

    /**
     * Get the hierarchical structure of permissions
     *
     * @return array Hierarchical structure
     */
    public static function structure(): array
    {
        return static::$structure;
    }

    /**
     * Clear all data
     *
     * @return void
     */
    public static function clear(): void
    {
        static::$permissions = [];
        static::$groupStack = [];
        static::$structure = [];
    }

    /**
     * Get or set the action map
     *
     * @return array<string>
     */
    public static function actionMap(?array $actionMap = null): array
    {
        if (is_null($actionMap)) {
            return static::$actionMap;
        }

        static::$actionMap = array_merge(static::$actionMap, $actionMap);

        return static::$actionMap;
    }

}
