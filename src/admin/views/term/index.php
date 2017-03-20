<?php

/**
 * @var \yii\base\View $this View
 * @var \yii\data\ActiveDataProvider $dataProvider Data provider
 * @var Manager $searchModel Search model
 * @var int $vid Statuses array
 */

use artkost\yii2\taxonomy\models\TaxonomyTermSearch;
use artkost\yii2\taxonomy\Manager;
use yii\grid\ActionColumn;
use yii\grid\CheckboxColumn;
use yii\helpers\Url;

$this->title = Manager::t('admin', 'Taxonomy');
$this->params['subtitle'] = Manager::t('admin', 'Terms');
$this->params['breadcrumbs'] = [
    $this->title
];
$gridId = 'taxonomy-terms-grid';
$gridConfig = [
    'id' => $gridId,
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'columns' => [
        [
            'class' => CheckboxColumn::classname()
        ],
        'name',
        'description'
    ]
];
$user = Yii::$app->user;
$actions = [];
$showActions = false;

if ($user->can('taxonomyTermCreate')) {
    $actions[] = '{create}';
}

if ($user->can('taxonomyTermUpdate')) {
    $actions[] = '{update}';
    $showActions = $showActions || true;
}

if ($user->can('taxonomyTermDelete')) {
    $actions[] = '{delete}';
    $showActions = $showActions || true;
}

if ($showActions === true) {
    $gridConfig['columns'][] = [
        'class' => ActionColumn::className(),
        'template' => implode(' ', $actions),
        'urlCreator' => function($action, $model, $key, $index) use($vid) {
            $params = is_array($key) ? $key : ['id' => (string) $key, 'vid' => $vid];
            $params[0] = $action;

            return Url::toRoute($params);
        }
    ];
}
?>

<div class="row">
    <div class="col-xs-12">
        <?= GridView::widget($gridConfig); ?>
    </div>
</div>
