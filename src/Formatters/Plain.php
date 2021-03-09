<?php

namespace Differ\Formatters\Plain;

use function Funct\Collection\flattenAll;

function format(array $diff): string
{
    $iter = function ($diff, $ancestors) use (&$iter): array {
        return array_map(function ($node) use ($ancestors, $iter) {
            [$type, $key] = [$node['type'], $node['key']];
            $pathToProperty = implode('.', array_filter([$ancestors, $key]));
            switch ($type) {
                case 'complex':
                    return $iter($node['children'], $pathToProperty);
                case 'added':
                    $formattedNewValue = prepareValue($node['newValue']);
                    return "Property '{$pathToProperty}' was added with value: {$formattedNewValue}";
                case 'removed':
                    return "Property '{$pathToProperty}' was removed";
                case 'unchanged':
                    return [];
                case 'updated':
                    $formattedOldValue = prepareValue($node['oldValue']);
                    $formattedNewValue = prepareValue($node['newValue']);
                    return "Property '{$pathToProperty}' was updated. From {$formattedOldValue} to {$formattedNewValue}";
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

    if (is_array($value) || is_object($value)) {
        return '[complex value]';
    }

    if (is_string($value)) {
        return "'{$value}'";
    }

    return "{$value}";
}
