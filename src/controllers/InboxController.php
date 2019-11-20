<?php

namespace eluhr\notification\controllers;


use eluhr\notification\assets\BackendNotificationAsset;
use eluhr\notification\components\helpers\Permission;
use eluhr\notification\models\InboxMessage;
use eluhr\notification\models\Message;
use eluhr\notification\models\MessagePreferences;
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
                    'actions' => [
                        'index',
                        'preferences',
                        'read',
                        'delete-inbox-message',
                        'sent',
                        'read-sent',
                        'delete-user-group',
                        'mark-inbox-message'
                    ],
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
                'mark-inbox-message' => ['POST'],
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
        BackendNotificationAsset::register($action->controller->view);
        return parent::beforeAction($action);
    }

    /**
     * @param $inboxMessageId
     *
     * @return \yii\web\Response
     * @throws NotFoundHttpException
     * @throws \yii\base\InvalidConfigException
     */
    public function actionMarkInboxMessage($inboxMessageId)
    {
        /** @var InboxMessage|null $inboxMessageModel */
        $inboxMessageModel = InboxMessage::find()->own()->andWhere(['id' => $inboxMessageId])->one();

        if ($inboxMessageModel === null) {
            throw new NotFoundHttpException(Yii::t('notification', 'Message not found.'));
        }

        $inboxMessageModel->marked = !(int)$inboxMessageModel->marked;

        if (!$inboxMessageModel->save()) {
            Yii::$app->session->addFlash('info',
                Yii::t('notification', 'Cannot update read status of this message.'));
        }

        return $this->redirect(Yii::$app->request->referrer ?? ['index']);
    }

    /**
     * @return string
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\base\Exception
     */
    public function actionIndex()
    {
        $inboxMessageSearchModel = new InboxMessageSearch();

        return $this->render('inbox', [
            'unreadInboxMessageDataProvider' => $inboxMessageSearchModel->inboxSearch(Yii::$app->request->queryParams,
                false),
            'everythingElseInboxMessageDataProvider' => $inboxMessageSearchModel->inboxSearch(Yii::$app->request->queryParams,
                true),
            'inboxMessageSearchModel' => $inboxMessageSearchModel
        ]);
    }

    /**
     * @return string
     * @throws \yii\base\InvalidConfigException
     */
    public function actionSent()
    {
        $inboxMessageSearchModel = new InboxMessageSearch();

        return $this->render('sent', [
            'sendInboxMessageDataProvider' => $inboxMessageSearchModel->sentSearch(Yii::$app->request->queryParams),
            'inboxMessageSearchModel' => $inboxMessageSearchModel
        ]);
    }

    /**
     * @return string
     * @throws \yii\base\Exception
     * @throws \yii\base\InvalidConfigException
     */
    public function actionUserGroup()
    {
        $messageUserGroupSearchModel = new MessageUserGroupSearch();

        return $this->render('user-group', [
            'messageUserGroupSearchModel' => $messageUserGroupSearchModel,
            'messageUserGroupDataProvider' => $messageUserGroupSearchModel->search(Yii::$app->request->queryParams),
        ]);
    }

    /**
     * @param $inboxMessageId
     *
     * @return string
     * @throws \yii\base\InvalidConfigException
     * @throws NotFoundHttpException
     */
    public function actionRead($inboxMessageId)
    {
        /** @var InboxMessage|null $inboxMessageModel */
        $inboxMessageModel = InboxMessage::find()->own()->andWhere(['id' => $inboxMessageId])->one();

        if ($inboxMessageModel === null) {
            throw new NotFoundHttpException(Yii::t('notification', 'Message not found.'));
        }


        if ($inboxMessageModel->read === 0) {
            $inboxMessageModel->read = 1;
            if (!$inboxMessageModel->save()) {
                Yii::$app->session->addFlash('info',
                    Yii::t('notification', 'Cannot update read status of this message.'));
            }
        }


        return $this->render('read', [
            'inboxMessageModel' => $inboxMessageModel
        ]);
    }

    /**
     * @param $messageId
     *
     * @return string
     * @throws \yii\base\InvalidConfigException
     * @throws NotFoundHttpException
     */
    public function actionReadSent($messageId)
    {
        /** @var InboxMessage|null $inboxMessageModel */
        $messageModel = Message::find()->own()->andWhere(['id' => $messageId])->one();

        if ($messageModel === null) {
            throw new NotFoundHttpException(Yii::t('notification', 'Message not found.'));
        }

        return $this->render('read-sent', [
            'messageModel' => $messageModel
        ]);
    }

    /**
     * @param $inboxMessageId
     *
     * @return \yii\web\Response
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\db\StaleObjectException
     */
    public function actionDeleteInboxMessage($inboxMessageId)
    {
        $inboxMessageModel = InboxMessage::find()->own()->andWhere(['id' => $inboxMessageId])->one();

        if ($inboxMessageModel === null) {
            throw new NotFoundHttpException(Yii::t('notification', 'Message not found.'));
        }

        if ($inboxMessageModel->delete() !== false) {
            Yii::$app->session->addFlash('info', Yii::t('notification', 'Message has been removed.'));
        }
        return $this->redirect(['index']);
    }

    /**
     * @param $messageUserGroupId
     *
     * @return \yii\web\Response
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\db\StaleObjectException
     */
    public function actionDeleteUserGroup($messageUserGroupId)
    {
        $messageUserGroupModel = MessageUserGroup::find()->own()->andWhere(['id' => $messageUserGroupId])->one();

        if ($messageUserGroupModel === null) {
            throw new NotFoundHttpException(Yii::t('notification', 'User group not found.'));
        }

        if ($messageUserGroupModel->delete() !== false) {
            Yii::$app->session->addFlash('info', Yii::t('notification', 'User group has been removed.'));
        }
        return $this->redirect(['user-group']);
    }

    /**
     * @param null $messageId
     * @param null $replyTo
     *
     * @return string
     */
    public function actionCompose($messageId = null, $replyTo = null)
    {
        $messageModel = new Message();
        $messageModel->author_id = Yii::$app->user->id;

        if ($messageModel->load(Yii::$app->request->post())) {
            if ($messageModel->validate() && $messageModel->save()) {
                Yii::$app->session->addFlash('success', Yii::t('notification', 'Message send successfully.'));
                return $this->redirect(['index']);
            }

            Yii::error($messageModel->errors, Message::class);
        }

        if ($messageId !== null) {
            $replyMessageModel = Message::findOne($messageId);

            if ($replyMessageModel !== null) {
                $messageModel->subject = (!empty($replyTo) ? 'Re' : 'Fwd') . ': ' . $replyMessageModel->subject;
                $messageModel->text = '<br>' . Html::tag('blockquote', $replyMessageModel->text);
                if (!empty($replyTo)) {
                    $messageModel->receiverIds[] = $replyTo;
                }
            }
        }

        return $this->render('compose', [
            'messageModel' => $messageModel
        ]);
    }

    /**
     * @param null|string $messageUserGroupId
     *
     * @return string
     * @throws \yii\base\InvalidConfigException
     * @throws NotFoundHttpException
     */
    public function actionUserGroupEdit($messageUserGroupId = null)
    {
        if ($messageUserGroupId === null) {
            $messageUserGroupModel = new MessageUserGroup();
        } else {
            $messageUserGroupModel = MessageUserGroup::find()->andWhere(['id' => $messageUserGroupId])->own()->one();

            if ($messageUserGroupModel === null) {
                throw new NotFoundHttpException(Yii::t('notification', 'User group does not exist.'));
            }
        }

        $messageUserGroupModel->owner_id = Yii::$app->user->id;

        if ($messageUserGroupModel->load(Yii::$app->request->post())) {
            if ($messageUserGroupModel->validate() && $messageUserGroupModel->save()) {
                Yii::$app->session->addFlash('success', Yii::t('notification', 'User group saved successfully.'));
                return $this->redirect(['user-group']);
            }

            Yii::error($messageUserGroupModel->errors, MessageUserGroup::class);
        }

        return $this->render('user-group-edit', [
            'messageUserGroupModel' => $messageUserGroupModel
        ]);
    }

    public function actionPreferences()
    {
        $model = MessagePreferences::find()->own()->one();

        if ($model === null) {
            $model = new MessagePreferences([
                'user_id' => Yii::$app->user->id,
                'wants_to_additionally_receive_messages_by_mail' => 1
            ]);
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->refresh();
        }

        return $this->render('preferences', ['model' => $model]);
    }
}
