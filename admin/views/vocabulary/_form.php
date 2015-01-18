<?php

/**
 * TaxonomyVocabulary model form view.
 *
 * @var \yii\base\View $this View
 * @var \yii\widgets\ActiveForm $form Form
 * @var TaxonomyVocabulary $model Model
 * @var \vova07\themes\admin\widgets\Box $box Box widget instance
 * @var array $statusArray Statuses array
 */

use app\modules\taxonomy\Module;
use app\modules\taxonomy\TaxonomyVocabulary;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

?>
<?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-sm-12">
            <?= $form->field($model, 'title')
                ->textInput(['placeholder' => Module::t('admin', 'Title')]) ?>
            <?= $form->field($model, 'name')
                ->textInput(['placeholder' => Module::t('admin', 'Name'), 'disabled' => !$model->isNewRecord]) ?>
        </div>
    </div>

<?= Html::submitButton(
    $model->isNewRecord ? Module::t('admin', 'Create vocabulary') : Module::t('admin', 'Update vocabulary'),
    [
        'class' => $model->isNewRecord ? 'btn btn-primary btn-large' : 'btn btn-success btn-large'
    ]
) ?>

<?php ActiveForm::end(); ?>