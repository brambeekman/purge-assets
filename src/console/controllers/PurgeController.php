<?php
/**
 * Purge Assets plugin for Craft CMS 3.x
 *
 * A plugin to purge your unused assets, disabled products, and more. 
 *
 * @link      https://brambeekman.com
 * @copyright Copyright (c) 2021 Bram Beekman
 */

namespace brambeekman\purgeassets\console\controllers;

use brambeekman\purgeassets\PurgeAssets;

use craft\commerce\elements\Product;
use Craft;
use yii\console\Controller;
use yii\helpers\Console;
use craft\db\Query;
use craft\db\Table;
use craft\elements\Asset;
use yii\console\ExitCode;

/**
 * Default Command
 *
 * @author    Bram Beekman
 * @package   PurgeAssets
 * @since     1.0.0
 */
class PurgeController extends Controller
{

    public function actionPurgeDisabledProducts() : int
    {
        // Fetch disabled products
        $products = Product::find()
            ->status('disabled')
            ->all();

        if (empty($products)) {
            $this->stdout('No disabled products to delete.' . PHP_EOL);
            return ExitCode::OK;
        } else {
            $this->stdout('Found ' . count($products) . ' disabled products.' . PHP_EOL);
        }

        foreach ($products as $product) {
            $this->stdout(" - Deleting asset {$product} ... ");
            Craft::$app->elements->deleteElement($product);
            $this->stdout('done' . PHP_EOL, Console::FG_GREEN);
        }

        $this->stdout('Finished deleting disabled products.' . PHP_EOL);
        return ExitCode::OK;

    }

    public function actionPurgeAllProducts() : int
    {
        // Fetch all products
        $products = Product::find()->all();

        if (empty($products)) {
            $this->stdout('No products to delete.' . PHP_EOL);
            return ExitCode::OK;
        } else {
            $this->stdout('Found ' . count($products) . ' products.' . PHP_EOL);
        }

        foreach ($products as $product) {
            $this->stdout(" - Deleting asset {$product} ... ");
            Craft::$app->elements->deleteElement($product);
            $this->stdout('done' . PHP_EOL, Console::FG_GREEN);
        }

        $this->stdout('Finished deleting all products.' . PHP_EOL);
        return ExitCode::OK;

    }

    public function actionPurgeUnusedAssets(): int
    {
        // Find any asset IDs that aren't related to anything
        $assetIds = (new Query())
            ->select(['a.id'])
            ->from(['a' => Table::ASSETS])
            ->leftJoin(Table::RELATIONS . ' r', '[[r.targetId]] = [[a.id]]')
            ->where(['r.id' => null])
            ->column();

        if (empty($assetIds)) {
            $this->stdout('No unrelated assets to delete.' . PHP_EOL);
            return ExitCode::OK;
        }

        // Now fetch and delete them
        $assets = Asset::find()
            ->id($assetIds)
            ->all();

        $this->stdout('Found ' . count($assets) . ' unrelated assets.' . PHP_EOL);

        foreach ($assets as $asset) {
            $this->stdout(" - Deleting asset {$asset->filename} ... ");
            Craft::$app->elements->deleteElement($asset);
            $this->stdout('done' . PHP_EOL, Console::FG_GREEN);
        }

        $this->stdout('Finished deleting unrelated assets.' . PHP_EOL);
        return ExitCode::OK;
    }
}
