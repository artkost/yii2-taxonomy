<?php

/**
 * @var \yii\base\View $this View
 * @var TaxonomyTerm $model
 * @var int $vid
 * @var TaxonomyTerm $parent
 */

use artkost\taxonomy\models\TaxonomyTerm;
use artkost\taxonomy\Module;

$this->title = Module::t('admin', 'Taxonomy');
$this->params['subtitle'] = Module::t('admin', 'Update term');
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
