<?php

namespace Differ\Formatters\json;

function format(array $data): string
{
    return json_encode($data, JSON_THROW_ON_ERROR);
}
