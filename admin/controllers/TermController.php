<?php

namespace artkost\taxonomy\admin\controllers;

use artkost\taxonomy\models\TaxonomyTerm;
use artkost\taxonomy\models\TaxonomyTermSearch;
use artkost\taxonomy\Module;
use Yii;
use yii\web\Controller;
use yii\web\HttpException;
use yii\web\Response;
use yii\widgets\ActiveForm;

/**
 * Taxonomy Term Controller.
 */
class TermController extends Controller
{

    /**
     * @param $vid
     * @return string
     */
    public function actionIndex($vid)
    {
        $searchModel = new TaxonomyTermSearch(['vid' => $vid]);
        $dataProvider = $searchModel->search(Yii::$app->request->get());

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'vid' => $vid
        ]);
    }

    /**
     * @param $vid
     * @return array|string|Response
     */
    public function actionCreate($vid)
    {
        $model = new TaxonomyTerm(['scenario' => 'admin-create', 'vid' => $vid]);

        if ($model->load(Yii::$app->request->post())) {
            if ($model->validate()) {
                if ($model->save(false)) {
                    return $this->redirect(['update', 'id' => $model->id, 'vid' => $vid]);
                } else {
                    Yii::$app->session->setFlash('danger',
                        Module::t('admin', 'Failed to create a term'));
                    return $this->refresh();
                }
            } elseif (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ActiveForm::validate($model);
            }
        }

        return $this->render('create', [
            'model' => $model,
            'vid' => $vid
        ]);
    }

    /**
     * Update model page.
     *
     * @param integer $vid
     * @param integer $id
     *
     * @return mixed
     */
    public function actionUpdate($vid, $id)
    {
        $model = $this->findModel($id, $vid);
        $model->setScenario('admin-update');

        if ($model->load(Yii::$app->request->post())) {
            if ($model->validate()) {
                if ($model->save(false)) {
                    return $this->refresh();
                } else {
                    Yii::$app->session->setFlash('danger',
                        Module::t('admin', 'Failed to update term {name}', ['{name}' => $model->name]));
                    return $this->refresh();
                }
            } elseif (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ActiveForm::validate($model);
            }
        }

        return $this->render('update', [
            'model' => $model,
            'vid' => $vid,
            'parent' => $model->getParent()
        ]);
    }

    /**
     * Delete model page.
     *
     * @param integer $id Post ID
     *
     * @return mixed
     */
    public function actionDelete($vid, $id)
    {
        $this->findModel($id, $vid)->delete();
        return $this->redirect(['index', 'vid' => $vid]);
    }

    /**
     * Delete multiple models page.
     *
     * @return mixed
     * @throws \yii\web\HttpException
     */
    public function actionBatchDelete($vid)
    {
        if (($ids = Yii::$app->request->post('ids')) !== null) {
            $models = $this->findModel($ids, $vid);
            foreach ($models as $model) {
                $model->delete();
            }
            return $this->redirect(['index', 'vid' => $vid]);
        } else {
            throw new HttpException(400);
        }
    }

    /**
     * Find model by ID.
     *
     * @param integer|array $id Model ID
     *
     * @return TaxonomyTerm
     *
     * @throws HttpException 404 error if model not found
     */
    protected function findModel($id, $vid)
    {
        if (is_array($id)) {
            $model = TaxonomyTerm::find()->where(['vid' => $vid, 'id' => $id])->all();
        } else {
            $model = TaxonomyTerm::find()->where(['vid' => $vid, 'id' => $id])->one();
        }
        if ($model !== null) {
            return $model;
        } else {
            throw new HttpException(404);
        }
    }
}
