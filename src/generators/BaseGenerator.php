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

        foreach ($xml->testsuite as $testsuiteLevel1) {
            foreach ($testsuiteLevel1->testsuite as $testsuiteLevel2) {
                foreach ($testsuiteLevel2->testsuite as $testsuite) {
                    $fileTests = [];
                    $nameParts = explode('\\', (string)$testsuite['name']);
                    $nameParts = array_splice($nameParts, -2);
                    $testClass = str_replace('Test', '', $nameParts[1]);

                    foreach ($testsuite->testcase as $testCase) {
                        $name = $testCase['name'];
                        $name = str_replace('__pest_evaluable_', '', $name);
                        $name = str_replace('__', '/underscore/', $name);
                        $name = str_replace('_', ' ', $name);
                        $name = str_replace('/underscore/', '_', $name);
                        $fileTests[] = [
                            'name' => $name,
                            'passed' => empty($testCase->error) && empty($testCase->failure),
                        ];
                    }

                    $filePath = $nameParts[0] . '/' . $nameParts[1] . '.php';
                    $contents = file_get_contents($path . '/' . $filePath);
                    preg_match('/\/\*\*.*?\*(.*?)\*\//s', $contents, $matches);
                    $description = isset($matches[1]) ? trim($matches[1]) : '';

                    $tests[$nameParts[0]][$testClass] = [
                        'path' => $filePath,
                        'description' => $description,
                        'tests' => $fileTests,
                    ];
                }
            }
        }

        return $tests;
    }
}
