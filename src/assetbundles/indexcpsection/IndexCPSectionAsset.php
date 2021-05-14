<?php
/**
 * Purge Assets plugin for Craft CMS 3.x
 *
 * A plugin to purge your unused assets, disabled products, and more. 
 *
 * @link      https://brambeekman.com
 * @copyright Copyright (c) 2021 Bram Beekman
 */

namespace brambeekman\purgeassets\assetbundles\indexcpsection;

use Craft;
use craft\web\AssetBundle;
use craft\web\assets\cp\CpAsset;

/**
 * @author    Bram Beekman
 * @package   PurgeAssets
 * @since     1.0.0
 */
class IndexCPSectionAsset extends AssetBundle
{
    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->sourcePath = "@brambeekman/purgeassets/assetbundles/indexcpsection/dist";

        $this->depends = [
            CpAsset::class,
        ];

        $this->js = [
            'js/Index.js',
        ];

        $this->css = [
            'css/Index.css',
        ];

        parent::init();
    }
}
