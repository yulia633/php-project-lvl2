<?php

namespace Differ\Differ;

use function Differ\Parsers\parse;
use function Differ\Formater\format;

function readFile(string $filePath)
{
    $path = realpath($filePath);

    if (!file_exists($path)) {
        throw new \Exception("The file {$filePath} does not exists.\n");
    }

    return file_get_contents($path);
}

function getData($path)
{
    $type = pathinfo($path, PATHINFO_EXTENSION);
    return parse(readFile($path), $type);
}

function genDiff(string $firstFilePath, string $secondFilePath, $format = 'stylish')
{
    $firstData = getData($firstFilePath);
    $secondData = getData($secondFilePath);

    $ast = genAst($firstData, $secondData);
    return format($ast, $format);
}

function union(array $firstColl, array $secondColl)
{
    return array_unique(array_merge($firstColl, $secondColl));
}

function genAst(array $firstData, array $secondData)
{
    //Todo: возможно тут возвращать принудительно массив вместо объекта

    $union = union(array_keys($firstData), array_keys($secondData));

    //ksort($union);

    $ast = array_reduce($union, function ($acc, $item) use ($firstData, $secondData) {
        $acc[] = diffData($item, $firstData, $secondData);
        return $acc;
    }, []);

    return $ast;
}

function diffData($item, $data1, $data2)
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
            'type' => 'nested',
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

// function calculate(array $firstData, array $secondData)
// {
//     $mergedData = array_merge($firstData, $secondData);

//     ksort($mergedData);

//     $result = [];

//     foreach ($mergedData as $key => $value) {
//         if (is_bool($value)) {
//             if ($value === true) {
//                 $value = 'true';
//             } else {
//                 $value = 'false';
//             }
//         }
//         if (array_key_exists($key, $secondData) && array_key_exists($key, $firstData)) {
//             if ($value === $firstData[$key]) {
//                 $result["    {$key}"] = $value;
//             } else {
//                 $result["  - {$key}"] = $firstData[$key];
//                 $result["  + {$key}"] = $value;
//             }
//         } elseif (array_key_exists($key, $firstData) && !array_key_exists($key, $secondData)) {
//             $result["  - {$key}"] = $value;
//         } else {
//             $result["  + {$key}"] = $value;
//         }
//     }

//     return render($result);
// }

// function render(array $data)
// {
//     $string = '';

//     foreach ($data as $key => $value) {
//         $string .= "{$key}: {$value}\n";
//     }

//     return "{\n{$string}}\n";
// }
