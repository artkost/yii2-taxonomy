<?php

namespace app\modules\taxonomy\admin\controllers;

use app\modules\taxonomy\models\TaxonomyVocabulary;
use app\modules\taxonomy\models\TaxonomyVocabularySearch;
use app\modules\taxonomy\Module;
use Yii;
use yii\base\Controller;
use yii\web\HttpException;
use yii\web\Response;
use yii\widgets\ActiveForm;

/**
 * Taxonomy Vocabulary Controller.
 */
class VocabularyController extends Controller
{

    /**
     * list page.
     */
    public function actionIndex()
    {
        $searchModel = new TaxonomyVocabularySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->get());
        $statusArray = TaxonomyVocabulary::statusLabels();

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'statusArray' => $statusArray
        ]);
    }

    /**
     * Create model page.
     */
    public function actionCreate()
    {
        $model = new TaxonomyVocabulary(['scenario' => 'admin-create']);
        $statusArray = TaxonomyVocabulary::statusLabels();

        if ($model->load(Yii::$app->request->post())) {
            if ($model->validate()) {
                if ($model->save(false)) {
                    return $this->redirect(['update', 'id' => $model->id]);
                } else {
                    Yii::$app->session->setFlash('danger', Module::t('admin', 'Failed to create a vocabulary'));
                    return $this->refresh();
                }
            } elseif (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ActiveForm::validate($model);
            }
        }

        return $this->render('create', [
            'model' => $model,
            'statusArray' => $statusArray
        ]);
    }

    /**
     * Update model page.
     *
     * @param integer $id Post ID
     *
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model->setScenario('admin-update');
        $statusArray = TaxonomyVocabulary::statusLabels();

        if ($model->load(Yii::$app->request->post())) {
            if ($model->validate()) {
                if ($model->save(false)) {
                    return $this->refresh();
                } else {
                    Yii::$app->session->setFlash('danger',
                        Module::t('admin', 'Failed to update vocabulary {name}', ['{name}' => $model->name]));
                    return $this->refresh();
                }
            } elseif (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ActiveForm::validate($model);
            }
        }

        return $this->render('update', [
            'model' => $model,
            'statusArray' => $statusArray
        ]);
    }

    /**
     * Delete model page.
     *
     * @param integer $id Post ID
     *
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        return $this->redirect(['index']);
    }

    /**
     * Delete multiple models page.
     *
     * @return mixed
     * @throws \yii\web\HttpException
     */
    public function actionBatchDelete()
    {
        if (($ids = Yii::$app->request->post('ids')) !== null) {
            $models = $this->findModel($ids);
            foreach ($models as $model) {
                $model->delete();
            }
            return $this->redirect(['index']);
        } else {
            throw new HttpException(400);
        }
    }

    /**
     * Find model by ID.
     *
     * @param integer|array $id Model ID
     *
     * @return TaxonomyVocabulary
     *
     * @throws HttpException 404 error if model not found
     */
    protected function findModel($id)
    {
        if (is_array($id)) {
            $model = TaxonomyVocabulary::findAll($id);
        } else {
            $model = TaxonomyVocabulary::findOne($id);
        }
        if ($model !== null) {
            return $model;
        } else {
            throw new HttpException(404);
        }
    }
}
