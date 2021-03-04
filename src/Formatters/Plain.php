<?php

namespace Differ\Formatters\Plain;

use function Funct\Collection\flattenAll;

function generatePlain(array $data): string
{
    $iter = function ($data, $origin) use (&$iter): array {
        return array_map(function ($node) use ($origin, $iter) {
            $type = $node['type'];
            $pathToProperty = "{$origin}{$node['key']}";
            switch ($type) {
                case 'complex':
                    return $iter($node['children'], "{$pathToProperty}.");
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
        }, $data);
    };
    return implode("\n", flattenAll($iter($data, "")));
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

function format(array $data): string
{
    return generatePlain($data);
}
