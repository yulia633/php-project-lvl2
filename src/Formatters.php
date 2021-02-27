<?php

namespace Differ\Formatters;

use function Differ\Formatters\Json\format as formatJson;
use function Differ\Formatters\Stylish\format as formatStylish;
use function Differ\Formatters\Plain\format as formatPlain;

function format(array $ast, string $format): string
{
    switch ($format) {
        case 'json':
            return formatJson($ast);
        case 'stylish':
            return formatStylish($ast);
        case 'plain':
            return formatPlain($ast);
        default:
            throw new \Exception("The report format '{$format}' is not supported");
    }
}
