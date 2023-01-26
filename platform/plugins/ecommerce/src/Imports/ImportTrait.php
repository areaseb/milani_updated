<?php

namespace Botble\Ecommerce\Imports;

use Carbon\Carbon;
use Exception;
use Illuminate\Support\Collection;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use DateTime;

trait ImportTrait
{
    /**
     * @var int
     */
    protected int $totalImported = 0;

    /**
     * @var array
     */
    protected array $successes = [];

    /**
     * @return int
     */
    public function getTotalImported(): int
    {
        return $this->totalImported;
    }

    /**
     * @return ImportTrait
     */
    public function setTotalImported(): static
    {
        ++$this->totalImported;

        return $this;
    }

    /**
     * @param mixed $item
     */
    public function onSuccess(mixed $item): void
    {
        $this->successes[] = $item;
    }

    /**
     * @return Collection
     */
    public function successes(): Collection
    {
        return collect($this->successes);
    }

    /**
     * Transform a date value into a Carbon object.
     *
     * @param $value
     * @param string $format
     * @return string
     */
    public function transformDate($value, string $format = ''): string
    {
        $format = $format ?: config('core.base.general.date_format.date_time');

        try {
            return Carbon::instance(Date::excelToDateTimeObject($value))->format($format);
        } catch (Exception $exception) {
            return Carbon::createFromFormat($format, $value);
        }
    }

    /**
     * Transform a date value into a Carbon object.
     *
     * @param $value
     * @param string $format
     * @param null $default
     * @return string|null
     */
    public function getDate($value, string $format = 'Y-m-d H:i:s', $default = null): ?string
    {
        try {
            $date = DateTime::createFromFormat('!' . $format, $value);
            return $date ? $date->format(config('core.base.general.date_format.date_time')) : $value;
        } catch (Exception $exception) {
            return $default;
        }
    }
}
