<?php

namespace Differ\Parsers;

use Symfony\Component\Yaml\Yaml;

function parse($data, $type)
{
    return getExtension()[$type]($data);
}

function getExtension()
{
    return [
        'json' => fn($data) => json_decode($data, true),
        'yml' => fn($data) => Yaml::parse($data, Yaml::PARSE_OBJECT_FOR_MAP),
    ];

    //Todo: возможно тут возвращать в ямл принудительно массив вместо объекта
}
