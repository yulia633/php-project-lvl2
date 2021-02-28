<?php

namespace Differ\Formatters\Stylish;

function generateStylish(array $data, int $depth): string
{
    $indent = makeIndent($depth - 1);
    $diffStylish = array_reduce($data, function ($acc, $node) use ($depth, $indent): array {
        [$type, $key] = [$node['type'], $node['key']];
        switch ($type) {
            case 'complex':
                $children = generateStylish($node['children'], $depth + 1);
                return [...$acc, "{$indent}    {$key}: {$children}"];
            case 'added':
                $formattedNewValue = prepareValue($node['newValue'], $depth);
                return [...$acc, "{$indent}  + {$key}: {$formattedNewValue}"];
            case 'removed':
                $formattedOldValue = prepareValue($node['newValue'], $depth);
                return [...$acc, "{$indent}  - {$key}: {$formattedOldValue}"];
            case 'unchanged':
                $formattedNewValue = prepareValue($node['newValue'], $depth);
                return [...$acc, "{$indent}    {$key}: {$formattedNewValue}"];
            case 'updated':
                $formattedOldValue = prepareValue($node['oldValue'], $depth);
                $formattedNewValue = prepareValue($node['newValue'], $depth);
                $addedString = "{$indent}  + {$key}: {$formattedNewValue}";
                $deletedString = "{$indent}  - {$key}: {$formattedOldValue}";
                return [...$acc, implode("\n", [$deletedString, $addedString])];
            default:
                throw new \Exception("This type: {$type} is not supported.");
        };
    }, []);

    $formattedString = implode("\n", $diffStylish);
    return "{\n{$formattedString}\n{$indent}}";
}

function prepareValue($value, int $depth): string
{
    if (is_bool($value)) {
        return $value ? 'true' : 'false';
    }

    if (is_null($value)) {
        return 'null';
    }

    if (is_array($value)) {
        $keys = array_keys($value);
        $indent = makeIndent($depth);

        $result = array_map(function ($key) use ($value, $depth, $indent): string {
            $childValue = prepareValue($value[$key], $depth + 1);
                return "{$indent}    {$key}: {$childValue}";
        }, $keys);

        $formattedData = implode("\n", $result);
        return "{\n{$formattedData}\n{$indent}}";
    }
    return $value;
}

function makeIndent(int $depth): string
{
    return str_repeat(" ", 4 * $depth);
}

function format(array $data): string
{
    return generateStylish($data, 1);
}
