<?php


namespace eluhr\notification\components\helpers;


use eluhr\notification\models\InboxMessage;
use eluhr\notification\models\Message;
use eluhr\notification\models\MessageUserGroup;
use eluhr\notification\models\search\InboxMessage as InboxMessageSearch;
use rmrevin\yii\fontawesome\FA;
use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\LinkPager;

/**
 * @package eluhr\notification\components\helpers
 * @author Elias Luhr <elias.luhr@gmail.com>
 */
class Inbox
{
    /**
     * @param ActiveDataProvider $data_provider
     * @param InboxMessageSearch $search_model
     *
     * @return array
     */
    public static function inboxGridViewConfig($data_provider, $search_model)
    {
        return [
            'dataProvider' => $data_provider,
            'pager' => [
                'class' => LinkPager::class,
                'firstPageLabel' => FA::icon(FA::_CHEVRON_LEFT),
                'lastPageLabel' => FA::icon(FA::_CHEVRON_RIGHT),
            ],
            'filterModel' => $search_model,
            'layout' => "<div class=\"box-body no-padding\">{items}</div><div class=\"box-footer\">{summary}\n{pager}</div>",
            'tableOptions' => [
                'class' => 'table table-hover'
            ],
            'rowOptions' => function (InboxMessage $model) {
                return [
                    'onclick' => 'window.location.href="' . Url::to(['read', 'inbox_message_id' => $model->id]) . '";'
                ];
            },
            'showHeader' => false,
            'emptyText' => Yii::t('notification', 'You have read all messages in your inbox!'),
            'columns' => [
                [
                    'value' => function (InboxMessage $model) {
                        return $model->receiver->username;
                    }
                ],
                [
                    'value' => function (InboxMessage $model) {
                        return $model->message->subject;
                    }
                ],
                [
                    'value' => function (InboxMessage $model) {
                        return Yii::$app->formatter->asRelativeTime($model->message->send_at);
                    }
                ]
            ]
        ];
    }

    /**
     * @param ActiveDataProvider $data_provider
     * @param InboxMessageSearch $search_model
     *
     * @return array
     */
    public static function userGroupGridViewConfig($data_provider, $search_model)
    {
        return [
            'dataProvider' => $data_provider,
            'pager' => [
                'class' => LinkPager::class,
                'firstPageLabel' => FA::icon(FA::_CHEVRON_LEFT),
                'lastPageLabel' => FA::icon(FA::_CHEVRON_RIGHT),
            ],
            'filterModel' => $search_model,
            'layout' => "<div class=\"box-body no-padding\">{items}</div><div class=\"box-footer\">{summary}\n{pager}</div>",
            'tableOptions' => [
                'class' => 'table table-hover'
            ],
            'rowOptions' => function (MessageUserGroup $model) {
                return [
                    'onclick' => 'window.location.href="' . Url::to([
                            'user-group-edit',
                            'message_user_group_id' => $model->id
                        ]) . '";'
                ];
            },
            'showHeader' => false,
            'emptyText' => Yii::t('notification', 'There are no user groups yet.'),
            'columns' => [
                [
                    'value' => function (MessageUserGroup $model) {
                        return $model->name;
                    }
                ],
                [
                    'value' => function (MessageUserGroup $model) {

                        $receivers_count = count($model->receivers);
                        if (!empty($model->receivers) && $receivers_count > 1) {
                            $label = $model->receivers[0]->username . ' +' . ($receivers_count - 1);
                        } else if ($receivers_count === 0) {
                            $label = 0;
                        } else {
                            $label = $model->receivers[0]->username;
                        }
                        return Html::tag('span', $label, ['class' => 'label label-primary']);
                    },
                    'format' => 'raw'
                ]
            ]
        ];
    }

    /**
     * @param ActiveDataProvider $data_provider
     * @param InboxMessageSearch $search_model
     *
     * @return array
     */
    public static function sentGridViewConfig($data_provider, $search_model)
    {
        return [
            'dataProvider' => $data_provider,
            'pager' => [
                'class' => LinkPager::class,
                'firstPageLabel' => FA::icon(FA::_CHEVRON_LEFT),
                'lastPageLabel' => FA::icon(FA::_CHEVRON_RIGHT),
            ],
            'filterModel' => $search_model,
            'layout' => "<div class=\"box-body no-padding\">{items}</div><div class=\"box-footer\">{summary}\n{pager}</div>",
            'tableOptions' => [
                'class' => 'table table-hover'
            ],
            'rowOptions' => function (Message $model) {
                return [
                    'onclick' => 'window.location.href="' . Url::to(['read-sent', 'message_id' => $model->id]) . '";'
                ];
            },
            'showHeader' => false,
            'emptyText' => Yii::t('notification', 'No messages found.'),
            'emptyTextOptions' => ['class' => 'h4 text-muted'],
            'columns' => [
                [
                    'value' => function (Message $model) {
                        return $model->subject;
                    }
                ],
                [
                    'value' => function (Message $model) {
                        return Yii::$app->formatter->asRelativeTime($model->send_at);
                    }
                ]
            ]
        ];
    }
}