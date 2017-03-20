<?php

/**
 * TaxonomyTerm model form view.
 *
 * @var \yii\base\View $this View
 * @var \yii\widgets\ActiveForm $form Form
 * @var array $statusArray Statuses array
 */

use artkost\yii2\taxonomy\models\TaxonomyTerm;
use artkost\yii2\taxonomy\Manager;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
?>
<?php $form = ActiveForm::begin(); ?>
    <div class="row">
        <div class="col-sm-12">
            <?= $form->field($model, 'name')
                ->textInput(['placeholder' => Manager::t('admin', 'Name')]) ?>
            <?= $form->field($model, 'description')
                ->textInput(['placeholder' => Manager::t('admin', 'Description')]) ?>
            <?= $form->field($model, 'parent_id')
                ->dropDownList(ArrayHelper::merge(['' => Manager::t('admin', 'None')], $model->vocabulary->termsTreeData)) ?>
        </div>
    </div>

<?= Html::submitButton(
    $model->isNewRecord ? Manager::t('admin', 'Create term') : Manager::t('admin', 'Update term'),
    [
        'class' => $model->isNewRecord ? 'btn btn-primary btn-large' : 'btn btn-success btn-large'
    ]
) ?>
<?php ActiveForm::end(); ?>
