<?php

namespace Differ\Formatters\json;

function format($data)
{
    return json_encode($data, JSON_THROW_ON_ERROR);
}
