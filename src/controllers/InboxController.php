<?php

namespace eluhr\notification\controllers;


use eluhr\notification\assets\NotificationAsset;
use eluhr\notification\components\helpers\Permission;
use eluhr\notification\models\InboxMessage;
use eluhr\notification\models\Message;
use eluhr\notification\models\MessageUserGroup;
use eluhr\notification\models\search\InboxMessage as InboxMessageSearch;
use eluhr\notification\models\search\MessageUserGroup as MessageUserGroupSearch;
use Yii;
use yii\base\Action;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\Html;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * @package eluhr\notification\controllers
 * @author Elias Luhr <elias.luhr@gmail.com>
 */
class InboxController extends Controller
{
    /**
     * @return array
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['access'] = [
            'class' => AccessControl::class,
            'rules' => [
                [
                    'allow' => true,
                    'actions' => ['index', 'read', 'delete-inbox-message', 'sent', 'read-sent', 'delete-user-group'],
                    'roles' => ['@'],
                ],
                [
                    'allow' => true,
                    'actions' => ['compose'],
                    'matchCallback' => function () {
                        return Yii::$app->user->can(Permission::COMPOSE_A_MESSAGE);
                    }
                ],
                [
                    'allow' => true,
                    'actions' => ['user-group', 'user-group-edit', 'delete-user-group'],
                    'matchCallback' => function () {
                        return Yii::$app->user->can(Permission::USER_GROUPS);
                    }
                ]
            ]
        ];
        $behaviors['verb'] = [
            'class' => VerbFilter::class,
            'actions' => [
                'delete-inbox-message' => ['POST'],
                'delete-user-group' => ['POST']
            ]
        ];
        return $behaviors;
    }

    /**
     * @param Action $action
     *
     * @return bool
     * @throws \yii\web\BadRequestHttpException
     */
    public function beforeAction($action)
    {
        NotificationAsset::register($action->controller->view);
        return parent::beforeAction($action);
    }

    /**
     * @return string
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\base\Exception
     */
    public function actionIndex()
    {
        $inbox_message_search_model = new InboxMessageSearch();

        $unread_inbox_message_data_provider = $inbox_message_search_model->inboxSearch(Yii::$app->request->queryParams,
            false);
        $everything_else_inbox_message_data_provider = $inbox_message_search_model->inboxSearch(Yii::$app->request->queryParams,
            true);

        return $this->render('inbox', [
            'unread_inbox_message_data_provider' => $unread_inbox_message_data_provider,
            'everything_else_inbox_message_data_provider' => $everything_else_inbox_message_data_provider,
            'inbox_message_search_model' => $inbox_message_search_model
        ]);
    }

    /**
     * @return string
     * @throws \yii\base\InvalidConfigException
     */
    public function actionSent()
    {
        $inbox_message_search_model = new InboxMessageSearch();

        $send_inbox_message_data_provider = $inbox_message_search_model->sentSearch(Yii::$app->request->queryParams);

        return $this->render('sent', [
            'send_inbox_message_data_provider' => $send_inbox_message_data_provider,
            'inbox_message_search_model' => $inbox_message_search_model
        ]);
    }

    /**
     * @return string
     * @throws \yii\base\Exception
     * @throws \yii\base\InvalidConfigException
     */
    public function actionUserGroup()
    {
        $message_user_group_search_model = new MessageUserGroupSearch();
        $message_user_group_data_provider = $message_user_group_search_model->search(Yii::$app->request->queryParams);

        return $this->render('user-group', [
            'message_user_group_search_model' => $message_user_group_search_model,
            'message_user_group_data_provider' => $message_user_group_data_provider,
        ]);
    }

    /**
     * @param $inbox_message_id
     *
     * @return string
     * @throws \yii\base\InvalidConfigException
     * @throws NotFoundHttpException
     */
    public function actionRead($inbox_message_id)
    {
        /** @var InboxMessage|null $inbox_message_model */
        $inbox_message_model = InboxMessage::find()->own()->andWhere(['id' => $inbox_message_id])->one();

        if ($inbox_message_model === null) {
            throw new NotFoundHttpException(Yii::t('notification', 'Message not found.'));
        }


        if ($inbox_message_model->read === 0) {
            $inbox_message_model->read = 1;
            if (!$inbox_message_model->save()) {
                Yii::$app->session->addFlash('info',
                    Yii::t('notification', 'Cannot update read status of this message.'));
            }
        }


        return $this->render('read', [
            'inbox_message_model' => $inbox_message_model
        ]);
    }

    /**
     * @param $message_id
     *
     * @return string
     * @throws \yii\base\InvalidConfigException
     * @throws NotFoundHttpException
     */
    public function actionReadSent($message_id)
    {
        /** @var InboxMessage|null $inbox_message_model */
        $message_model = Message::find()->own()->andWhere(['id' => $message_id])->one();

        if ($message_model === null) {
            throw new NotFoundHttpException(Yii::t('notification', 'Message not found.'));
        }

        return $this->render('read-sent', [
            'message_model' => $message_model
        ]);
    }

    /**
     * @param $inbox_message_id
     *
     * @return \yii\web\Response
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\db\StaleObjectException
     */
    public function actionDeleteInboxMessage($inbox_message_id)
    {
        $inbox_message_model = InboxMessage::find()->own()->andWhere(['id' => $inbox_message_id])->one();

        if ($inbox_message_model === null) {
            throw new NotFoundHttpException(Yii::t('notification', 'Message not found.'));
        }

        if ($inbox_message_model->delete() !== false) {
            Yii::$app->session->addFlash('info', Yii::t('notification', 'Message has been removed.'));
        }
        return $this->redirect(['index']);
    }

    /**
     * @param $message_user_group_id
     *
     * @return \yii\web\Response
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\db\StaleObjectException
     */
    public function actionDeleteUserGroup($message_user_group_id)
    {
        $message_user_group_model = MessageUserGroup::find()->own()->andWhere(['id' => $message_user_group_id])->one();

        if ($message_user_group_model === null) {
            throw new NotFoundHttpException(Yii::t('notification', 'User group not found.'));
        }

        if ($message_user_group_model->delete() !== false) {
            Yii::$app->session->addFlash('info', Yii::t('notification', 'User group has been removed.'));
        }
        return $this->redirect(['user-group']);
    }

    /**
     * @param null $message_id
     * @param null $reply_to
     *
     * @return string
     */
    public function actionCompose($message_id = null, $reply_to = null)
    {
        $message_model = new Message();
        $message_model->author_id = Yii::$app->user->id;

        if ($message_model->load(Yii::$app->request->post())) {
            if ($message_model->validate() && $message_model->save()) {
                Yii::$app->session->addFlash('success', Yii::t('notification', 'Message send successfully.'));
                return $this->redirect(['index']);
            }

            Yii::error($message_model->errors, Message::class);
        }

        if ($message_id !== null) {
            $reply_message_model = Message::findOne($message_id);

            if ($reply_message_model !== null) {
                $message_model->subject = (!empty($reply_to) ? 'Re' : 'Fwd') . ': ' . $reply_message_model->subject;
                $message_model->text = '<br>' . Html::tag('blockquote', $reply_message_model->text);
                if (!empty($reply_to)) {
                    $message_model->receiver_ids[] = $reply_to;
                }
            }
        }

        return $this->render('compose', [
            'message_model' => $message_model
        ]);
    }

    /**
     * @param null|string $message_user_group_id
     *
     * @return string
     * @throws \yii\base\InvalidConfigException
     * @throws NotFoundHttpException
     */
    public function actionUserGroupEdit($message_user_group_id = null)
    {
        if ($message_user_group_id === null) {
            $message_user_group_model = new MessageUserGroup();
        } else {
            $message_user_group_model = MessageUserGroup::find()->andWhere(['id' => $message_user_group_id])->own()->one();

            if ($message_user_group_model === null) {
                throw new NotFoundHttpException(Yii::t('notification', 'User group does not exist.'));
            }
        }

        $message_user_group_model->owner_id = Yii::$app->user->id;

        if ($message_user_group_model->load(Yii::$app->request->post())) {
            if ($message_user_group_model->validate() && $message_user_group_model->save()) {
                Yii::$app->session->addFlash('success', Yii::t('notification', 'User group saved successfully.'));
                return $this->redirect(['user-group']);
            }

            Yii::error($message_user_group_model->errors, MessageUserGroup::class);
        }

        return $this->render('user-group-edit', [
            'message_user_group_model' => $message_user_group_model
        ]);
    }
}