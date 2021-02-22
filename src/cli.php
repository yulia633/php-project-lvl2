<?php

namespace Differ\Cli;

use function Differ\Differ\genDiff;

const DOC = <<<DOC
gendiff -h

Generate diff

Usage:
  gendiff (-h|--help)
  gendiff (-v|--version)
  gendiff [--format <fmt>] <firstFilePath> <secondFilePath>

Options:
  -h --help                    Show this screen
  -v --version                 Show version
  --format <fmt>               Report format [default: stylish] Examples: stylish, plain, json

Report formats:
    --format <fmt>
        pretty                 Show changes in files marked with symbols "+/-"
        plain                  Show changes in files explained by the text
        json                   Show changes in files as a JSON string
DOC;

function run()
{
    $args = \Docopt::handle(DOC, ['version' => '0.1']);

    $firstFilePath = $args['<firstFilePath>'];
    $secondFilePath = $args['<secondFilePath>'];
    $format = $args["--format"];

    print_r(genDiff($firstFilePath, $secondFilePath, $format));
}
