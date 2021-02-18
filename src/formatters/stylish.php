<?php

namespace Differ\Formatters\stylish;

function stylish($data, $dept)
{
    $indent = str_repeat(" ", ($dept - 1) * 4);
    $result = array_reduce($data, function ($acc, $node) use ($dept, $indent) {
        $type = $node['type'];
        $key = $node['key'];
        switch ($type) {
            case 'nested':
                $children = stylish($node['children'], $dept + 1);
                $acc[] = "{$indent}    {$key}: {$children}";
                break;
            case 'added':
                $formattedNewValue = prepareValue($node['newValue'], $dept);
                $acc[] = "{$indent}  + {$key}: {$formattedNewValue}";
                break;
            case 'removed':
                $formattedOldValue = prepareValue($node['newValue'], $dept);
                $acc[] = "{$indent}  - {$key}: {$formattedOldValue}";
                break;
            case 'not updated':
                $formattedNewValue = prepareValue($node['newValue'], $dept);
                $acc[] = "{$indent}    {$key}: {$formattedNewValue}";
                break;
            case 'updated':
                $formattedOldValue = prepareValue($node['oldValue'], $dept);
                $formattedNewValue = prepareValue($node['newValue'], $dept);
                $addedNode = "{$indent}  + {$key}: {$formattedNewValue}";
                $deletedNode = "{$indent}  - {$key}: {$formattedOldValue}";
                $acc[] = implode("\n", [$deletedNode, $addedNode]);
                break;
            default:
                throw new \Exception("Invalid {$type}.");
        };
        return $acc;
    }, []);

    $formatedData = implode("\n", $result);
    return "{\n{$formatedData}\n{$indent}}";
}

function prepareValue($value, $dept)
{
    if (is_bool($value)) {
        return $value ? 'true' : 'false';
    }

    if (is_null($value)) {
        return 'null';
    }

    if (!is_array($value) && !is_object($value)) {
        return $value;
    }

    if (is_object($value)) {
        $keys = array_keys(get_object_vars($value));
        $indent = str_repeat(" ", 4 * $dept);

        $result = array_map(function ($key) use ($value, $dept, $indent) {
            $childValue = prepareValue($value->$key, $dept + 1);
            return "{$indent}    {$key}: {$childValue}";
        }, $keys);

        $formatedData = implode("\n", $result);
        return "{\n{$formatedData}\n{$indent}}";
    }
}

function format($data)
{
    return stylish($data, 1);
}
