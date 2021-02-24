<?php

namespace Differ\Formatters\stylish;

function stylish(array $data, int $dept): string
{
    $indent = str_repeat(" ", ($dept - 1) * 4);
    $result = array_reduce($data, function ($acc, $node) use ($dept, $indent): array {
        $type = $node['type'];
        $key = $node['key'];
        switch ($type) {
            case 'complex':
                $children = stylish($node['children'], $dept + 1);
                return [...$acc, "{$indent}    {$key}: {$children}"];
            case 'added':
                $formattedNewValue = prepareValue($node['newValue'], $dept);
                return [...$acc, "{$indent}  + {$key}: {$formattedNewValue}"];
            case 'removed':
                $formattedOldValue = prepareValue($node['newValue'], $dept);
                return [...$acc, "{$indent}  - {$key}: {$formattedOldValue}"];
            case 'not updated':
                $formattedNewValue = prepareValue($node['newValue'], $dept);
                return [...$acc, "{$indent}    {$key}: {$formattedNewValue}"];
            case 'updated':
                $formattedOldValue = prepareValue($node['oldValue'], $dept);
                $formattedNewValue = prepareValue($node['newValue'], $dept);
                $addedNode = "{$indent}  + {$key}: {$formattedNewValue}";
                $deletedNode = "{$indent}  - {$key}: {$formattedOldValue}";
                return [...$acc, implode("\n", [$deletedNode, $addedNode])];
            default:
                throw new \Exception("Invalid {$type}.");
        };
    }, []);

    $formatedData = implode("\n", $result);
    return "{\n{$formatedData}\n{$indent}}";
}

function prepareValue($value, int $dept): string
{
    if (is_bool($value)) {
        return $value ? 'true' : 'false';
    }

    if (is_null($value)) {
        return 'null';
    }

    if (is_array($value)) {
        $formatedData = implode(", ", $value);
        return "[{formatedData}]";
    }

    if (is_object($value)) {
        $keys = array_keys(get_object_vars($value));
        $indent = str_repeat(" ", 4 * $dept);

        $result = array_map(function ($key) use ($value, $dept, $indent): string {
            $childValue = prepareValue($value->$key, $dept + 1);
            return "{$indent}    {$key}: {$childValue}";
        }, $keys);

        $formatedData = implode("\n", $result);
        return "{\n{$formatedData}\n{$indent}}";
    }

    return "{$value}";
}

function format(array $data): string
{
    return stylish($data, 1);
}
