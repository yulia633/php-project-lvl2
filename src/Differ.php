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

function getType(string $path): string
{
    return pathinfo($path, PATHINFO_EXTENSION);
}

function genDiff(string $firstFilePath, string $secondFilePath, string $format = 'stylish'): string
{
    $firstData = readFile($firstFilePath);
    $secondData = readFile($secondFilePath);

    $parsedFirstData = parse($firstData, getType($firstFilePath));
    $parsedSecondData = parse($secondData, getType($secondFilePath));

    $ast = makeNode($parsedFirstData, $parsedSecondData);

    return format($ast, $format);
}

function makeNode(array $firstDataArray, array $secondDataArray): array
{
    $unionKeys = union(array_keys($firstDataArray), array_keys($secondDataArray));
    $sortedKeys = array_values(sortBy($unionKeys, fn($key) => $key));

    $buildAst = array_map(function ($key) use ($firstDataArray, $secondDataArray): array {
        if (!array_key_exists($key, $firstDataArray)) {
            return [
                'key' => $key,
                'type' => 'added',
                'oldValue' => null,
                'newValue' => $secondDataArray[$key],
            ];
        }
        if (!array_key_exists($key, $secondDataArray)) {
            return [
                'key' => $key,
                'type' => 'removed',
                'oldValue' => null,
                'newValue' => $firstDataArray[$key],
            ];
        }
        if (is_array($firstDataArray[$key]) && is_array($secondDataArray[$key])) {
            return [
                'key' => $key,
                'type' => 'complex',
                'children' => makeNode($firstDataArray[$key], $secondDataArray[$key]),
            ];
        }
        if ($firstDataArray[$key] === $secondDataArray[$key]) {
            return [
                'key' => $key,
                'type' => 'unchanged',
                'oldValue' => $firstDataArray[$key],
                'newValue' => $secondDataArray[$key],
            ];
        }
        return [
            'key' => $key,
            'type' => 'updated',
            'oldValue' => $firstDataArray[$key],
            'newValue' => $secondDataArray[$key],
        ];
    }, $sortedKeys);

    return $buildAst;
}
