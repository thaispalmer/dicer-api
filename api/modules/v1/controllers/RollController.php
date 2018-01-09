<?php

namespace api\modules\v1\controllers;

use yii\rest\ActiveController;

/**
 * Roll Controller API
 */
class RollController extends ActiveController
{
    public $modelClass = 'api\modules\v1\models\Roll';

    /**
     * @inheritdoc
     */
    public function actions()
    {
        $actions = parent::actions();
        unset($actions['update']);
        unset($actions['delete']);
        return $actions;
    }
}