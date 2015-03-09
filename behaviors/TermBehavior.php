<?php

namespace artkost\taxonomy\behaviors;

use artkost\taxonomy\models\TaxonomyTerm;
use artkost\taxonomy\models\TaxonomyVocabulary;
use Yii;
use yii\base\Behavior;
use yii\base\InvalidConfigException;
use yii\base\InvalidParamException;
use yii\db\ActiveRecord;

/**
 * Class TermBehavior
 * @package artkost\taxonomy\behaviors
 */
class TermBehavior extends Behavior
{
    /**
     *
     * @var array Attributes array
     */
    public $attributes = [];

    protected $instances = [];
    /**
     * @var TaxonomyVocabulary[]
     */
    protected $models = [];

    /**
     * @inheritdoc
     */
    public function attach($owner)
    {
        parent::attach($owner);

        if (!is_array($this->attributes) || empty($this->attributes)) {
            throw new InvalidParamException('Invalid or empty attributes array.');
        } else {
            foreach ($this->attributes as $attribute => $config) {

                if (!isset($config['name'])) {
                    throw new InvalidConfigException("'name' key not defined in term attribute config");
                }

                $model = $this->getVocabularyByName($config['name']);

                if (isset($config['create']) && $config['create'] && $model == false) {
                    $model = $this->createVocabularyByName($config['name']);
                }

                if ($model == false) {
                    throw new InvalidConfigException("Vocabulary with name {$config['name']} doesn't exist, please create it");
                }

                $this->instances[$attribute] = $model;
            }
        }
    }

    /**
     * @return array
     */
    public function getTermAttributes()
    {
        return $this->attributes;
    }

    /**
     * @param string $name
     * @return array
     */
    public function getTermAttribute($name)
    {
        return isset($this->attributes[$name]) ? $this->attributes[$name] : null;
    }

    /**
     * @param $name string
     * @return TaxonomyVocabulary|null
     */
    protected function getVocabularyByName($name)
    {
        $models = TaxonomyVocabulary::getModelsArray();

        return isset($models[$name]) ? $models[$name] : false;
    }

    /**
     * Get attached instance by attribute name
     * @param $name
     * @return TaxonomyTerm|null
     */
    public function getTermVocabulary($name)
    {
        return isset($this->instances[$name]) ? $this->instances[$name] : null;
    }

    /**
     * Creates vocabulary
     * @param $name
     * @return TaxonomyVocabulary
     * @throws \yii\base\ErrorException
     */
    protected function createVocabularyByName($name)
    {
        return TaxonomyVocabulary::create($name);
    }
}
