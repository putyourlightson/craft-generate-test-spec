<?php
/**
 * @copyright Copyright (c) PutYourLightsOn
 */

namespace putyourlightson\generatetestspec\generators;

abstract class BaseGenerator
{
    /**
     * @return array<string, array<string, array{
     *              path: string,
     *              description: string,
     *              tests: array<int, array{
     *                  name: string,
     *                  passed: bool
     *              }>
     *          }>>
     */
    public static function getTests(string $path): array
    {
        $tests = [];

        $xml = @simplexml_load_file($path . '/test-results.xml');

        if (!$xml) {
            exit('Could not load `' . $path . '/test-results.xml` file.');
        }

        foreach ($xml->testsuite as $testsuite) {
            foreach ($testsuite->testsuite as $testFile) {
                $fileTests = [];
                $nameParts = explode('\\', (string)$testFile['name']);
                $nameParts = array_splice($nameParts, -2);
                $testType = $nameParts[0];
                $testClass = str_replace('Test', '', $nameParts[1]);

                foreach ($testFile->testcase as $testCase) {
                    $fileTests[] = [
                        'name' => (string)$testCase['name'],
                        'passed' => empty($testCase->failure),
                    ];
                }

                $file = (string)$testFile['file'];
                $pathParts = explode('/', $file);
                $pathParts = array_splice($pathParts, -2);

                $contents = file_get_contents($file);
                preg_match('/\/\*\*.*?\*(.*?)\*\//s', $contents, $matches);
                $description = isset($matches[1]) ? trim($matches[1]) : '';

                $tests[$testType][$testClass] = [
                    'path' => $pathParts[0] . '/' . $pathParts[1],
                    'description' => $description,
                    'tests' => $fileTests,
                ];
            }
        }

        return $tests;
    }
}
