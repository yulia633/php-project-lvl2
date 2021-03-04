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

    $ast = makeNode($parsedFirstData, $parsedSecondData);

    return format($ast, $format);
}

function makeNode(array $firstData, array $secondData): array
{
    $unionKeys = union(array_keys($firstData), array_keys($secondData));
    $sortedKeys = array_values(sortBy($unionKeys, fn($key) => $key));

    $buildAst = array_map(function ($key) use ($firstData, $secondData): array {
        if (!array_key_exists($key, $firstData)) {
            return [
                'key' => $key,
                'type' => 'added',
                'oldValue' => null,
                'newValue' => $secondData[$key],
            ];
        }
        if (!array_key_exists($key, $secondData)) {
            return [
                'key' => $key,
                'type' => 'removed',
                'oldValue' => null,
                'newValue' => $firstData[$key],
            ];
        }
        if (is_array($firstData[$key]) && is_array($secondData[$key])) {
            return [
                'key' => $key,
                'type' => 'complex',
                'children' => makeNode($firstData[$key], $secondData[$key]),
            ];
        }
        if ($firstData[$key] === $secondData[$key]) {
            return [
                'key' => $key,
                'type' => 'unchanged',
                'oldValue' => $firstData[$key],
                'newValue' => $secondData[$key],
            ];
        }
        return [
            'key' => $key,
            'type' => 'updated',
            'oldValue' => $firstData[$key],
            'newValue' => $secondData[$key],
        ];
    }, $sortedKeys);

    return $buildAst;
}
