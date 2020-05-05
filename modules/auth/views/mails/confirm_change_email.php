<?php

use yii\helpers\Url;

/** @var string $email */
/** @var string $code */
?>
<p>Confirm a change your email on site:
    <a href="<?=Url::base('http')?>/profile/confirm/email?email=<?=$email?>&code=<?=$code?>">
        Confirm my email
    </a>
</p>