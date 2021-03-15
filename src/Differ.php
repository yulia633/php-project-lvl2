<?php

namespace Differ\Differ;

use function Funct\Collection\sortBy;
use function Funct\Collection\union;
use function Differ\Parsers\parse;
use function Differ\Formatters\format;

function readFile(string $filePath): string
{
    if (!file_exists($filePath)) {
        throw new \Exception("The file {$filePath} does not exists.");
    }

    return (string) file_get_contents($filePath);
}

function genDiff(string $firstFilePath, string $secondFilePath, string $format = 'stylish'): string
{
    $firstData = readFile($firstFilePath);
    $secondData = readFile($secondFilePath);

    $parsedFirstData = parse($firstData, pathinfo($firstFilePath, PATHINFO_EXTENSION));
    $parsedSecondData = parse($secondData, pathinfo($secondFilePath, PATHINFO_EXTENSION));

    $ast = genAst($parsedFirstData, $parsedSecondData);

    return format($ast, $format);
}

function genAst(object $firstData, object $secondData): array
{
    $firstKeys = array_keys(get_object_vars($firstData));
    $secondKeys = array_keys(get_object_vars($secondData));
    $unionKeys = union($firstKeys, $secondKeys);
    $sortedKeys = array_values(sortBy($unionKeys, fn($key) => $key));

    $buildAst = array_map(function ($key) use ($firstData, $secondData): array {
        if (!property_exists($firstData, $key)) {
            return makeNode($key, 'added', null, $secondData->$key);
        }
        if (!property_exists($secondData, $key)) {
            return makeNode($key, 'removed', $firstData->$key, null);
        }
        if (is_object($firstData->$key) && is_object($secondData->$key)) {
            return makeNode($key, 'complex', null, null, genAst($firstData->$key, $secondData->$key));
        }
        if ($firstData->$key === $secondData->$key) {
            return makeNode($key, 'unchanged', $firstData->$key, $secondData->$key);
        }
        return makeNode($key, 'updated', $firstData->$key, $secondData->$key);
    }, $sortedKeys);

    return $buildAst;
}

function makeNode(string $key, string $type, $oldValue, $newValue, $children = null): array
{
    return [
        'key' => $key,
        'type' => $type,
        'oldValue' => $oldValue,
        'newValue' => $newValue,
        'children' => $children
    ];
}
