<?php

use auth\models\User;

/** @var User $user */

$this->title = 'Profile';

?>

<h1>Profile</h1>

<p>Email <input type="text" disabled value="<?=$user->email?>"> <a href="/profile/change/email">Change</a></p>
<p>Phone <input type="text" disabled value="<?=$user->phone?>"> <a href="/profile/change/phone">Change</a></p>
<p>Password <a href="/profile/change/password">Change</a></p>