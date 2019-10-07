<?php

namespace fcm\manager\models\searchs;

use fcm\manager\models\FcmNotification as FcmNotificationsModel;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * FcmNotifications represents the model behind the search form of `fcm\manager\models\FcmNotification`.
 */
class FcmNotification extends FcmNotificationsModel
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'status', 'delay_time'], 'integer'],
            [['title', 'body', 'target', 'extra_data', 'create_datetime', 'update_datetime'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = FcmNotificationsModel::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id'              => $this->id,
            'status'          => $this->status,
            'delay_time'      => $this->delay_time,
            'create_datetime' => $this->create_datetime,
            'update_datetime' => $this->update_datetime,
        ]);

        $query->andFilterWhere(['ilike', 'title', $this->title])
            ->andFilterWhere(['ilike', 'body', $this->body])
            ->andFilterWhere(['ilike', 'target', $this->target])
            ->andFilterWhere(['ilike', 'extra_data', $this->extra_data]);

        return $dataProvider;
    }
}
