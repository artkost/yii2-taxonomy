<?php

namespace app\modules\taxonomy\widgets;

use app\modules\taxonomy\models\TaxonomyTerm;
use app\modules\taxonomy\models\TaxonomyTermHierarchy;
use app\modules\taxonomy\models\TaxonomyVocabulary;
use yii\base\InvalidConfigException;
use yii\bootstrap\Nav;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;

class TermsListNav extends Nav
{
    public $vocabularyName;

    public $termRoute = 'taxonomy/term';

    public $termCurrent = 0;

    protected $_terms = [];

    public function run()
    {
        if (!$this->vocabularyName) {
            throw new InvalidConfigException('vocabularyName not set');
        }

        foreach ($this->getTerms() as $term) {
            $this->items[$term->id]['label'] = $term->name;
            $this->items[$term->id]['url'] =  Url::toRoute([
                $this->termRoute,
                'term' => $term->id,
                'name' => $term->nameSlug
            ]);

            if ($this->termCurrent == $term->id) {
                $this->items[$term->id]['active'] = true;
            }

            if ($term->child) foreach ($term->child as $child) {
                /** @var $term TaxonomyTerm */
                $this->items[$term->id]['items'][$child->id] = [
                    'label' => $child->name,
                    'url' => Url::toRoute([
                        $this->termRoute,
                        'term' => $child->id,
                        'name' => $child->nameSlug
                    ])
                ];
            } else {
                /** @var $term TaxonomyTerm */
                $this->items[$term->id]['url'] = Url::toRoute([$this->termRoute, 'term' => $term->id, 'name' => $term->nameSlug]);
            }
        }

        parent::run();
    }

    /**
     * @return \app\modules\taxonomy\models\TaxonomyTerm[]|array
     */
    protected function getTerms()
    {
        if (empty($this->_terms)) {
            $vocabularies = TaxonomyVocabulary::getModelsArray();

            if (isset($vocabularies[$this->vocabularyName])) {
                $this->_terms = TaxonomyTermHierarchy::getTree($vocabularies[$this->vocabularyName]->id);
            }
        }

        return $this->_terms;
    }
}