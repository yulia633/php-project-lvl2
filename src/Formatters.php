<?php

namespace Differ\Formatters;

use function Differ\Formatters\Json\format as formatJson;
use function Differ\Formatters\Stylish\format as formatStylish;
use function Differ\Formatters\Plain\format as formatPlain;

function format(array $data, string $format): string
{
    switch ($format) {
        case 'json':
            return formatJson($data);
        case 'stylish':
            return formatStylish($data);
        case 'plain':
            return formatPlain($data);
        default:
            throw new \Exception("The report format '{$format}' is not supported");
    }
}
