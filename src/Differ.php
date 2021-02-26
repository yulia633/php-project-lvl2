<?php

namespace Differ\Differ;

use function Funct\Collection\sortBy;
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

    $parseFirstData = parse($firstData, getType($firstFilePath));
    $parseSecondData = parse($secondData, getType($secondFilePath));

    $node = makeNode($parseFirstData, $parseSecondData);
    return format($node, $format);
}

function makeNode(object $firstData, object $secondData): array
{
    $firstDataArray = (array) $firstData;
    $secondDataArray = (array) $secondData;

    $unionKeys = array_keys(array_merge($firstDataArray, $secondDataArray));
    $sortedKeys = array_values(sortBy($unionKeys, fn($key) => $key));

    $node = array_map(function ($key) use ($firstDataArray, $secondDataArray): array {
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
        if (is_object($firstDataArray[$key]) && is_object($secondDataArray[$key])) {
            return [
                'key' => $key,
                'type' => 'complex',
                'children' => makeNode($firstDataArray[$key], $secondDataArray[$key]),
            ];
        }
        if ($firstDataArray[$key] === $secondDataArray[$key]) {
            return [
                'key' => $key,
                'type' => 'not updated',
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

    return $node;
}
