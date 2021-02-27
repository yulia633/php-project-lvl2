<?php

namespace Differ\Formatters\Plain;

function generatePlane(array $data, string $origin): string
{
    $diffPlane = array_reduce($data, function ($acc, $node) use ($origin): array {
        [$type, $key] = [$node['type'], $node['key']];
        $property = "{$origin}{$key}";
        switch ($type) {
            case 'complex':
                return [...$acc, generatePlane($node['children'], "{$property}.")];
            case 'added':
                $formattedNewValue = prepareValue($node['newValue']);
                return [...$acc, "Property '{$property}' was added with value: {$formattedNewValue}"];
            case 'removed':
                return [...$acc, "Property '{$property}' was removed"];
            case 'unchanged':
                return [...$acc, []];
            case 'updated':
                $formattedOldValue = prepareValue($node['oldValue']);
                $formattedNewValue = prepareValue($node['newValue']);
                return [...$acc, "Property '{$property}' was updated. From {$formattedOldValue} to {$formattedNewValue}"];
            default:
                throw new \Exception("This type: {$type} is not supported.");
        };
    }, []);
    return implode("\n", arrayFlatten($diffPlane));
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
    return generatePlane($data, "");
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
