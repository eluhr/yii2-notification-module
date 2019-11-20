<?php
/**
 * @link http://www.diemeisterei.de/
 * @copyright Copyright (c) 2019 diemeisterei GmbH, Stuttgart
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace eluhr\notification\assets;


use yii\web\AssetBundle;

/**
 * @package eluhr\notification\assets
 * @author Elias Luhr <e.luhr@herzogkommunikation.de>
 */
class BackendNotificationAsset extends AssetBundle
{
    public $sourcePath = __DIR__ . '/web/backend';

    public $js = [
        'js/filter-form.js'
    ];

    public $depends = [
        NotificationAsset::class
    ];
}
