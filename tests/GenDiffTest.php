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
            'input nested yml - output stylish' => ["diffStylish.txt", "fileNest1.yml", "fileNest2.yml", "stylish"],
            'input nested json - output plain' => ["diffPlain.txt", "fileNest1.json", "fileNest2.json", "plain"],
            'input nested yml - output plain' => ["diffPlain.txt", "fileNest1.yml", "fileNest2.yml", "plain"],
            'input nested json - output json' => ["diffJson.txt", "fileNest1.json", "fileNest2.json", "json"],
            'input nested yml - output json' => ["diffJson.txt", "fileNest1.yml", "fileNest2.yml", "json"]
        ];
    }
}
