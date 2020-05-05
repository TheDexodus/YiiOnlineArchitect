<?php

namespace crud\components\columns;

class PasswordColumn extends TextColumn
{
    /**
     * @inheritDoc
     */
    public function getFieldAction(): string
    {
        return 'passwordInput';
    }
}