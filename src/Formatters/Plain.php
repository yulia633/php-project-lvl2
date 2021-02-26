<?php

namespace Differ\Formatters\Plain;

function plain(array $data, string $propertyValue): string
{
    $result = array_reduce($data, function ($acc, $node) use ($propertyValue): array {
        $type = $node['type'];
        $key = $node['key'];
        $property = "{$propertyValue}{$key}";
        switch ($type) {
            case 'complex':
                return [...$acc, plain($node['children'], "{$property}.")];
            case 'added':
                $formattedNewValue = prepareValue($node['newValue']);
                return [...$acc, "Property '{$property}' was added with value: {$formattedNewValue}"];
            case 'removed':
                return [...$acc, "Property '{$property}' was removed"];
            case 'not updated':
                return [...$acc, []];
            case 'updated':
                $formattedOldValue = prepareValue($node['oldValue']);
                $formattedNewValue = prepareValue($node['newValue']);
                return [...$acc, "Property '{$property}' was updated. From {$formattedOldValue} to {$formattedNewValue}"];
            default:
                throw new \Exception("Invalid {$type}.");
        };
    }, []);
    return implode("\n", arrayFlatten($result));
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
    return plain($data, "");
}

function arrayFlatten(array $items): array
{
    return array_reduce($items, function ($acc, $item): array {
        if (is_array($item)) {
            return [...$acc, ...arrayFlatten($item)];
        };
        return [...$acc, $item];
    }, []);
}
