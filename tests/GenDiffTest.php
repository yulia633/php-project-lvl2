<?php

namespace Differ\Tests;

use PHPUnit\Framework\TestCase;

use function Differ\Differ\genDiff;

class GenDiffTest extends TestCase
{
    private function getFilePathFixtures(string $fileName): string
    {
        $parts = [__DIR__, 'fixtures', $fileName];
        return implode(DIRECTORY_SEPARATOR, $parts);
    }

     /**
     * @dataProvider defaultOutputProvider
     */
    public function testDefaultFormatOutput(string $fileName1, string $fileName2, string $expectedFileName): void
    {
        $outputFilePath = $this->getFilePathFixtures($expectedFileName);
        $expectedOutput = file_get_contents($outputFilePath);

        $inputFilePath1 = $this->getFilePathFixtures($fileName1);
        $inputFilePath2 = $this->getFilePathFixtures($fileName2);

        $diffResult = genDiff($inputFilePath1, $inputFilePath2);

        $this->assertSame($expectedOutput, $diffResult);
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
        $outputFilePath = $this->getFilePathFixtures($expectedFileName);
        $expectedOutput = trim(file_get_contents($outputFilePath));

        $inputFilePath1 = $this->getFilePathFixtures($fileName1);
        $inputFilePath2 = $this->getFilePathFixtures($fileName2);

        $diffResult = genDiff($inputFilePath1, $inputFilePath2, $format);

        $this->assertSame($expectedOutput, $diffResult);
    }

    public function defaultOutputProvider(): array
    {
        return [
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
