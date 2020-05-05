<?php

use crud\components\Model;
use crud\models\forms\ModelForm;
use yii\bootstrap\Html;
use yii\db\ActiveRecord;

/** @var ModelForm $form */
/** @var Model $model */
/** @var ActiveRecord $fillModel */

$this->title = sprintf('Update %s', $model->title);

if (Yii::$app->user->can('ROLE_VIEW_ALL_MODELS')) {
    $this->params['breadcrumbs'][] = ['label' => 'CRUD', 'url' => ['/admin/crud/']];
} else {
    $this->params['breadcrumbs'][] = ['label' => 'CRUD'];
}
if (Yii::$app->user->can('ROLE_'.strtoupper($model->name).'_VIEW')) {
    $this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['/admin/crud/'.$model->name.'/index']];
} else {
    $this->params['breadcrumbs'][] = ['label' => $model->title];
}
$this->params['breadcrumbs'][] = $fillModel->getPrimaryKey();
?>

<div class="model-update">
    <h1><?=Html::encode($this->title)?></h1>

    <?=$this->render(
        '_form',
        [
            'form'   => $form,
            'model'  => $model,
            'method' => 'Update',
        ]
    )?>
</div>