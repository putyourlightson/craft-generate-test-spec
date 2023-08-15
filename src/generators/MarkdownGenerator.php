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

        foreach ($tests as $type => $testTypes) {
            $output .= PHP_EOL . '## ' . $type . ' Tests' . PHP_EOL;

            foreach ($testTypes as $class => $testClass) {
                $output .= PHP_EOL . '### [' . $class . '](' . $pathFolder . '/' . $testClass['path'] . ')' . PHP_EOL . PHP_EOL;

                if (!empty($testClass['description'])) {
                    $output .= '> _' . $testClass['description'] . '_' . PHP_EOL . PHP_EOL;
                }

                foreach ($testClass['tests'] as $test) {
                    // Octicons: https://github.com/primer/octicons/tree/main/icons
                    $output .= $test['passed'] ? '![Pass](https://raw.githubusercontent.com/putyourlightson/craft-generate-test-spec/main/icons/pass.svg)' : '![Fail](https://raw.githubusercontent.com/putyourlightson/craft-generate-test-spec/main/icons/fail.svg)';
                    $output .= ' ' . $test['name'] . '.  ' . PHP_EOL;
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
