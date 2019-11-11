<?php


namespace eluhr\notification\models\search;

use eluhr\notification\models\MessageUserGroup as MessageUserGroupModel;
use yii\data\ActiveDataProvider;

/**
 * @package eluhr\notification\models
 * @author Elias Luhr <elias.luhr@gmail.com>
 *
 * --- PROPERTIES ---
 *
 * @property string $q
 */
class MessageUserGroup extends MessageUserGroupModel
{
    public $q;

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
            ['q', 'safe']
        ];
    }

    /**
     * @param $params
     *
     * @return ActiveDataProvider
     * @throws \yii\base\InvalidConfigException
     */
    public function search($params)
    {
        $query = MessageUserGroupModel::find();

        $activeDataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        $query->orFilterWhere(['LIKE', 'name', $this->q]);

        $query->joinWith('receivers');
        $query->orFilterWhere(['LIKE', 'username', $this->q]);

        $query->orderBy(['created_at' => SORT_DESC]);
        $query->own();
        $query->groupBy([MessageUserGroupModel::tableName().'.id']);

        return $activeDataProvider;
    }
}
