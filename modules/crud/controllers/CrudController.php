<?php

declare(strict_types=1);

namespace crud\controllers;

use crud\components\helpers\NameHelper;
use crud\components\Model;
use crud\models\forms\ModelForm;
use Throwable;
use Yii;
use yii\data\ActiveDataProvider;
use yii\db\ActiveRecord;
use yii\db\StaleObjectException;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * Class CrudController
 */
class CrudController extends Controller
{
    /**
     * @return string
     * @throws ForbiddenHttpException
     */
    public function actionModelList(): string
    {
        if (!Yii::$app->user->can('ROLE_VIEW_ALL_MODELS')) {
            throw new ForbiddenHttpException('You do not have access to this page.');
        }

        $canModels = [];

        /** @var Model $model */
        foreach (Yii::$app->crud->getModelsForAction('view') as $model) {
            if ($this->checkAccess('view', $model->name) === null) {
                $canModels[$model->name] = $model;
            }
        }

        return $this->render(
            'model_list',
            [
                'models' => $canModels,
            ]
        );
    }

    /**
     * @param string $name
     *
     * @return string|Response
     *
     * @throws NotFoundHttpException
     * @throws ForbiddenHttpException
     */
    public function actionIndex(string $name)
    {
        if (($result = $this->checkAccess('view', $name)) !== null) {
            return $result;
        }

        $model = $this->getModel($name, Model::ACTION_VIEW);

        $dataProvider = new ActiveDataProvider(
            [
                'query' => $model->getFillData(),
            ]
        );

        return $this->render(
            'index',
            [
                'dataProvider' => $dataProvider,
                'model'        => $model,
            ]
        );
    }

    /**
     * @param string $name
     * @param int    $id
     *
     * @return Response
     *
     * @throws NotFoundHttpException
     * @throws Throwable
     * @throws StaleObjectException
     */
    public function actionDelete(string $name, int $id): Response
    {
        if (($result = $this->checkAccess('delete', $name)) !== null) {
            return $result;
        }

        $model = $this->getModel($name, Model::ACTION_DELETE);

        /** @var ActiveRecord $fillModel */
        if (!($fillModel = $model->getOneFillData(['id' => $id])) instanceof $model->class) {
            throw new NotFoundHttpException(sprintf('%s with id %s not found', $model->title, $id));
        }

        $fillModel->delete();

        return $this->redirect('/admin/crud/'.$name.'/index');
    }

    /**
     * @param string $name
     * @param int    $id
     *
     * @return Response|string
     *
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     */
    public function actionView(string $name, int $id)
    {
        if (($result = $this->checkAccess('view', $name)) !== null) {
            return $result;
        }

        $model = $this->getModel($name, Model::ACTION_VIEW);

        /** @var ActiveRecord $fillModel */
        if (!($fillModel = $model->getOneFillData(['id' => $id])) instanceof $model->class) {
            throw new NotFoundHttpException(sprintf('%s with id %s not found', $model->title, $id));
        }

        return $this->render(
            'view',
            [
                'model'     => $model,
                'fillModel' => $fillModel,
            ]
        );
    }

    /**
     * @param string $name
     *
     * @return string|Response
     *
     * @throws NotFoundHttpException
     * @throws ForbiddenHttpException
     */
    public function actionCreate(string $name)
    {
        if (($result = $this->checkAccess('create', $name)) !== null) {
            return $result;
        }

        $model = $this->getModel($name, Model::ACTION_CREATE);

        $form = new ModelForm($model, ['scenario' => ModelForm::SCENARIO_CREATE]);

        if ($form->load(Yii::$app->request->post()) && ($record = $form->createRecord()) instanceof ActiveRecord) {
            return $this->redirect(['/admin/crud/'.$name.'/view', 'id' => $record->getPrimaryKey()]);
        }

        return $this->render(
            'create',
            [
                'form'  => $form,
                'model' => $model,
            ]
        );
    }


    /**
     * @param string $name
     *
     * @param int    $id
     *
     * @return string|Response
     *
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     */
    public function actionUpdate(string $name, int $id)
    {
        if (($result = $this->checkAccess('update', $name)) !== null) {
            return $result;
        }

        $model = $this->getModel($name, Model::ACTION_UPDATE);

        /** @var ActiveRecord $fillModel */
        if (!($fillModel = $model->getOneFillData(['id' => $id])) instanceof $model->class) {
            throw new NotFoundHttpException(sprintf('%s with id %s not found', $model->title, $id));
        }

        $form = new ModelForm($model, ['scenario' => ModelForm::SCENARIO_UPDATE], $fillModel);
        $form->fill($fillModel);

        if ($form->load(Yii::$app->request->post()) && $form->updateRecord($fillModel)) {
            return $this->redirect(['/admin/crud/'.$name.'/view', 'id' => $id]);
        }

        return $this->render(
            'update',
            [
                'form'  => $form,
                'model' => $model,
                'fillModel' => $fillModel,
            ]
        );
    }

    /**
     * @param string $modelName
     * @param string $action
     *
     * @return Model
     *
     * @throws NotFoundHttpException
     */
    private function getModel(string $modelName, string $action): Model
    {
        /** @var Model|null $model */
        if (($model = Yii::$app->crud->getModel($modelName)) === null) {
            throw new NotFoundHttpException('Model does not exists');
        }

        if (!$model->canAction($action)) {
            throw new NotFoundHttpException(sprintf('Model no have action "%s"', $action));
        }

        return $model;
    }

    /**
     * @param string $action
     * @param string $modelName
     *
     * @return Response|null
     *
     * @throws ForbiddenHttpException
     */
    private function checkAccess(string $action, string $modelName): ?Response
    {
        if (Yii::$app->user->isGuest) {
            return $this->redirect('/login');
        }

        if (!Yii::$app->authManager->checkAccess(
            Yii::$app->user->id,
            'ROLE_'.NameHelper::getRoleName($modelName). '_' . strtoupper($action)
        )) {
            throw new ForbiddenHttpException('You do not have access to this page.');
        }

        return null;
    }
}
