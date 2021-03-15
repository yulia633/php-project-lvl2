<?php

namespace Differ\Formatters\Stylish;

use function Funct\Collection\flattenAll;

function format(array $diff): string
{
    $iter = function (array $diff, int $depth) use (&$iter): array {
        return array_map(function ($node) use ($depth, $iter) {
            [
                'key' => $key,
                'type' => $type,
                'oldValue' => $oldValue,
                'newValue' => $newValue,
                'children' => $children
            ] = $node;

            $indent = makeIndent($depth - 1);

            switch ($type) {
                case 'complex':
                    $indentAfter = makeIndent($depth);
                    return ["{$indent}    {$key}: {", $iter($children, $depth + 1), "{$indentAfter}}"];
                case 'added':
                    $preparedNewValue = prepareValue($newValue, $depth);
                    return "{$indent}  + {$key}: {$preparedNewValue}";
                case 'removed':
                    $preparedOldValue = prepareValue($oldValue, $depth);
                    return "{$indent}  - {$key}: {$preparedOldValue}";
                case 'unchanged':
                    $preparedNewValue = prepareValue($newValue, $depth);
                    return "{$indent}    {$key}: {$preparedNewValue}";
                case 'updated':
                    $preparedOldValue = prepareValue($oldValue, $depth);
                    $preparedNewValue = prepareValue($newValue, $depth);
                    $addedLine = "{$indent}  + {$key}: {$preparedNewValue}";
                    $deletedLine = "{$indent}  - {$key}: {$preparedOldValue}";
                    return implode("\n", [$deletedLine, $addedLine]);
                default:
                    throw new \Exception("This type: {$type} is not supported.");
            };
        }, $diff);
    };
    return implode("\n", flattenAll(['{', $iter($diff, 1), '}']));
}

function prepareValue($value, int $depth): string
{
    if (is_bool($value)) {
        return $value ? 'true' : 'false';
    }
    if (is_null($value)) {
        return 'null';
    }
    if (!is_object($value)) {
        return $value;
    }

    $keys = array_keys(get_object_vars($value));
    $indent = makeIndent($depth);

    $lines = array_map(function ($key) use ($value, $depth, $indent): string {
        $childrenValue = prepareValue($value->$key, $depth + 1);
            return "{$indent}    {$key}: {$childrenValue}";
    }, $keys);

    $preparedValue = implode("\n", $lines);
    return "{\n{$preparedValue}\n{$indent}}";
}

function makeIndent(int $depth): string
{
    return str_repeat(" ", 4 * $depth);
}
