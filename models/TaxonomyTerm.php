<?php

namespace artkost\taxonomy\models;

use artkost\taxonomy\Taxonomy;
use Yii;
use yii\db\ActiveRecord;
use yii\helpers\Inflector;

/**
 * This is the model class for table "taxonomy_term".
 *
 * @property integer $id
 * @property integer $vid
 * @property string $name
 * @property string $description
 *
 * @property string $nameSlug
 */
class TaxonomyTerm extends ActiveRecord
{
    /**
     * @var int
     */
    public $parent_id;
    /**
     * @var self
     */
    protected $_parent;
    /**
     * @var array self[]
     */
    protected $_child = [];

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%taxonomy_term}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['vid', 'name'], 'required'],
            [['vid', 'parent_id'], 'integer'],
            [['parent'], 'validateHasParent'],
            [['name'], 'string', 'max' => 50],
            [['description'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'parent_id' => 'Parent',
            'vid' => 'Vid',
            'name' => 'Name',
            'description' => 'Description',
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios['admin-create'] = ['name', 'description', 'parent_id'];
        $scenarios['admin-update'] = ['name', 'description', 'parent_id'];

        return $scenarios;
    }

    public function validateHasParent($attribute, $params)
    {
        if (!$this->hasErrors() && $this->getParent() == null) {
            $this->addError($attribute, Taxonomy::t('model', 'Invalid parent term id'));
        }
    }

    /**
     * @param TaxonomyTerm $parent
     * @return $this
     */
    public function setParent(self &$parent)
    {
        $this->_parent = $parent;
        $this->parent_id = $parent->id;
        return $this;
    }

    /**
     * @return TaxonomyTerm|null|static
     */
    public function getParent()
    {
        if (is_null($this->parent_id)) {
            /** @var TaxonomyTermHierarchy $hierarchy */
            $hierarchy = TaxonomyTermHierarchy::find()
                ->where(['child_id' => $this->id, 'vid' => $this->vid])
                ->one();

            $this->parent_id = $hierarchy ? $hierarchy->parent_id : 0;
        }

        if (is_null($this->_parent) && is_numeric($this->parent_id)) {
            $this->_parent = static::findOne($this->parent_id);
        }

        return $this->_parent;
    }

    /**
     * @return string
     */
    public function getNameSlug()
    {
        return Inflector::slug($this->name);
    }

    /**
     * @return static
     */
    public function getVocabulary()
    {
        return $this->hasOne(TaxonomyVocabulary::className(), ['id' => 'vid'])->inverseOf('terms');
    }

    /**
     * @param TaxonomyTerm $child
     */
    public function addChild(self &$child)
    {
        $this->_child[$child->id] = $child->setParent($this);
    }

    /**
     * @param $id
     * @return bool
     */
    public function hasChild($id)
    {
        return isset($this->_child[$id]);
    }

    /**
     * @return TaxonomyTerm[]
     */
    public function getChild()
    {
        return $this->_child;
    }

    /**
     * @param $vid
     * @param int $excludeId
     * @return array
     */
    public static function treeListData($vid, $excludeId = 0)
    {
        $terms = TaxonomyTermHierarchy::getTree($vid);

        $data = [];

        foreach ($terms as $term) {
            if ($excludeId != $term->id) {
                $data[$term->id] = $term->name;
            }

            if (!empty($term->childs)) {
                foreach ($term->childs as $child) {
                    if ($excludeId != $child->id) {
                        $data[$child->id] = $child->name;
                    }
                }
            }
        }

        return $data;
    }

    public function afterSave($insert, $changedAttributes)
    {
        TaxonomyTermHierarchy::addChild($this->id, $this->vid, $this->getParent() ? $this->getParent()->id : 0);

        parent::afterSave($insert, $changedAttributes);
    }

    /**
     * @param $from
     * @return TaxonomyTerm
     */
    public static function create($from)
    {
        return new self([
            'id' => $from['id'],
            'vid' => $from['vid'],
            'name' => $from['name'],
            'description' => $from['description']
        ]);
    }
}
