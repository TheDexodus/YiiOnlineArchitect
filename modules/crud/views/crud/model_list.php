<?php

use crud\components\Model;

/** @var Model[] $models */

$this->title = 'CRUD Index';
$this->params['breadcrumbs'][] = 'CRUD';

?>

<h1>Model Menu</h1>
<ul>
    <?php foreach ($models as $modelName => $model): ?>
        <li><a href="/admin/crud/<?=$modelName?>/index"><?=$model->title?></a></li>
    <?php endforeach; ?>
</ul>