<?php

namespace StackTrace\Mann\Filterables;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use StackTrace\Mann\Filterable;
use function array_merge;
use function count;
use function is_array;
use function is_integer;
use function is_null;
use function is_numeric;

class Range extends Filterable
{
    protected ?string $component = 'range';

    /**
     * The minimum value of the range.
     */
    protected int|float|null $min = null;

    /**
     * The maximum value of the range.
     */
    protected int|float|null $max = null;

    /**
     * The step of value increments.
     */
    protected int|float $step = 1;

    /**
     * The name of query parameter for minimum value.
     */
    protected string $minAttribute = 'from';

    /**
     * The name of query parameter for maximum value.
     */
    protected string $maxAttribute = 'to';

    /**
     * Set the step value.
     */
    public function step(int|float $step): static
    {
        $this->step = $step;

        return $this;
    }

    /**
     * Retrieve the step value.
     */
    public function getStep(): float|int
    {
        return $this->step;
    }

    /**
     * Set the range limits.
     */
    public function limits(int|float $min, int|float $max): static
    {
        if ($max < $min) {
            throw new \RuntimeException("The max value should be greater than max value.");
        }

        $this->min = $min;
        $this->max = $max;

        return $this;
    }

    /**
     * Retrieve the maximum value of the range.
     */
    public function getMax(): float|int
    {
        if (is_null($this->max)) {
            throw new \RuntimeException("The max value is not set.");
        }

        return $this->max;
    }

    /**
     * Retrieve minimum value of the range.
     */
    public function getMin(): float|int
    {
        if (is_null($this->min)) {
            throw new \RuntimeException("The min value is not set.");
        }

        return $this->min;
    }

    public function getEmptyValue(): mixed
    {
        return [
            $this->minAttribute => $this->getMin(),
            $this->maxAttribute => $this->getMax(),
        ];
    }

    public function toArray()
    {
        return array_merge(parent::toArray(), [
            'min' => $this->getMin(),
            'max' => $this->getMax(),
            'step' => $this->getStep(),
            'minAttribute' => $this->minAttribute,
            'maxAttribute' => $this->maxAttribute,
        ]);
    }

    public function sanitizeValue(mixed $value): mixed
    {
        if ($this->isValidValue($value)) {
            if (is_integer($this->getStep())) {
                return [$this->minAttribute => (int) $value[$this->minAttribute], $this->maxAttribute => (int) $value[$this->maxAttribute]];
            } else {
                return [$this->minAttribute => (float) $value[$this->minAttribute], $this->maxAttribute => (float) $value[$this->maxAttribute]];
            }
        }

        return parent::sanitizeValue($value);
    }

    protected function isValidValue($value): bool
    {
        return is_array($value)
            && Arr::has($value, $this->minAttribute)
            && Arr::has($value, $this->maxAttribute)
            && is_numeric($value[$this->minAttribute])
            && is_numeric($value[$this->maxAttribute])
            && $value[$this->minAttribute] >= $this->getMin()
            && $value[$this->maxAttribute] <= $this->getMax();
    }

    public function applyOnEloquentBuilder(Builder $builder, mixed $value): void
    {
        if ($this->isValidValue($value)) {
            $builder
                ->where($this->getAttribute(), '>=', $value[$this->minAttribute])
                ->where($this->getAttribute(), '<=', $value[$this->maxAttribute]);
        } else {
            throw new \RuntimeException("The range value is not valid.");
        }
    }

    public function applyOnCollection(Collection $collection, mixed $value): Collection
    {
        if ($this->isValidValue($value)) {
            return $collection
                ->where($this->getAttribute(), '>=', $value[$this->minAttribute])
                ->where($this->getAttribute(), '<=', $value[$this->maxAttribute]);
        } else {
            throw new \RuntimeException("The range value is not valid.");
        }
    }
}
