<?php

namespace Differ\Formater;

use function Funct\Collection\flattenAll;

const STYLISH_INDENT = "    ";
const STYLISH_INDENT_BASE = " ";

function format($ast, $format)
{
    return mapping()[$format]($ast);
}

function mapping()
{
    return [
        'stylish' => fn($ast) => stylish($ast),
        'json' => fn($ast) => json($ast),
    ];
}

function json($ast)
{
    return json_encode($ast);
}

function stylish($ast)
{
   $result = array_reduce($ast, function ($acc, $node) {
        $type = $node['type'];
        $key = $node['key'];
        switch ($type) {
            case 'nested':
                $children = $node['children'];
                $acc[] = STYLISH_INDENT_BASE . "    {$key}: {";
                $acc[] = stylish($children, STYLISH_INDENT_BASE . STYLISH_INDENT);
                $acc[] = STYLISH_INDENT_BASE . "    }";
                break;
            case 'added':
                $newValue = $node['newValue'];
                $acc[] = STYLISH_INDENT_BASE . "  + {$key}: " . prepareValue($newValue, STYLISH_INDENT_BASE . STYLISH_INDENT);
                break;
            case 'removed':
                $oldValue = $node['oldValue'];
                $acc[] = STYLISH_INDENT_BASE . "  - {$key}: " . prepareValue($oldValue, STYLISH_INDENT_BASE . STYLISH_INDENT);
                break;
            case 'not updated':
                $newValue = $node['newValue'];
                $acc[] = STYLISH_INDENT_BASE . "    {$key}: " . prepareValue($newValue, STYLISH_INDENT_BASE . STYLISH_INDENT);
                break;
            case 'updated':
                $newValue = $node['newValue'];
                $oldValue = $node['oldValue'];
                $acc[] = STYLISH_INDENT_BASE . "  - {$key}: " . prepareValue($oldValue, STYLISH_INDENT_BASE . STYLISH_INDENT);
                $acc[] = STYLISH_INDENT_BASE . "  + {$key}: " . prepareValue($newValue, STYLISH_INDENT_BASE . STYLISH_INDENT);
                break;
            default:
                throw new Exception("Invalid {$type}.");
        };
        return $acc;
    }, []);
   // return flattenAll($result);
   return implode(
       PHP_EQL,
       array_merge(['{'], flattenAll($result), ['}']
       )
   );
}

function prepareValue($value, $space = STYLISH_INDENT_BASE)
{
    $array = (array) $value;
    $result = implode("", array_map(function ($key, $value) use ($space) {
        return "\n" . $space . "    {$key}: " . prepareValue($value, $space . "    ");
    }, array_keys($array), $array));
    return "{" . $result . "\n" . $space . "}";
}
