<?php
/**
 * @copyright Copyright (c) PutYourLightsOn
 */

namespace putyourlightson\generatetestspec\generators;

use craft\helpers\FileHelper;
use craft\helpers\StringHelper;

abstract class BaseGenerator
{
    /**
     * @return array<string, array<string, array{
     *             description: string,
     *             tests: array<int, array{
     *                 name: string,
     *                 passed: bool,
     *             }>
     *         }>>
     */
    public static function getTests(string $path): array
    {
        $tests = [];
        $testResults = self::getTestResults($path);

        $directories = FileHelper::findDirectories($path);
        sort($directories);

        foreach ($directories as $directory) {
            $folder = last(explode('/', $directory));
            $files = FileHelper::findFiles($directory, ['only' => ['*Test.php']]);
            sort($files);

            foreach ($files as $file) {
                $filename = last(explode('/', $file));
                $test = implode(' ', StringHelper::toWords(str_replace('Test.php', '', $filename)));

                $contents = file_get_contents($file);
                preg_match('/\/\*\*.*?\*(.*?)\*\//s', $contents, $matches);
                $tests[$folder][$test]['description'] = isset($matches[1]) ? trim($matches[1]) : '';
                $tests[$folder][$test]['tests'] = [];

                preg_match_all('/^(it|test)\(\'(.*?)\'/m', $contents, $matches);
                foreach ($matches[2] as $match) {
                    $function = ucfirst($match);
                    $tests[$folder][$test]['tests'][] = [
                        'name' => $function,
                        'passed' => str_contains($testResults, '[x] ' . strtolower($function)),
                    ];
                }
            }
        }

        return $tests;
    }

    public static function getTestResults(string $path): string
    {
        $file = FileHelper::normalizePath(CRAFT_BASE_PATH . '/' . $path . '/test-results.txt');

        return @file_get_contents($file) ?: '';
    }
}
