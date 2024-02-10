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

        foreach ($xml->testsuite as $testSuiteLevel1) {
            foreach ($testSuiteLevel1->testsuite as $testSuiteLevel2) {
                foreach ($testSuiteLevel2->testsuite as $testSuiteLevel3) {
                    $nameParts = explode('\\', (string)$testSuiteLevel3['name']);
                    $nameParts = array_splice($nameParts, -2);
                    $testClass = str_replace('Test', '', $nameParts[1]);

                    $testSuites = !empty($testSuiteLevel3->testsuite) ? $testSuiteLevel3->testsuite : [$testSuiteLevel3];
                    $fileTests = [];

                    foreach ($testSuites as $testSuite) {
                        foreach ($testSuite->testcase as $testCase) {
                            $name = $testCase['name'];
                            $name = str_replace(['"dataset "', '""'], '`', $name);
                            $fileTests[] = [
                                'name' => $name,
                                'passed' => empty($testCase->error) && empty($testCase->failure),
                            ];
                        }
                    }

                    $filePath = $nameParts[0] . '/' . $nameParts[1] . '.php';

                    if (file_exists($path . '/' . $filePath)) {
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
        }

        return $tests;
    }
}
