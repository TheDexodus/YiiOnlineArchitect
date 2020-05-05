<?php

/* @var $this \yii\web\View */

/* @var $content string */

use app\widgets\Alert;
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?=Yii::$app->language?>">
<head>
    <meta charset="<?=Yii::$app->charset?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php $this->registerCsrfMetaTags() ?>
    <title><?=Html::encode($this->title)?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

<div class="wrap">
    <?php
    NavBar::begin(
        [
            'brandLabel' => Yii::$app->name,
            'brandUrl'   => Yii::$app->homeUrl,
            'options'    => [
                'class' => 'navbar-inverse navbar-fixed-top',
            ],
        ]
    );
    $navItems = [
        ['label' => 'Home', 'url' => ['/']],
        ['label' => 'Wizard', 'url' => ['/wizard']],
    ];

    if (Yii::$app->user->isGuest) {
        $navItems[] = ['label' => 'Login', 'url' => ['/login']];
        $navItems[] = ['label' => 'Sign up', 'url' => ['/register']];
    } else {
        if (Yii::$app->user->can('ROLE_VIEW_ALL_MODELS')) {
            $navItems[] = ['label' => 'CRUD', 'url' => ['/admin/crud']];
        }
        if (Yii::$app->user->can('ROLE_MATERIAL_CREATE') &&
            Yii::$app->user->can('ROLE_MATERIAL_TYPE_CREATE')
        ) {
            $navItems[] = ['label' => 'Material Import', 'url' => ['/admin/importer']];
        }
        $navItems[] = ['label' => 'Profile', 'url' => ['/profile']];
        $navItems[] = (
            '<li>'
            .Html::beginForm(['/logout'], 'post')
            .Html::submitButton(
                'Logout ('.Yii::$app->user->identity->username.')',
                ['class' => 'btn btn-link logout']
            )
            .Html::endForm()
            .'</li>'
        );
    }
    echo Nav::widget(
        [
            'options' => ['class' => 'navbar-nav navbar-right'],
            'items'   => $navItems,
        ]
    );
    NavBar::end();
    ?>

    <div class="container">
        <?=Breadcrumbs::widget(
            [
                'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
            ]
        )?>
        <?=Alert::widget()?>
        <?=$content?>
    </div>
</div>

<footer class="footer">
    <div class="container">
        <p class="pull-left">&copy; My Company <?=date('Y')?></p>

        <p class="pull-right"><?=Yii::powered()?></p>
    </div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
