<?php

namespace Differ\Formatters\Plain;

use function Funct\Collection\flattenAll;

function format(array $diff): string
{
    $iter = function ($diff, $ancestors) use (&$iter): array {
        return array_map(function ($node) use ($ancestors, $iter) {
            [
                'key' => $key,
                'type' => $type,
                'oldValue' => $oldValue,
                'newValue' => $newValue,
                'children' => $children
            ] = $node;

            $pathToProperty = implode('.', [...$ancestors, $key]);

            switch ($type) {
                case 'complex':
                    return $iter($children, [...$ancestors, $key]);
                case 'added':
                    $preparedNewValue = prepareValue($newValue);
                    return "Property '{$pathToProperty}' was added with value: {$preparedNewValue}";
                case 'removed':
                    return "Property '{$pathToProperty}' was removed";
                case 'unchanged':
                    return [];
                case 'updated':
                    $preparedOldValue = prepareValue($oldValue);
                    $preparedNewValue = prepareValue($newValue);
                    return "Property '{$pathToProperty}' was updated. From {$preparedOldValue} to {$preparedNewValue}";
                default:
                    throw new \Exception("This type: {$type} is not supported.");
            }
        }, $diff);
    };
    return implode("\n", flattenAll($iter($diff, [])));
}

function prepareValue($value): string
{
    if (is_bool($value)) {
        return $value ? 'true' : 'false';
    }
    if (is_null($value)) {
        return 'null';
    }
    if (is_object($value)) {
        return '[complex value]';
    }
    if (is_string($value)) {
        return "'{$value}'";
    }
    return (string) $value;
}
