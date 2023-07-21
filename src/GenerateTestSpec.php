<?php
/**
 * @copyright Copyright (c) PutYourLightsOn
 */

namespace putyourlightson\generatetestspec;

use Craft;
use putyourlightson\generatetestspec\console\controllers\GenerateTestSpecController;
use yii\base\BootstrapInterface;
use yii\base\Module;

class GenerateTestSpec extends Module implements BootstrapInterface
{
    public function bootstrap($app): void
    {
        if (Craft::$app->request->isConsoleRequest) {
            Craft::$app->controllerMap['generate-test-spec'] = GenerateTestSpecController::class;
        }
    }
}
