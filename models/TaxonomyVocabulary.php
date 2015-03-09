<?php

namespace artkost\taxonomy\models;

use artkost\taxonomy\Module;
use Yii;
use yii\base\ErrorException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\helpers\Inflector;

/**
 * This is the model class for table "taxonomy_vocabulary".
 *
 * @property integer $id
 * @property string $name
 * @property string $title
 * @property integer $type
 * @property integer $nested
 * @property integer $status_id
 * @property integer $created_at
 * @property integer $updated_at
 */
class TaxonomyVocabulary extends ActiveRecord
{
    const STATUS_DISABLED = 0;
    const STATUS_ENABLED = 1;
    const CACHE_KEY = 'TaxonomyVocabulary';

    /**
     * @var self[] cached list of vocabularies
     */
    protected static $models;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%taxonomy_vocabulary}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'title'], 'required'],
            [['type', 'nested', 'status_id', 'created_at', 'updated_at'], 'integer'],
            [['name'], 'string', 'max' => 30],
            [['title'], 'string', 'max' => 255],
            [['name'], 'unique']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Module::t('model', 'ID'),
            'name' => Module::t('model', 'Name'),
            'title' => Module::t('model', 'Title'),
            'type' => Module::t('model', 'Type'),
            'nested' => Module::t('model', 'Nested'),
            'status_id' => Module::t('model', 'Status ID'),
            'created_at' => Module::t('model', 'Created At'),
            'updated_at' => Module::t('model', 'Updated At'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'timestampBehavior' => [
                'class' => TimestampBehavior::className(),
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios['admin-create'] = ['name', 'title'];
        $scenarios['admin-update'] = ['name', 'title'];

        return $scenarios;
    }

    /**
     * @inheritdoc
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        Yii::$app->cache->delete(self::CACHE_KEY);
    }

    /**
     * @return static
     */
    public function getTerms()
    {
        return $this->hasMany(TaxonomyTerm::className(), ['vid' => 'id'])->inverseOf('vocabulary');
    }

    /**
     * @param $id
     * @return TaxonomyTerm|bool
     */
    public function getTerm($id)
    {
        $terms = ArrayHelper::index($this->terms, 'id');

        return isset($terms[$id]) ? $terms[$id] : false;
    }

    /**
     * Get flat term data
     * @return array
     */
    public function getTermsListData()
    {
        return ArrayHelper::map($this->terms, 'id', 'name');
    }

    /**
     * Get tree of terms for select list
     * @param int $parentID
     * @return array
     */
    public function getTermsTreeData($parentID = 0)
    {
        $tree = TaxonomyTermHierarchy::getTree($this->id);

        $data = [];

        static::mapTreeDataRecursive($tree, $data, $parentID);

        return $data;
    }

    protected static function mapTreeDataRecursive($terms, &$data, $level, $delimiter = "--")
    {
        foreach ($terms as $term) {
            $data[$term->id] = str_repeat($delimiter, $level) . ' ' . $term->name;

            if (!empty($term->child)) {
                $childLevel = $level + 1;
                static::mapTreeDataRecursive($term->child, $data, $childLevel, $delimiter);
            }
        }
    }

    /**
     * @return self[]
     */
    public static function getModels()
    {
        if (empty(self::$models)) {
            self::$models = static::find()->all();
        }

        return self::$models;
    }

    /**
     * @return array Model array
     */
    public static function getModelsArray()
    {
        $array = Yii::$app->cache->get(self::CACHE_KEY);

        if ($array === false) {
            $array = ArrayHelper::index(self::getModels(), 'name');

            Yii::$app->cache->set(self::CACHE_KEY, $array, 0);
        }

        return $array;
    }

    /**
     * @return string Model readable status
     */
    public function getStatus()
    {
        return self::statusLabels()[$this->status_id];
    }

    /**
     * @return TaxonomyTerm[]
     */
    public function getTree()
    {
        return TaxonomyTermHierarchy::getTree($this->id);
    }

    /**
     * @return string
     */
    public function getNameSlug()
    {
        return Inflector::slug($this->name);
    }

    /**
     * @return array Status array.
     */
    public static function statusLabels()
    {
        return [
            self::STATUS_DISABLED => Module::t('model', 'Unpublished'),
            self::STATUS_ENABLED => Module::t('model', 'Published')
        ];
    }

    /**
     * Creates taxonomy vocabulary by name
     * @param $name
     * @return TaxonomyVocabulary
     * @throws ErrorException
     */
    public static function create($name)
    {
        $title = Inflector::titleize($name, true);
        $slug = Inflector::slug($name);
        $model = new self(['name' => $slug, 'title' => $title, 'status_id' => self::STATUS_ENABLED]);

        if ($model->save()) {
            return $model;
        } else {
            throw new ErrorException(implode(' ', $model->getFirstErrors()));
        }
    }
}
