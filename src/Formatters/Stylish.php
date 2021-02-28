<?php

namespace Differ\Formatters\Stylish;

use function Funct\Collection\flattenAll;

function generateStylish(array $data): string
{
    $diffStylish = function (array $data, int $depth) use (&$diffStylish): array {
        return array_map(function ($node) use ($depth, $diffStylish) {
            $indent = makeIndent($depth - 1);
            [$type, $key] = [$node['type'], $node['key']];
            switch ($type) {
                case 'complex':
                    return [
                        makeIndent($depth) . "{$key}: {",
                        $diffStylish($node['children'], $depth + 1),
                        makeIndent($depth) . "}"
                    ];
                case 'added':
                    $formattedNewValue = prepareValue($node['newValue'], $depth);
                    return "{$indent}  + {$key}: {$formattedNewValue}";
                case 'removed':
                    $formattedOldValue = prepareValue($node['newValue'], $depth);
                    return "{$indent}  - {$key}: {$formattedOldValue}";
                case 'unchanged':
                    $formattedNewValue = prepareValue($node['newValue'], $depth);
                    return "{$indent}    {$key}: {$formattedNewValue}";
                case 'updated':
                    $formattedOldValue = prepareValue($node['oldValue'], $depth);
                    $formattedNewValue = prepareValue($node['newValue'], $depth);
                    $addedString = "{$indent}  + {$key}: {$formattedNewValue}";
                    $deletedString = "{$indent}  - {$key}: {$formattedOldValue}";
                    return implode("\n", [$deletedString, $addedString]);
                default:
                    throw new \Exception("This type: {$type} is not supported.");
            };
        }, $data);
    };
    $formattedData = array_merge(['{'], flattenAll($diffStylish($data, 1)), ['}']);
    $formattedString = implode("\n", $formattedData);
    return "{$formattedString}";
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
    return generateStylish($data);
}
