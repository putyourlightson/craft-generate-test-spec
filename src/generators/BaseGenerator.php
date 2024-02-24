<?php
/**
 * @copyright Copyright (c) PutYourLightsOn
 */

namespace putyourlightson\generatetestspec\generators;

use SimpleXMLElement;

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

        $testSuites = self::getTestSuitesWithCases($xml);

        foreach ($testSuites as $testSuite) {
            $nameParts = explode('\\', (string)$testSuite['name']);
            $nameParts = array_splice($nameParts, -2);
            $testClass = str_replace('Test', '', $nameParts[1]);
            $fileTests = [];

            foreach ($testSuite->testcase as $testCase) {
                $name = $testCase['name'];
                $name = str_replace(['"dataset "', '""'], '`', $name);
                $fileTests[] = [
                    'name' => $name,
                    'passed' => empty($testCase->error) && empty($testCase->failure),
                ];
            }

            $filePath = $nameParts[0] . '/' . $nameParts[1] . '.php';

            if (file_exists($path . '/' . $filePath)) {
                $contents = file_get_contents($path . '/' . $filePath);
                preg_match('/\/\*\*.*?\*(.*?)\*\//s', $contents, $matches);
                $description = isset($matches[1]) ? trim($matches[1]) : '';
                $previousTests = $tests[$nameParts[0]][$testClass]['tests'] ?? [];
                $tests[$nameParts[0]][$testClass] = [
                    'path' => $filePath,
                    'description' => $description,
                    'tests' => array_merge($previousTests, $fileTests),
                ];
            }
        }

        return $tests;
    }

    /**
     * Return all test suites that contain test cases.
     *
     * @return SimpleXMLElement[]
     */
    private static function getTestSuitesWithCases(SimpleXMLElement $testsuite): array
    {
        $testSuites = [];

        foreach ($testsuite->testsuite as $testSuite) {
            if (isset($testSuite->testcase)) {
                $testSuites[] = $testSuite;
            } else {
                $testSuites = array_merge($testSuites, self::getTestSuitesWithCases($testSuite));
            }
        }

        return $testSuites;
    }
}
