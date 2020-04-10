<?php
namespace app\controllers;

use http\Exception;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use yii\rest\ActiveController;
use yii\web\BadRequestHttpException;

class BookController extends ActiveController
{
    public $modelClass = 'app\models\Book';

    public $reservedParams = ['sort','q'];

    public function actions() {
        $actions = parent::actions();
        // 'prepareDataProvider' is the only function that need to be overridden here
        $actions['index']['prepareDataProvider'] = [$this, 'indexDataProvider'];
        return $actions;
    }

    public function indexDataProvider() {
        $params = \Yii::$app->request->queryParams;

        $model = new $this->modelClass;
        // I'm using yii\base\Model::getAttributes() here
        // In a real app I'd rather properly assign
        // $model->scenario then use $model->safeAttributes() instead
        $modelAttr = $model->attributes;

        // this will hold filtering attrs pairs ( 'name' => 'value' )
        $search = [];

        if (!empty($params)) {
            foreach ($params as $key => $value) {
                // In case if you don't want to allow wired requests
                // holding 'objects', 'arrays' or 'resources'
                if(!is_scalar($key) or !is_scalar($value)) {
                    throw new BadRequestHttpException('Bad Request');
                }
                // if the attr name is not a reserved Keyword like 'q' or 'sort' and
                // is matching one of models attributes then we need it to filter results
                if (!in_array(strtolower($key), $this->reservedParams)
                    && ArrayHelper::keyExists($key, $modelAttr, false)) {
                    $search[$key] = $value;
                }
            }
        }

        // you may implement and return your 'ActiveDataProvider' instance here.
        // in my case I prefer using the built in Search Class generated by Gii which is already
        // performing validation and using 'like' whenever the attr is expecting a 'string' value.
        $searchByAttr['BookSearch'] = $search;
        $searchModel = new \app\models\BookSearch();
        return $searchModel->search($searchByAttr);
    }
}