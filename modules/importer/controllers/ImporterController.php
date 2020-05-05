<?php

declare(strict_types=1);

namespace importer\controllers;

use importer\models\forms\ImportForm;
use Yii;
use yii\data\ArrayDataProvider;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\Response;
use yii\web\UploadedFile;

/**
 * Class ImporterController
 */
class ImporterController extends Controller
{
    /**
     * @return Response|string
     *
     * @throws ForbiddenHttpException
     * @throws \Throwable
     */
    public function actionIndex()
    {
        $this->checkAccess();

        $form = new ImportForm();

        if (Yii::$app->request->isPost) {
            $form->file = UploadedFile::getInstance($form, 'file');
            if (($models = $form->importModels()) !== null) {
                $materialProvider = new ArrayDataProvider(
                    [
                        'allModels' => $models['materials'],
                    ]
                );
                $materialTypeProvider = new ArrayDataProvider(
                    [
                        'allModels' => $models['material_types'],
                    ]
                );

                return $this->render(
                    'congratulation',
                    [
                        'materialProvider' => $materialProvider,
                        'materialTypeProvider' => $materialTypeProvider,
                    ]
                );
            }
        }

        return $this->render(
            'index',
            [
                'form' => $form,
            ]
        );
    }

    /**
     * @throws ForbiddenHttpException
     */
    private function checkAccess(): void
    {
        if (Yii::$app->user->isGuest ||
            !Yii::$app->user->can('ROLE_MATERIAL_CREATE') ||
            !Yii::$app->user->can('ROLE_MATERIAL_TYPE_CREATE')
        ) {
            throw new ForbiddenHttpException();
        }
    }
}
