<?php

declare(strict_types=1);

namespace crud\components\columns;

use yii\jui\DatePicker;

/**
 * Class DateColumn
 */
class DateColumn extends AbstractColumn
{
    /** @var string */
    protected $pattern = '[0-9]{2}.[0-9]{2}.[0-9]{4}';

    /**
     * @return string|null
     */
    public function getMinDate(): ?string
    {
        return $this->options['min_date'] ?? null;
    }

    /**
     * @return string|null
     */
    public function getMaxDate(): ?string
    {
        return $this->options['max_date'] ?? null;
    }

    /**
     * @inheritDoc
     */
    public function setWidget(?string $widget): void
    {
        $this->widget = $widget ?? DatePicker::class;
    }

    /**
     * @inheritDoc
     */
    public function validate($value): ?string
    {
        $result = parent::validate($value);

        if ($result === null) {
            $timestamp = strtotime($value);

            if ($this->getMinDate() !== null && $timestamp < strtotime($this->getMinDate())) {
                return sprintf('Date less than minimal date(%s)', $this->getMinDate());
            }

            if ($this->getMaxDate() !== null && $timestamp > strtotime($this->getMaxDate())) {
                return sprintf('Date greater than maximal date(%s)', $this->getMaxDate());
            }

            return null;
        }

        return $result;
    }
}
