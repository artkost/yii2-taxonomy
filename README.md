# Taxonomy module for Yii2
This module inspired by Drupal taxonomy system and works same.



## Usage

### Configuration

```php

return [
  'modules' => [
    'taxonomy' => [
      'class' => '\artkost\taxonomy\Module'
    ]
  ]
]

```
We have tables

`news_post`
  - id
  - title
  - slug
  - content
  - updated_at
  - created_at

`news_term`
  - post_id // news_post.id news post id
  - vocab_id // taxonomy_term.vid because we can have many types of categories, tags etc.
  - term_id // taxonomy_term.id term id


### Usage

Attach behavior to Model

```php
class NewsTerm extends ActiveRecord 
{

  public static function saveTerm($vocabID, $postID, $termID)
  {
    return (new self([
      'post_id' => $postID,
      'vocab_id' => $vocabID,
      'term_id' => $termID,
    ]))->save();
  }

  public static function saveTerms($vocabID, $postID, array $termIDs)
  {
    foreach ($termIDs as $termID) {
      self::saveTerm($vocabID, $postID, $termID);
    }
  }

  public static function removeTerm($vocabID, $postID, $termID = false)
  {
      $condition = [
        'post_id' => $postID,
        'vocab_id' => $vocabID
      ];

      if ($termID) {
          $condition['term_id'] = $termID;
          TaxonomyTerm:: deleteAll(['id' => $termID]);
      } else {
          TaxonomyTerm:: deleteAll(['vid' => $vocabID]);
      }

      return self::deleteAll($condition);
  }

}

class NewsPost extends ActiveRecord {
  public function behaviors()
  {
    return [
      'termBehavior' => [
        'class' => artkost\taxonomy\behaviors\TermBehavior::className(),
        'vocabularies' => [
          'category' => [
            'name' => 'news-category', //machine name of vocab, must be uniq for whole project
            'create' => true // auto create if not exists
          ],
          'tags' => [
            'name' => 'news-tags',
            'create' => true
          ],
        ]
      ]
    ];
  }
  
  public static function find()
  {
      return (new ActiveQuery(get_called_class()))
          ->with('tagsTerms', 'categoryTerm');
  }

  /**
   * @return \artkost\taxonomy\models\TaxonomyVocabulary
   */
  public function getCategory()
  {
    return $this->getVocabulary('category');
  }

  /**
   * @return TaxonomyTerm
   */
  public function getCategoryTerm()
  {
    $vocabID = $this->category->id;

    return $this->hasOne(TaxonomyTerm::className(), ['id' => 'term_id'])
        ->viaTable(NewsTerm::tableName(), ['post_id' => 'id'], function ($query) use ($vocabID) {
            return $query->andWhere(['vocab_id' => $vocabID]);
        });
  }

  public function setCategoryTerm($termID)
  {
    $this->categoryID = (int) $termID;
  }

  /**
   * @return \artkost\taxonomy\models\TaxonomyVocabulary
   */
  public function getTags()
  {
    return $this->getVocabulary('tags');
  }

  /**
   * @return TaxonomyTerm[]
   */
  public function getTagsTerms()
  {
    $vocabID = $this->tags->id;

    return $this->hasMany(TaxonomyTerm::className(), ['id' => 'term_id'])
        ->viaTable(NewsTerm::tableName(), ['post_id' => 'id'], function ($query) use ($vocabID) {
            return $query->andWhere(['vocab_id' => $vocabID]);
        });
  }

  public function setTagsTerms(array $termsIDs)
  {
    $this->tagsIDs = $termIDs;
  }
  
  public function afterSave($insert, $changedAttributes)
  {
    // we can have only one category per news post
    if ($this->categoryID) {
      NewsTerm::removeTerm($this->category->id, $this->id);
      NewsTerm::saveTerm($this->category->id, $this->id, $this->categoryID);
    }
    
    if (!empty($this->tagsIDs)) {
      NewsTerm::removeTerm($this->tags->id, $this->id); //removes all
      NewsTerm::saveTerms($this->tags->id, $this->id, $this->tagsIDs);
    }
  }
}
```

In view or controller you can access 
