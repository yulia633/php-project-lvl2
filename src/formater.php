<?php

namespace Differ\Formater;

function format($ast, $format)
{
    return mapping()[$format]($ast);
}

function mapping()
{
    return [
        'stylish' =>
            fn($ast) => stylish($ast),
        'json' =>
            fn($ast) => json($ast),
    ];
}

function json($ast)
{
    return json_encode($ast);
}

// 'added',
// 'removed',
// 'nested',
// 'not updated'
// 'updated'
function stylish($ast)
{
    //
}
