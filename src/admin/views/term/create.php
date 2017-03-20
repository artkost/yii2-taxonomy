<?php

/**
 * TaxonomyTerm model create view.
 *
 * @var \yii\base\View $this View
 * @var array $statusArray Statuses array
 */

use artkost\yii2\taxonomy\Manager;

$this->title = Manager::t('admin', 'Taxonomy');
$this->params['subtitle'] = Manager::t('admin', 'Create a term');
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
