<?php
/**
 * @copyright Copyright (c) PutYourLightsOn
 */

namespace putyourlightson\generatetestspec\generators;

use craft\helpers\FileHelper;

class MarkdownGenerator extends BaseGenerator
{
    public static function generate(string $path): ?string
    {
        $output = '';
        $tests = parent::getTests($path);
        $pathFolder = last(explode('/', $path));

        foreach ($tests as $testTypes) {
            $output .= PHP_EOL . '## [' . $testTypes['type'] . ' Tests](' . $pathFolder . $testTypes['path'] . ')' . PHP_EOL;

            foreach ($testTypes['files'] as $file) {
                $output .= PHP_EOL . '### [' . $file['name'] . '](' . $pathFolder . $file['path'] . ')' . PHP_EOL . PHP_EOL;

                if (!empty($file['description'])) {
                    $output .= '> _' . $file['description'] . '_' . PHP_EOL . PHP_EOL;
                }

                foreach ($file['tests'] as $test) {
                    $output .= '- ' . $test . '.' . PHP_EOL;
                }
            }
        }

        $outputPath = FileHelper::normalizePath(CRAFT_BASE_PATH . '/' . $path . '/../TESTS.md');
        $contents = @file_get_contents($outputPath) ?: '';
        if (preg_match('/.*^\h*?---\h*$/ms', $contents, $matches)) {
            $contents = $matches[0] . PHP_EOL . $output;
        } else {
            $contents = $contents . PHP_EOL . PHP_EOL . '---' . PHP_EOL . $output;
        }
        FileHelper::writeToFile($outputPath, $contents, ['lock' => false]);

        return $outputPath;
    }
}
