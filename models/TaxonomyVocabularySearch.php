<?php

namespace artkost\taxonomy\models;

use yii\data\ActiveDataProvider;

/**
 * Taxonomy Vocabulary models search model.
 */
class TaxonomyVocabularySearch extends TaxonomyVocabulary
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            // Integer
            [['id', 'type', 'nested'], 'integer'],
            // String
            [['name', 'title'], 'string', 'max' => 255],
            // Status
            ['status_id', 'in', 'range' => array_keys(self::statusLabels())],
            // Date
            [['created_at', 'updated_at'], 'date', 'format' => 'd.m.Y']
        ];
    }

    /**
     * Creates data provider instance with search query applied.
     *
     * @param array $params Search params
     *
     * @return ActiveDataProvider DataProvider
     */
    public function search($params)
    {
        $query = self::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere(
            [
                'id' => $this->id,
                'type' => $this->type,
                'nested' => $this->nested,
                'status_id' => $this->status_id,
                'FROM_UNIXTIME(created_at, "%d.%m.%Y")' => $this->created_at,
                'FROM_UNIXTIME(updated_at, "%d.%m.%Y")' => $this->updated_at
            ]
        );

        $query->andFilterWhere(['like', 'name', $this->name]);

        if ($this->title) {
            $query->andFilterWhere(['like', 'title', $this->title]);
        }

        return $dataProvider;
    }
}
