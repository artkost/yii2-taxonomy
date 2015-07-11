<?php

/**
 * TaxonomyTerm model form view.
 *
 * @var \yii\base\View $this View
 * @var \yii\widgets\ActiveForm $form Form
 * @var array $statusArray Statuses array
 */

use artkost\taxonomy\models\TaxonomyTerm;
use artkost\taxonomy\Taxonomy;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
?>
<?php $form = ActiveForm::begin(); ?>
    <div class="row">
        <div class="col-sm-12">
            <?= $form->field($model, 'name')
                ->textInput(['placeholder' => Taxonomy::t('admin', 'Name')]) ?>
            <?= $form->field($model, 'description')
                ->textInput(['placeholder' => Taxonomy::t('admin', 'Description')]) ?>
            <?= $form->field($model, 'parent_id')
                ->dropDownList(ArrayHelper::merge(['' => Taxonomy::t('admin', 'None')], $model->vocabulary->termsTreeData)) ?>
        </div>
    </div>

<?= Html::submitButton(
    $model->isNewRecord ? Taxonomy::t('admin', 'Create term') : Taxonomy::t('admin', 'Update term'),
    [
        'class' => $model->isNewRecord ? 'btn btn-primary btn-large' : 'btn btn-success btn-large'
    ]
) ?>
<?php ActiveForm::end(); ?>
