<?php
/**
 * Purge Assets plugin for Craft CMS 3.x
 *
 * A plugin to purge your unused assets, disabled products, and more. 
 *
 * @link      https://brambeekman.com
 * @copyright Copyright (c) 2021 Bram Beekman
 */

namespace brambeekman\purgeassets\controllers;

use brambeekman\purgeassets\PurgeAssets;

use craft\commerce\elements\Product;
use craft\elements\Entry;
use Craft;
use craft\web\Controller;
use craft\db\Query;
use craft\db\Table;
use craft\elements\Asset;

use yii\console\ExitCode;
use yii\helpers\Console;

/**
 * @author    Bram Beekman
 * @package   PurgeAssets
 * @since     1.0.0
 */
class PurgeController extends Controller
{
    
    public $deleteAllTrashed = true;

    public function actionPurgeUnusedAssets()
    {

        $hardDelete = Craft::$app->getRequest()->getParam('hardDelete');

        // Find any asset IDs that aren't related to anything
        $assetIds = (new Query())
            ->select(['a.id'])
            ->from(['a' => Table::ASSETS])
            ->leftJoin(Table::RELATIONS . ' r', '[[r.targetId]] = [[a.id]]')
            ->where(['r.id' => null])
            ->column();

        if (empty($assetIds)) {
            $results = 'No unrelated assets to delete.';
            Craft::$app->getSession()->setNotice(Craft::t('purge-assets', $results));
            return $this->redirect('purge-assets');
        }

        // Now fetch and delete them
        $assets = Asset::find()
            ->id($assetIds)
            ->all();

        foreach ($assets as $asset) {
            Craft::$app->elements->deleteElement($asset);
        }

        if ($hardDelete == 1) {
            // After deleting the assets, hard delete them to
            $gc = Craft::$app->getGc();
            $deleteAllTrashed = $gc->deleteAllTrashed;
            $gc->deleteAllTrashed = $this->deleteAllTrashed;
            $gc->run(true);
            $gc->deleteAllTrashed = $deleteAllTrashed;
        }

        $results = 'Finished deleting ' . count($assets) . ' unrelated assets.';
        Craft::$app->getSession()->setNotice(Craft::t('purge-assets', $results));
        return $this->redirect('purge-assets');
    }

    public function actionPurgeDisabledEntries()
    {

        $hardDelete = Craft::$app->getRequest()->getParam('hardDelete');

        // Fetch disabled products
        $entries = Entry::find()
            ->status('disabled')
            ->all();

        // if empty return with a message
        if (empty($entries)) {
            $results = 'No disabled entries to delete.';
            Craft::$app->getSession()->setNotice(Craft::t('purge-assets', $results));
            return $this->redirect('purge-assets');
        }

        // loop over the disabled products and delete them
        foreach ($entries as $entry) {
            Craft::$app->elements->deleteElement($entry);
        }

        if ($hardDelete == 1) {
            // After deleting the product, hard delete them to
            $gc = Craft::$app->getGc();
            $deleteAllTrashed = $gc->deleteAllTrashed;
            $gc->deleteAllTrashed = $this->deleteAllTrashed;
            $gc->run(true);
            $gc->deleteAllTrashed = $deleteAllTrashed;
        }

        // Finish with a message
        $results = 'Finished deleting ' . count($entries) . ' disabled entries.';
        Craft::$app->getSession()->setNotice(Craft::t('purge-assets', $results));
        return $this->redirect('purge-assets');

    }

    public function actionPurgeDisabledProducts()
    {

        $hardDelete = Craft::$app->getRequest()->getParam('hardDelete');

        // Fetch disabled products
        $products = Product::find()
            ->status('disabled')
            ->all();

        // if empty return with a message
        if (empty($products)) {
            $results = 'No disabled products to delete.';
            Craft::$app->getSession()->setNotice(Craft::t('purge-assets', $results));
            return $this->redirect('purge-assets');
        }

        // loop over the disabled products and delete them
        foreach ($products as $product) {
            Craft::$app->elements->deleteElement($product);
        }

        if ($hardDelete == 1) {
            // After deleting the product, hard delete them to
            $gc = Craft::$app->getGc();
            $deleteAllTrashed = $gc->deleteAllTrashed;
            $gc->deleteAllTrashed = $this->deleteAllTrashed;
            $gc->run(true);
            $gc->deleteAllTrashed = $deleteAllTrashed;
        }

        // Finish with a message
        $results = 'Finished deleting ' . count($products) . ' disabled products.';
        Craft::$app->getSession()->setNotice(Craft::t('purge-assets', $results));
        return $this->redirect('purge-assets');

    }
}
