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

        foreach ($tests as $folder => $files) {
            $output .= PHP_EOL . '## [' . $folder . ' Tests](tests/' . $folder . ')' . PHP_EOL;

            foreach ($files as $file) {
                if (!empty($file['description'])) {
                    $output .= '> _' . $file['description'] . '_' . PHP_EOL . PHP_EOL;
                }

                foreach ($file['tests'] as $test) {
                    $output .= '- [' . ($test['passed'] ? 'x' : ' ') . '] ' . $test['name'] . PHP_EOL;
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
