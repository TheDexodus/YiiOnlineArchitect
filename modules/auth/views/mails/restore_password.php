<?php

use yii\helpers\Url;

/** @var string $email */
/** @var string $code */
?>
<p>To recover your password you need to go to the following link:
    <a href="<?=Url::base('http')?>/password/change?email=<?=$email?>&code=<?=$code?>">
        Restore my password
    </a>
</p>