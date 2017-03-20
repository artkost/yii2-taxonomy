<?php

/**
 * @var \yii\web\View $this View
 * @var \yii\data\ActiveDataProvider $dataProvider Data provider
 * @var TaxonomyVocabularySearch $searchModel Search model
 * @var array $statusArray Statuses array
 */

use artkost\yii2\taxonomy\models\TaxonomyVocabularySearch;
use artkost\yii2\taxonomy\Manager;
use yii\grid\ActionColumn;
use yii\grid\CheckboxColumn;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\jui\DatePicker;

$this->title = Manager::t('admin', 'Taxonomy');
$this->params['subtitle'] = Manager::t('admin', 'Vocabulary list');
$this->params['breadcrumbs'] = [
    $this->title
];
$gridId = 'vocabularies-grid';
$gridConfig = [
    'id' => $gridId,
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'columns' => [
        [
            'class' => CheckboxColumn::classname()
        ],
        'id',
        [
            'attribute' => 'title',
            'format' => 'html',
            'value' => function ($model) {
                return Html::a($model->title, ['/taxonomy/term/index', 'vid' => $model->id]);
            }
        ],
        'name',
        [
            'attribute' => 'status_id',
            'format' => 'html',
            'value' => function ($model) {
                $class = ($model->status_id === $model::STATUS_ENABLED) ? 'label-success' : 'label-danger';

                return '<span class="label ' . $class . '">' . $model->status . '</span>';
            },
            'filter' => Html::activeDropDownList(
                $searchModel,
                'status_id',
                $statusArray,
                [
                    'class' => 'form-control',
                    'prompt' => Manager::t('admin', 'Select status')
                ]
            )
        ],
        [
            'attribute' => 'created_at',
            'format' => 'date',
            'filter' => DatePicker::widget(
                [
                    'model' => $searchModel,
                    'attribute' => 'created_at',
                    'options' => [
                        'class' => 'form-control'
                    ],
                    'clientOptions' => [
                        'dateFormat' => 'dd.mm.yy',
                    ]
                ]
            )
        ],
        [
            'attribute' => 'updated_at',
            'format' => 'date',
            'filter' => DatePicker::widget(
                [
                    'model' => $searchModel,
                    'attribute' => 'updated_at',
                    'options' => [
                        'class' => 'form-control'
                    ],
                    'clientOptions' => [
                        'dateFormat' => 'dd.mm.yy',
                    ]
                ]
            )
        ]
    ]
];
$user = Yii::$app->user;

$actions = [];
$showActions = false;

if ($user->can('taxonomyVocabularyCreate')) {
	$actions[] = '{create}';
}
if ($user->can('taxonomyVocabularyUpdate')) {
	$actions[] = '{update}';
	$showActions = $showActions || true;
}
if ($user->can('taxonomyVocabularyDelete')) {
	$actions[] = '{delete}';
	$showActions = $showActions || true;
}
if ($showActions === true) {
	$gridConfig['columns'][] = [
	    'class' => ActionColumn::className(),
	    'template' => implode(' ', $actions)
	];
}

?>

<div class="row">
    <div class="col-xs-12">
        <?= GridView::widget($gridConfig); ?>
    </div>
</div>
