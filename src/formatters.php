<?php

namespace Differ\Formatters;

use Differ\Formatters\json;
use Differ\Formatters\stylish;
use Differ\Formatters\plain;

function format(array $data, string $format): string
{
    switch ($format) {
        case 'json':
            return json\format($data);
        case 'stylish':
            return stylish\format($data);
        case 'plain':
            return plain\format($data);
        default:
            throw new \Exception("The report format '{$format}' is not supported");
    }
}
