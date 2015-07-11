<?php


use yii\base\InvalidConfigException;
use yii\db\ActiveRecord;

class Action extends \yii\base\Action
{
    /**
     * @var string class name of the question model which will be handled by this action.
     * The model class must implement [[QuestionInterface]] or [[AnswerInterface]].
     * This property must be set.
     */
    public $modelClass;

    /**
     * @var callable a PHP callable that will be called to return the model corresponding
     * to the specified primary key value. If not set, [[findModel()]] will be used instead.
     * The signature of the callable should be:
     *
     * ```php
     * function ($class, $id, $action) {
     *     // $id is the primary key value. If composite primary key, the key values
     *     // will be separated by comma.
     *     // $action is the action object currently running
     * }
     * ```
     *
     * The callable should return the model found, or throw an exception if not found.
     */
    public $findModel;

    /**
     * @var callable a PHP callable that will be called when running an action to determine
     * if the current user has the permission to execute the action. If not set, the access
     * check will not be performed. The signature of the callable should be as follows,
     *
     * ```php
     * function ($action, $model = null) {
     *     // $model is the requested model instance.
     *     // If null, it means no specific model (e.g. IndexAction)
     * }
     * ```
     */
    public $checkAccess;

    /**
     * @var string name of the view file
     */
    public $viewFile;

    /**
     * @inheritdoc
     */
    public function init()
    {
        if ($this->modelClass === null) {
            throw new InvalidConfigException(get_class($this) . '::$modelClass must be set.');
        }
    }

    /**
     * @param string $modelClass
     * @param null $id
     * @return ActiveRecord
     */
    protected function findModel($modelClass, $id = null)
    {
        if ($this->findModel !== null) {
            return call_user_func($this->findModel, $modelClass, $id, $this);
        }

        $model = $modelClass::find()->where(['id' => $id])->one();

        return ($model !== null) ? $model : $this->notFoundException();
    }
}
