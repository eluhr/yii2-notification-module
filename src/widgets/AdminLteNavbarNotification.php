<?php
/**
 * @link http://www.diemeisterei.de/
 * @copyright Copyright (c) 2019 diemeisterei GmbH, Stuttgart
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace eluhr\notification\widgets;


use eluhr\notification\assets\NotificationAsset;
use eluhr\notification\models\InboxMessage;
use Yii;
use yii\base\Widget;

/**
 * @package eluhr\notification\models
 * @author Elias Luhr <e.luhr@herzogkommunikation.de>
 */
class AdminLteNavbarNotification extends Widget
{
    public $moduleId = 'notification';

    public function run()
    {
        $this->registerAssets();
        $inboxMessages = InboxMessage::find()
            ->own()
            ->unread()
            ->joinWith('message')
            ->orderBy(['send_at' => SORT_DESC])
            ->all();
        $inboxMessagesCount = count($inboxMessages);
        return $this->render('admin-lte-navbar-notification', [
            'inboxMessages' => $inboxMessages,
            'moduleId' => $this->moduleId,
            'inboxMessagesCount' => $inboxMessagesCount,
            'hasMessages' => $inboxMessagesCount > 0
        ]);
    }

    private function registerAssets()
    {
        NotificationAsset::register($this->view);
    }

}
