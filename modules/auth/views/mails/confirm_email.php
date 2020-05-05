<?php

use yii\helpers\Url;

/** @var string $email */
/** @var string $code */
?>
<p>Confirm your email on site:
    <a href="<?=Url::base('http')?>/confirm/email?email=<?=$email?>&code=<?=$code?>">
        Confirm my email
    </a>
</p>