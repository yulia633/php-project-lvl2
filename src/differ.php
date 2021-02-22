<?php

namespace Differ\Differ;

use function Differ\Parsers\parse;
use function Differ\Formatters\format;

function readFile(string $filePath): string
{
    $path = realpath($filePath);

    if (!file_exists($path)) {
        throw new \Exception("The file {$filePath} does not exists.\n");
    }

    return file_get_contents($path);
}

function getData(string $path): object
{
    $type = pathinfo($path, PATHINFO_EXTENSION);
    return parse(readFile($path), $type);
}

function genDiff(string $firstFilePath, string $secondFilePath, $format = 'stylish'): string
{
    $firstData = getData($firstFilePath);
    $secondData = getData($secondFilePath);

    $ast = genAst($firstData, $secondData);
    return format($ast, $format);
}

function genAst(object $firstData, object $secondData): array
{
    $firstData = (array) $firstData;
    $secondData = (array) $secondData;

    $union = array_keys(array_merge($firstData, $secondData));
    sort($union);

    $ast = array_reduce($union, function ($acc, $item) use ($firstData, $secondData) {
        $acc[] = diffData($item, $firstData, $secondData);
        return $acc;
    }, []);

    return $ast;
}

function diffData(string $item, array $data1, array $data2): array
{
    if (!array_key_exists($item, $data1)) {
        return [
            'key' => $item,
            'type' => 'added',
            'oldValue' => null,
            'newValue' => $data2[$item],
        ];
    }
    if (!array_key_exists($item, $data2)) {
        return [
            'key' => $item,
            'type' => 'removed',
            'oldValue' => null,
            'newValue' => $data1[$item],
        ];
    }
    if (is_object($data1[$item]) && is_object($data2[$item])) {
        return [
            'key' => $item,
            'type' => 'complex',
            'children' => genAst($data1[$item], $data2[$item]),
        ];
    }
    if ($data1[$item] === $data2[$item]) {
        return [
            'key' => $item,
            'type' => 'not updated',
            'oldValue' => $data1[$item],
            'newValue' => $data2[$item],
        ];
    } else {
        return [
            'key' => $item,
            'type' => 'updated',
            'oldValue' => $data1[$item],
            'newValue' => $data2[$item],
        ];
    }
}
