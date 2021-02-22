<?php

namespace Differ\Tests;

use PHPUnit\Framework\TestCase;

use function Differ\Differ\genDiff;

class GenDiffTest extends TestCase
{
    private function getFilePath(string $fileName): string
    {
        $parts = [__DIR__, 'fixtures', $fileName];
        return implode(DIRECTORY_SEPARATOR, $parts);
    }

     /**
     * @dataProvider defaultOutputProvider
     */
    public function testDefaultFormatOutput(string $fileName1, string $fileName2, string $expectedFileName): void
    {
        $expectedOutput = file_get_contents($this->getFilePath($expectedFileName));
        $this->assertSame($expectedOutput, genDiff($this->getFilePath($fileName1), $this->getFilePath($fileName2)));
    }

    /**
     * @dataProvider differentFormatsProvider
     */
    public function testDifferentFormatOutputs(
        string $fileName1,
        string $fileName2,
        string $format,
        string $expectedFileName
    ): void {
        $expectedOutput = trim(file_get_contents($this->getFilePath($expectedFileName)));
        $this->assertSame($expectedOutput, genDiff(
            $this->getFilePath($fileName1),
            $this->getFilePath($fileName2),
            $format
        ));
    }

    public function defaultOutputProvider(): array
    {
        return [
            'default output for json files' => ['fileNest1.json', 'fileNest2.json', 'diffStylish.txt'],
            'default output for yaml files' => ['fileNest1.yml', 'fileNest2.yml', 'diffStylish.txt']
        ];
    }

    public function differentFormatsProvider(): array
    {
        return [
            'output stylish' => ['fileNest1.yml', 'fileNest2.yml', 'stylish', 'diffStylish.txt'],
            'output plain' => ['fileNest1.json', 'fileNest2.json', 'plain', 'diffPlain.txt'],
            'output json' => ['fileNest1.json', 'fileNest2.json', 'json', 'diffJson.txt']
        ];
    }
}
