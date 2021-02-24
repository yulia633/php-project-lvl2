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

function getData(string $path): object
{
    $type = pathinfo($path, PATHINFO_EXTENSION);
    return parse(readFile($path), $type);
}

function genDiff(string $firstFilePath, string $secondFilePath, string $format = 'stylish'): string
{
    $firstData = getData($firstFilePath);
    $secondData = getData($secondFilePath);

    $node = makeNode($firstData, $secondData);
    return format($node, $format);
}

function makeNode(object $firstData, object $secondData): array
{
    $firstDataArray = (array) $firstData;
    $secondDataArray = (array) $secondData;

    $unionKeys = array_keys(array_merge($firstDataArray, $secondDataArray));
    $sortedKeys = array_values(sortBy($unionKeys, fn($key) => $key));

    $node = array_reduce($sortedKeys, function ($acc, $key) use ($firstDataArray, $secondDataArray): array {
        return[...$acc, diffData($key, $firstDataArray, $secondDataArray)];
    }, []);

    return $node;
}

function diffData(string $key, array $data1, array $data2): array
{
    if (!array_key_exists($key, $data1)) {
        return [
            'key' => $key,
            'type' => 'added',
            'oldValue' => null,
            'newValue' => $data2[$key],
        ];
    }
    if (!array_key_exists($key, $data2)) {
        return [
            'key' => $key,
            'type' => 'removed',
            'oldValue' => null,
            'newValue' => $data1[$key],
        ];
    }
    if (is_object($data1[$key]) && is_object($data2[$key])) {
        return [
            'key' => $key,
            'type' => 'complex',
            'children' => makeNode($data1[$key], $data2[$key]),
        ];
    }
    if ($data1[$key] === $data2[$key]) {
        return [
            'key' => $key,
            'type' => 'not updated',
            'oldValue' => $data1[$key],
            'newValue' => $data2[$key],
        ];
    }
    return [
        'key' => $key,
        'type' => 'updated',
        'oldValue' => $data1[$key],
        'newValue' => $data2[$key],
    ];
}
