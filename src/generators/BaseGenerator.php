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
     * @return array<int, array{
     *              type: string,
     *              path: string,
     *              files: array<int, array{
     *                  name: string,
     *                  path: string,
     *                  description: string,
     *                  tests: array<int, string>
     *              }>
     *          }>
     */
    public static function getTests(string $path): array
    {
        $tests = [];
        $directories = FileHelper::findDirectories($path, [
            'except' => ['datasets'],
            'caseSensitive' => false,
        ]);
        sort($directories);

        foreach ($directories as $directory) {
            $testFiles = [];
            $folder = last(explode('/', $directory));
            $files = FileHelper::findFiles($directory, ['only' => ['*Test.php']]);
            sort($files);

            foreach ($files as $file) {
                $test = [];
                $test['path'] = str_replace($path, '', $file);

                $filename = last(explode('/', $file));
                $test['name'] = implode(' ', StringHelper::toWords(str_replace('Test.php', '', $filename)));

                $contents = file_get_contents($file);
                preg_match('/\/\*\*.*?\*(.*?)\*\//s', $contents, $matches);
                $test['description'] = isset($matches[1]) ? trim($matches[1]) : '';
                $test['tests'] = [];

                preg_match_all('/^(it|test)\(\'(.*?)\'/m', $contents, $matches);
                foreach ($matches[2] as $match) {
                    $function = ucfirst($match);
                    $test['tests'][] = $function;
                }

                $testFiles[] = $test;
            }

            $tests[] = [
                'type' => $folder,
                'path' => str_replace($path, '', $directory),
                'files' => $testFiles,
            ];
        }

        return $tests;
    }

    public static function getTestResults(string $path): string
    {
        $file = FileHelper::normalizePath(CRAFT_BASE_PATH . '/' . $path . '/test-results.txt');

        return @file_get_contents($file) ?: '';
    }
}
