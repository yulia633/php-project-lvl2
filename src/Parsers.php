<?php

namespace Differ\Parsers;

use Symfony\Component\Yaml\Yaml;

function parse(string $data, string $type): array
{
    switch ($type) {
        case 'json':
            return json_decode($data, true, 512, JSON_THROW_ON_ERROR);
        case 'yaml':
        case 'yml':
            return Yaml::parse($data);
        default:
            throw new \Exception("The type: '{$type}' is not supported");
    }
}
