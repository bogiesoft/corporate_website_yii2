<?php

namespace backend\modules\catalog\controllers;

use common\modules\catalog\models\MaterialBackingTrack;
use Yii;
use common\modules\catalog\models\Material;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;


class BackingtrackController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    public function actionCreate()
    {
        $model = new MaterialBackingTrack();

        if (Yii::$app->request->isPost) {
            $model->load(Yii::$app->request->post());
            $model->audio_file = UploadedFile::getInstance($model, 'audio_file');

            if ($model->save()) {
                return $this->redirect('@web/catalog/material/update' . '?id=' . $model->getAttribute('material_id') . '#tableMarkbackingtrack');
            }
        }

        $model->setAttribute('material_id', Yii::$app->request->getQueryParam('material'));

        return $this->render('audioForm', [
                'model' => $model,
            ]);
    }

    public function actionUpdate($id)
    {
        $model = MaterialBackingTrack::find()->where(['id' => $id])->one();

        if (Yii::$app->request->isPost) {
            $model->load(Yii::$app->request->post());
            $model->audio_file = UploadedFile::getInstance($model, 'audio_file');

            if ($model->save()) {
                return $this->redirect('@web/catalog/material/update' . '?id=' . $model->getAttribute('material_id') . '#tableMarkbackingtrack', 302);
            }
        }

        return $this->render('audioForm', [
                'model' => $model,
            ]);
    }

    public function actionDelete($id)
    {
        $audio = MaterialBackingTrack::findOne($id);
        $material_id = $audio->getAttribute('material_id');
        $audio->delete();

        return $this->redirect("@web/catalog/material/update?id=$material_id#tableMarkbackingtrack");
    }

    /**
     * Finds the Material model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Material the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Material::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('Такого материала не существует.');
        }
    }

    public function actionDeletefile($id, $fileId = null, $fieldName = null)
    {
        Yii::$app->response->format = 'json';

        $material = MaterialBackingTrack::findOne($id);

        if ($fileId) {
            return $material->deleteFile($fieldName, (int)$fileId);
        } else {
            return $material->deleteFile($fieldName);
        }
    }
}
