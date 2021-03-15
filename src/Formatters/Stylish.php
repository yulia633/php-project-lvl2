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
                    $formattedNewValue = prepareValue($newValue, $depth);
                    return "{$indent}  + {$key}: {$formattedNewValue}";
                case 'removed':
                    $formattedOldValue = prepareValue($oldValue, $depth);
                    return "{$indent}  - {$key}: {$formattedOldValue}";
                case 'unchanged':
                    $formattedNewValue = prepareValue($newValue, $depth);
                    return "{$indent}    {$key}: {$formattedNewValue}";
                case 'updated':
                    $formattedOldValue = prepareValue($oldValue, $depth);
                    $formattedNewValue = prepareValue($newValue, $depth);
                    $addedString = "{$indent}  + {$key}: {$formattedNewValue}";
                    $deletedString = "{$indent}  - {$key}: {$formattedOldValue}";
                    return implode("\n", [$deletedString, $addedString]);
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

    $result = array_map(function ($key) use ($value, $depth, $indent): string {
        $childValue = prepareValue($value->$key, $depth + 1);
            return "{$indent}    {$key}: {$childValue}";
    }, $keys);

    $formattedData = implode("\n", $result);
    return "{\n{$formattedData}\n{$indent}}";
}

function makeIndent(int $depth): string
{
    return str_repeat(" ", 4 * $depth);
}
