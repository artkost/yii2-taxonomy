<?php

/**
 * @var \yii\base\View $this View
 * @var TaxonomyTerm $model
 * @var int $vid
 * @var TaxonomyTerm $parent
 */

use artkost\yii2\taxonomy\models\TaxonomyTerm;
use artkost\yii2\taxonomy\Manager;

$this->title = Manager::t('admin', 'Taxonomy');
$this->params['subtitle'] = Manager::t('admin', 'Update term');
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
                'model' => $model
            ]
        ); ?>
    </div>
</div>
