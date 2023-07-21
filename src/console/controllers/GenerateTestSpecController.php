<?php
/**
 * @copyright Copyright (c) PutYourLightsOn
 */

namespace putyourlightson\generatetestspec\console\controllers;

use putyourlightson\generatetestspec\generators\MarkdownGenerator;
use yii\console\Controller;
use yii\console\ExitCode;
use yii\helpers\BaseConsole;

class GenerateTestSpecController extends Controller
{
    public function actionIndex(string $path): int
    {
        $outputPath = MarkdownGenerator::generate($path);

        if ($outputPath === null) {
            $this->stdout('Test spec could not be generated.', BaseConsole::FG_RED);
        } else {
            $this->stdout('Test spec generated at ' . str_replace(CRAFT_BASE_PATH, '', $outputPath), BaseConsole::FG_GREEN);
        }

        return ExitCode::OK;
    }
}
