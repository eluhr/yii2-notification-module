<?php


namespace eluhr\notification\components\helpers;


use eluhr\notification\components\helpers\Message as MessageHelper;
use eluhr\notification\models\InboxMessage;
use eluhr\notification\models\Message;
use eluhr\notification\models\MessageUserGroup;
use eluhr\notification\models\search\InboxMessage as InboxMessageSearch;
use rmrevin\yii\fontawesome\FA;
use Yii;
use yii\bootstrap\ButtonDropdown;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\i18n\Formatter;
use yii\widgets\LinkPager;

/**
 * @package eluhr\notification\components\helpers
 * @author Elias Luhr <elias.luhr@gmail.com>
 */
class Inbox
{
    /**
     * @param ActiveDataProvider $dataProvider
     * @param InboxMessageSearch $searchModel
     *
     * @return array
     */
    public static function inboxGridViewConfig($dataProvider, $searchModel, $checkboxEnabled = false)
    {
        return [
            'dataProvider' => $dataProvider,
            'pager' => [
                'class' => LinkPager::class,
                'maxButtonCount' => 5,
                'firstPageLabel' => FA::icon(FA::_CHEVRON_LEFT),
                'lastPageLabel' => FA::icon(FA::_CHEVRON_RIGHT),
            ],
            'filterModel' => $searchModel,
            'layout' => "<div class=\"box-body no-padding\">{items}</div><div class=\"box-footer\">{summary}\n{pager}</div>",
            'tableOptions' => [
                'class' => 'table table-hover'
            ],
            'rowOptions' => function (InboxMessage $model) {
                return [
                    //'onclick' => 'window.location.href="' . Url::to(['read', 'inboxMessageId' => $model->id]) . '";'
                ];
            },
            'showHeader' => true,
            'formatter' => [
                'class' => Formatter::class,
                'nullDisplay' => ''
            ],
            'emptyText' => Yii::t('notification', 'You have read all messages in your inbox!'),
            'columns' => [
                [
                    'class' => 'yii\grid\CheckboxColumn',
                    'name' => 'checked',
                    'visible' => $checkboxEnabled,
                    'checkboxOptions' => function ($model) {
                        return ["value" => $model->id];
                    }
                ],
                [
                    'value' => function (InboxMessage $model) {
                        return Html::a(FA::icon((int)$model->marked === 0 ? FA::_FLAG_O : FA::_FLAG,
                            ['class' => 'text-warning']), ['mark-inbox-message', 'inboxMessageId' => $model->id],
                            ['data-method' => 'post', 'class' => 'no-border']);
                    },
                    'format' => 'raw'
                ],
                [
                    'value' => function (InboxMessage $model) {
                        return Html::a(MessageHelper::concatenateInboxMessageSenderNames($model),
                            Url::to(['read', 'inboxMessageId' => $model->id]),
                            ['data-method' => 'post', 'class' => 'no-border message-link']);
                    },
                    'format' => 'raw'
                ],

                [
                    'value' => function (InboxMessage $model) {
                        return Html::encode($model->message->subject);
                    }
                ],
                [
                    'value' => function (InboxMessage $model) {
                        return Yii::$app->formatter->asRelativeTime($model->message->send_at);
                    }
                ],
                [
                    'value' => function (InboxMessage $model) {
                        return Html::tag('b', str_repeat('!', (int)$model->message->priority));
                    },
                    'format' => 'raw'
                ],
                [
                    'value' => function (InboxMessage $model) {
                        return ButtonDropdown::widget([
                            'label' => FA::icon(FA::_ELLIPSIS_H),
                            'encodeLabel' => false,
                            'containerOptions' => [
                                'class' => 'pull-right'
                            ],
                            'dropdown' => [
                                'items' => [
                                    [
                                        'label' => Yii::t('notification', 'Read'),
                                        'url' => ['read', 'inboxMessageId' => $model->id]
                                    ],
                                    [
                                        'label' => (int)$model->marked === 0 ? Yii::t('notification',
                                            'Mark') : Yii::t('notification', 'Unmark'),
                                        'url' => ['mark-inbox-message', 'inboxMessageId' => $model->id],
                                        'linkOptions' => [
                                            'data-method' => 'post',
                                            'class' => 'text-danger no-border',
                                        ]
                                    ],
                                    [
                                        'label' => Yii::t('notification', 'Delete'),
                                        'url' => [
                                            'delete-inbox-message',
                                            'inboxMessageId' => $model->id,
                                        ],
                                        'linkOptions' => [
                                            'class' => 'text-danger no-border',
                                            'data-method' => 'post'
                                        ]
                                    ]
                                ]
                            ]
                        ]);
                    },
                    'format' => 'raw'
                ]
            ]
        ];
    }

    /**
     * @param ActiveDataProvider $dataProvider
     * @param InboxMessageSearch|\eluhr\notification\models\search\MessageUserGroup $searchModel
     *
     * @return array
     */
    public static function userGroupGridViewConfig($dataProvider, $searchModel)
    {
        return [
            'dataProvider' => $dataProvider,
            'pager' => [
                'class' => LinkPager::class,
                'maxButtonCount' => 5,
                'firstPageLabel' => FA::icon(FA::_CHEVRON_LEFT),
                'lastPageLabel' => FA::icon(FA::_CHEVRON_RIGHT),
            ],
            'filterModel' => $searchModel,
            'layout' => "<div class=\"box-body no-padding\">{items}</div><div class=\"box-footer\">{summary}\n{pager}</div>",
            'tableOptions' => [
                'class' => 'table table-hover'
            ],
            'rowOptions' => function (MessageUserGroup $model) {
                return [
                    'onclick' => 'window.location.href="' . Url::to([
                            'user-group-edit',
                            'messageUserGroupId' => $model->id
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

                        $receiversCount = count($model->receivers);
                        if (!empty($model->receivers) && $receiversCount > 1) {
                            $label = $model->receivers[0]->username . ' +' . ($receiversCount - 1);
                        } else {
                            $label = $receiversCount === 0 ? 0 : $model->receivers[0]->username;
                        }
                        return Html::tag('span', $label, ['class' => 'label label-primary']);
                    },
                    'format' => 'raw'
                ],
                [
                    'value' => function (MessageUserGroup $model) {
                        return ButtonDropdown::widget([
                            'label' => FA::icon(FA::_ELLIPSIS_H),
                            'encodeLabel' => false,
                            'containerOptions' => [
                                'class' => 'pull-right'
                            ],
                            'dropdown' => [
                                'items' => [
                                    [
                                        'label' => Yii::t('notification', 'Edit'),
                                        'url' => ['user-group-edit', 'messageUserGroupId' => $model->id]
                                    ],
                                    [
                                        'label' => Yii::t('notification', 'Delete'),
                                        'url' => [
                                            'delete-user-group',
                                            'messageUserGroupId' => $model->id,
                                        ],
                                        'linkOptions' => [
                                            'class' => 'text-danger no-border',
                                            'data-method' => 'post'
                                        ]
                                    ]
                                ]
                            ]
                        ]);
                    },
                    'format' => 'raw'
                ]
            ]
        ];
    }

    /**
     * @param ActiveDataProvider $dataProvider
     * @param InboxMessageSearch $searchModel
     *
     * @return array
     */
    public static function sentGridViewConfig($dataProvider, $searchModel, $checkboxEnabled = false)
    {
        return [
            'dataProvider' => $dataProvider,
            'pager' => [
                'class' => LinkPager::class,
                'maxButtonCount' => 5,
                'firstPageLabel' => FA::icon(FA::_CHEVRON_LEFT),
                'lastPageLabel' => FA::icon(FA::_CHEVRON_RIGHT),
            ],
            'filterModel' => $searchModel,
            'layout' => "<div class=\"box-body no-padding\">{items}</div><div class=\"box-footer\">{summary}\n{pager}</div>",
            'tableOptions' => [
                'class' => 'table table-hover'
            ],
            'rowOptions' => function (Message $model) {
                return [
                    // 'onclick' => 'window.location.href="' . Url::to(['read-sent', 'messageId' => $model->id]) . '";'
                ];
            },
            'showHeader' => false,
            'emptyText' => Yii::t('notification', 'No messages found.'),
            'emptyTextOptions' => ['class' => 'h4 text-muted'],
            'columns' => [
                [
                    'value' => function (Message $model) {
                        return Html::a(Html::encode($model->subject),
                            Url::to(['read-sent', 'messageId' => $model->id]),
                            ['data-method' => 'post', 'class' => 'no-border message-link']);
                    },
                    'format' => 'raw'
                ],
                [
                    'value' => function (Message $model) {
                        return Yii::$app->formatter->asRelativeTime($model->send_at);
                    }
                ],
                [
                    'value' => function (Message $model) {
                        return Html::tag('b', str_repeat('!', (int)$model->priority));
                    },
                    'format' => 'raw'
                ],
                [
                    'value' => function (Message $model) {
                        return ButtonDropdown::widget([
                            'label' => FA::icon(FA::_ELLIPSIS_H),
                            'encodeLabel' => false,
                            'containerOptions' => [
                                'class' => 'pull-right'
                            ],
                            'dropdown' => [
                                'items' => [
                                    [
                                        'label' => Yii::t('notification', 'Read'),
                                        'url' => ['read-sent', 'messageId' => $model->id]
                                    ]
                                ]
                            ]
                        ]);
                    },
                    'format' => 'raw'
                ]
            ]
        ];
    }
}
