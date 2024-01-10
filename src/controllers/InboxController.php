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
use yii\web\Response;

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
                        'unread',
                        'read',
                        'delete-inbox-message',
                        'sent',
                        'read-sent',
                        'delete-user-group',
                        'mark-inbox-message',
                        'context-action'
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
                'delete-user-group' => ['POST'],
                'context_action' => ['POST']
            ]
        ];
        return $behaviors;
    }

    /**
     * @param Action $action
     *
     * @throws \yii\web\BadRequestHttpException
     * @return bool
     */
    public function beforeAction($action)
    {
        BackendNotificationAsset::register($action->controller->view);
        return parent::beforeAction($action);
    }

    /**
     * @param $inboxMessageId
     *
     * @throws NotFoundHttpException
     * @throws \yii\base\InvalidConfigException
     * @return \yii\web\Response
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
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\base\Exception
     * @return string
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
     * @throws \yii\base\InvalidConfigException
     * @return string
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
     * @throws \yii\base\Exception
     * @throws \yii\base\InvalidConfigException
     * @return string
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
     * @throws \yii\base\InvalidConfigException
     * @throws NotFoundHttpException
     * @return Response
     */
    public function actionUnread($inboxMessageId)
    {
        if ($this->setMessageRead($inboxMessageId, 0)) {
            return $this->redirect(['index']);
        }
        throw new NotFoundHttpException(Yii::t('notification', 'Message not found.'));
    }

    /**
     * @param $inboxMessageId
     *
     * @throws \yii\base\InvalidConfigException
     * @throws NotFoundHttpException
     * @return string
     */
    public function actionRead($inboxMessageId)
    {
        $inboxMessageModel = $this->setMessageRead($inboxMessageId);

        return $this->render('read', [
            'inboxMessageModel' => $inboxMessageModel
        ]);
    }

    /**
     * @param $messageId
     *
     * @throws \yii\base\InvalidConfigException
     * @throws NotFoundHttpException
     * @return string
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
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\db\StaleObjectException
     * @return \yii\web\Response
     */
    public function actionDeleteInboxMessage($inboxMessageId)
    {
        $this->deleteInboxMessage($inboxMessageId);
        return $this->redirect(['index']);
    }

    /**
     * @param $messageUserGroupId
     *
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\db\StaleObjectException
     * @return \yii\web\Response
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
     * @return string|Response
     */
    public function actionCompose($messageId = null, $replyTo = null)
    {
        $userId = Yii::$app->user->id;
        $messageModel = new Message([
            'author_id' => $userId
        ]);

        if ($messageModel->load(Yii::$app->request->post())) {
            $messageModel->setAttribute('author_id', $userId);
            if ($messageModel->validate() && $messageModel->save()) {
                Yii::$app->session->addFlash('success', Yii::t('notification', 'Message send successfully.'));
                return $this->redirect(['index']);
            }

            Yii::error($messageModel->errors, Message::class);
        }

        if ($messageId !== null) {
            $replyMessageModel = Message::findOne($messageId);

            if ($replyMessageModel !== null) {
                $messageModel->subject = (!empty($replyTo) ? 'Re' : 'Fwd') . ': ' . Html::encode($replyMessageModel->subject);
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
     * @throws \yii\base\InvalidConfigException
     * @throws NotFoundHttpException
     * @return string
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

    /**
     * @return mixed
     */
    public function actionContextAction()
    {
        $post = \Yii::$app->request->post();
        $messages = $post['checked'] ?? [];

        if (!empty($messages)) {
            if (\Yii::$app->request->post(Message::SUBMIT_TYPE_NAME) === Message::DELETE_MESSAGE) {
                foreach ($messages as $messageId) {
                    $this->deleteInboxMessage($messageId);
                }
            } else if (\Yii::$app->request->post(Message::SUBMIT_TYPE_NAME) === Message::MARK_MESSAGE_AS_READ) {
                foreach ($messages as $messageId) {
                    $this->setMessageRead($messageId);
                }
            } /*
               TODO: Deleting sent messages currently throws a constraint violation
               else if (\Yii::$app->request->post(Message::SUBMIT_TYPE_NAME) === Message::DELETE_SENT_MESSAGE){
                foreach ($messages as $messageId){
                    $this->deleteSentMessage($messageId);
                }
            }*/
        }
        return $this->redirect(['index']);
    }

    /**
     * @param $inboxMessageId
     *
     * @return InboxMessage|null
     */
    private function setMessageRead($inboxMessageId, $readStatus = 1)
    {
        /** @var InboxMessage|null $inboxMessageModel */
        $inboxMessageModel = InboxMessage::find()->own()->andWhere(['id' => $inboxMessageId])->one();

        if ($inboxMessageModel === null) {
            throw new NotFoundHttpException(Yii::t('notification', 'Message not found.'));
        }

        $inboxMessageModel->read = $readStatus;
        if (!$inboxMessageModel->save()) {
            Yii::$app->session->addFlash('info',
                Yii::t('notification', 'Cannot update read status of this message.'));
        }
        return $inboxMessageModel;
    }

    /**
     * @param $inboxMessageId
     *
     * @return void
     */
    private function deleteInboxMessage($inboxMessageId)
    {
        $inboxMessageModel = InboxMessage::find()->own()->andWhere(['id' => $inboxMessageId])->one();
        $this->deleteMessage($inboxMessageModel);
    }

    /**
     * @param $inboxMessageId
     *
     * @return void
     */
    private function deleteSentMessage($inboxMessageId)
    {
        $inboxMessageModel = Message::find()->own()->andWhere(['id' => $inboxMessageId])->one();
        $this->deleteMessage($inboxMessageModel);
    }

    /**
     * @param $messageModel
     *
     * @return void
     */
    private function deleteMessage($messageModel)
    {
        if ($messageModel === null) {
            throw new NotFoundHttpException(Yii::t('notification', 'Message not found.'));
        }

        if ($messageModel->delete() !== false) {
            Yii::$app->session->addFlash('info', Yii::t('notification', 'Message has been removed.'));
        } else {
            Yii::$app->session->addFlash('error', Yii::t('notification', 'There was a problem delete this message.'));
        }
    }
}
