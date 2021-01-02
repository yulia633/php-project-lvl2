<?php

namespace Differ\Cli;

use function Differ\Differ\genDiff;

const DOC = <<<DOC
gendiff -h

Generate diff

Usage:
  gendiff (-h|--help)
  gendiff (-v|--version)
  gendiff [--format <fmt>] <firstFile> <secondFile>

Options:
  -h --help                     Show this screen
  -v --version                  Show version
  --format <fmt>                Report format [default: stylish]
DOC;

function run()
{
    $args = \Docopt::handle(DOC, ['version' => '0.1']);

    $firstFilePath = $args['<firstFile>'];
    $secondFilePath = $args['<secondFile>'];

    print_r(genDiff($firstFilePath, $secondFilePath));
}
