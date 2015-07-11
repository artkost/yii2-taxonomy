<?php

/**
 * TaxonomyTerm model create view.
 *
 * @var \yii\base\View $this View
 * @var array $statusArray Statuses array
 */

use artkost\taxonomy\Taxonomy;

$this->title = Taxonomy::t('admin', 'Taxonomy');
$this->params['subtitle'] = Taxonomy::t('admin', 'Create a term');
$this->params['breadcrumbs'] = [
    [
        'label' => $this->title,
        'url' => ['index'],
    ],
    $this->params['subtitle']
]; ?>
<div class="row">
    <div class="col-sm-12">
        <?= $this->render(
            '_form',
            [
                'model' => $model
            ]
        ); ?>
    </div>
</div>
