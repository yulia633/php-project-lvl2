<?php

namespace Differ\Tests;

use PHPUnit\Framework\TestCase;

use function Differ\Differ\genDiff;

class GenDiffTest extends TestCase
{
    /**
     * @dataProvider additionProvider
     */

    public function testGenDiffWithDataSet($expected, $file1, $file2, $format)
    {
        $fixtures = "./tests/fixtures/";
        $this->assertEquals(
            trim(file_get_contents($fixtures . $expected)),
            genDiff($fixtures . $file1, $fixtures . $file2, $format)
        );
    }

    public function additionProvider()
    {
        return [
            'input flat json - output json' => ["diffJson.txt", "file1.json", "file2.json", "json"],
            'input flat yml - output json' => ["diffJson.txt", "file1.yml", "file2.yml", "json"],
            'input flat json - output stylish' => ["diff.txt", "file1.json", "file2.json", "stylish"],
            'input flat yml - output stylish' => ["diff.txt", "file1.yml", "file2.yml", "stylish"],
            'input nested json - output stylish' => ["diffStylish.txt", "fileNest1.json", "fileNest2.json", "stylish"],
            'input nested yml - output stylish' => ["diffStylish.txt", "fileNest1.yml", "fileNest2.yml", "stylish"]
        ];
    }
}
