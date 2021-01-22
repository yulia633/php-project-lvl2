<?php

namespace Differ\Parsers;

use Symfony\Component\Yaml\Yaml;

function parse($data, $type)
{
    return [
        'json' => fn($data) => json_decode($data, true),
        'yml' => (array) fn($data) => Yaml::parse($data, Yaml::PARSE_OBJECT_FOR_MAP),
    ];

    //Todo: возможно тут возвращать в ямл принудительно массив вместо объекта
}
