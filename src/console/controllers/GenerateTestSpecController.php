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
    public function actionMarkdown(string $path): int
    {
        return $this->exit(MarkdownGenerator::generate($path));
    }

    private function exit(string $outputPath): int
    {
        if (empty($outputPath)) {
            $this->stdout('Test spec could not be generated.', BaseConsole::FG_RED);
        } else {
            $this->stdout('Test spec generated at ' . trim(str_replace(CRAFT_BASE_PATH, '', $outputPath), '/'), BaseConsole::FG_GREEN);
        }

        return ExitCode::OK;
    }
}
