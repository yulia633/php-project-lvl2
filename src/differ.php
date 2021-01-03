<?php

namespace Differ\Differ;

function readFile(string $filePath)
{
    $path = realpath($filePath);

    if (!file_exists($path)) {
        throw new \Exception('The file {$path} does not exists.\n');
    }

    return file_get_contents($path);
}

function genDiff(string $firstFilePath, string $secondFilePath)
{
    $firstContent = readFile($firstFilePath);
    $secondContent = readFile($secondFilePath);

    $firstData = json_decode($firstContent, true);
    $secondData = json_decode($secondContent, true);

    return calculate($firstData, $secondData);
}

function calculate(array $firstData, array $secondData)
{
    $mergedData = array_merge($firstData, $secondData);

    ksort($mergedData);

    $result = [];

    foreach ($mergedData as $key => $value) {
        if (is_bool($value)) {
            if ($value === true) {
                $value = 'true';
            } else {
                $value = 'false';
            }
        }
        if (array_key_exists($key, $secondData) && array_key_exists($key, $firstData)) {
            if ($value === $firstData[$key]) {
                $result["   {$key}"] = $value;
            } else {
                $result[" - {$key}"] = $firstData[$key];
                $result[" + {$key}"] = $value;
            }
        } elseif (array_key_exists($key, $firstData) && !array_key_exists($key, $secondData)) {
            $result[" - {$key}"] = $value;
        } else {
            $result[" + {$key}"] = $value;
        }
    }

    return render($result);
}

function render(array $data)
{
    $string = '';

    foreach ($data as $key => $value) {
        $string .= "{$key} => {$value}\n";
    }

    return "{\n{$string}}\n";
}