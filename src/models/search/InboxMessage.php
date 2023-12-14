<?php


namespace eluhr\notification\models\search;

use Da\User\Model\User;
use eluhr\notification\models\InboxMessage as InboxMessageModel;
use eluhr\notification\models\Message;
use yii\data\ActiveDataProvider;

/**
 * @package eluhr\notification\models
 * @author Elias Luhr <elias.luhr@gmail.com>
 *
 * --- PROPERTIES ---
 *
 * @property string $q
 * @property integer $sort
 */
class InboxMessage extends InboxMessageModel
{
    public $q;
    public $sort;

    /**
     * @return string
     */
    public function formName()
    {
        return '';
    }

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['q','sort'], 'safe'],
            ['sort', 'integer'],
            ['q', 'filter', 'filter' => 'strip_tags']
        ];
    }

    /**
     * @param $params
     *
     * @param $read
     *
     * @return ActiveDataProvider
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\base\Exception
     */
    public function inboxSearch($params, $read)
    {
        $query = InboxMessageModel::find();

        $activeDataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'defaultPageSize' => 10,
                'pageParam' => ((int)$read === 0 ? 'inbox' : 'seen') . '-page',
                'pageSizeParam' => ((int)$read === 0 ? 'inbox' : 'seen') . '-per-page'
            ]
        ]);

        $this->load($params);

        $query->joinWith('message');
        $query->orFilterWhere(['LIKE', 'text', $this->q]);
        $query->orFilterWhere(['LIKE', 'subject', $this->q]);

        $query->leftJoin(User::tableName(), Message::tableName() . '.author_id = ' . User::tableName() . '.id');
        $query->orFilterWhere(['LIKE', 'username', $this->q]);

        $query->andWhere(['read' => (int)$read]);
        $query->orderBy(['send_at' => empty($this->sort) || $this->sort === '0' ? SORT_DESC : SORT_ASC]);
        $query->own();

        return $activeDataProvider;
    }

    /**
     * @param $params
     *
     *
     * @return ActiveDataProvider
     * @throws \yii\base\InvalidConfigException
     */
    public function sentSearch($params)
    {
        $query = Message::find();

        $activeDataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        $query->orFilterWhere(['LIKE', 'text', $this->q]);
        $query->orFilterWhere(['LIKE', 'subject', $this->q]);

        $query->orderBy(['created_at' => SORT_DESC]);
        $query->own();

        return $activeDataProvider;
    }
}
