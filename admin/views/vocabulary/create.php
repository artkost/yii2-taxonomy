<?php

/**
 * TaxonomyVocabulary model create view.
 *
 * @var \yii\web\View $this View
 * @var TaxonomyVocabulary $model Model
 * @var array $statusArray Statuses array
 */

use artkost\taxonomy\Taxonomy;
use artkost\taxonomy\models\TaxonomyVocabulary;

$this->title = Taxonomy::t('admin', 'Taxonomy');
$this->params['subtitle'] = Taxonomy::t('admin', 'Create vocabulary');
$this->params['breadcrumbs'] = [
    [
        'label' => $this->title,
        'url' => ['index'],
    ],
    $this->params['subtitle']
];

?>
<div class="row">
    <div class="col-sm-12">
        <?= $this->render(
            '_form',
            [
                'model' => $model,
                'statusArray' => $statusArray
            ]
        ); ?>
    </div>
</div>
