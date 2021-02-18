<?php

namespace Differ\Formatters\plain;

function plain($data, $propertyValue)
{
    $result = array_reduce($data, function ($acc, $node) use ($propertyValue) {
        $type = $node['type'];
        $key = $node['key'];
        $property = "{$propertyValue}{$key}";
        switch ($type) {
            case 'complex':
                $acc[] = plain($node['children'], "{$property}.");
                break;
            case 'added':
                $formattedNewValue = prepareValue($node['newValue']);
                $acc[] = "Property '{$property}' was added with value: {$formattedNewValue}";
                break;
            case 'removed':
                $formattedOldValue = prepareValue($node['newValue']);
                $acc[] = "Property '{$property}' was removed";
                break;
            case 'not updated':
                $acc[] = [];
                break;
            case 'updated':
                $formattedOldValue = prepareValue($node['oldValue']);
                $formattedNewValue = prepareValue($node['newValue']);
                $acc[] = "Property '{$property}' was updated. From {$formattedOldValue} to {$formattedNewValue}";
                break;
            default:
                throw new \Exception("Invalid {$type}.");
        };
        return $acc;
    }, []);
    return implode("\n", arrayFlatten($result));
}

function prepareValue($value)
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

    return $value;
}

function format($data)
{
    return plain($data, "");
}

function arrayFlatten($tree, $depth = 0)
{
    $result = [];
    foreach ($tree as $key => $value) {
        if ($depth >= 0 && is_array($value)) {
            $value = arrayFlatten($value, $depth > 1 ? $depth - 1 : 0 - $depth);
            $result = array_merge($result, $value);
        } else {
            $result[] = $value;
        }
    }
    return $result;
}
