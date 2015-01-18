<?php

namespace app\modules\taxonomy\models;

use yii\data\ActiveDataProvider;

/**
 * Comment models search model.
 */
class TaxonomyTermSearch extends TaxonomyTerm
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['vid', 'id'], 'integer'],
            [['name'], 'string', 'max' => 50],
            [['description'], 'string', 'max' => 255]
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
                'vid' => $this->vid,
            ]
        );

        $query->andFilterWhere(['like', 'name', $this->name]);

        if ($this->description) {
            $query->andFilterWhere(['like', 'description', $this->description]);
        }

        return $dataProvider;
    }
}
