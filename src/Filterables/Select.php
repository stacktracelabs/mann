<?php

namespace StackTrace\Mann\Filterables;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use StackTrace\Mann\Filterable;
use StackTrace\Mann\Option;
use function array_merge;
use function collect;
use function count;
use function is_array;
use function is_string;

class Select extends Filterable
{
    /**
     * Default select component.
     */
    protected ?string $component = 'select';

    /**
     * List of filter options.
     */
    protected array $options = [];

    /**
     * Determine if multiple options can be select.
     */
    protected bool $multiple = false;

    /**
     * The title of option when nothing is selected.
     */
    protected ?string $emptyOptionTitle = 'Please choose a value';

    /**
     * Add option to the select.
     */
    public function option(Option $option): static
    {
        $this->options[] = $option;

        return $this;
    }

    /**
     * Enables or disables multiple options in filterable.
     */
    public function multiple(bool $multiple = true): static
    {
        $this->multiple = $multiple;

        return $this;
    }

    /**
     * Allows multiple options to be selected with the filterable.
     */
    public function allowMultipleSelections(): static
    {
        return $this->multiple();
    }

    /**
     * Applies filter on the Collection.
     */
    public function applyOnCollection(Collection $collection, mixed $value): Collection
    {
        $value = $this->findOptionsForValue($value);

        if ($value instanceof Option) {
            return $collection->where($this->getAttribute(), $value->value());
        } else if (is_array($value) && count($value) > 0) {
            return $collection->whereIn($this->getAttribute(), collect($value)->map(fn (Option $option) => $option->value())->all());
        }

        return $collection;
    }

    /**
     * Applies filterable on eloquent builder.
     */
    public function applyOnEloquentBuilder(Builder $builder, mixed $value): void
    {
        $value = $this->findOptionsForValue($value);

        if ($value instanceof Option) {
            $builder->where($this->getAttribute(), $value->value());
        } else if (is_array($value) && count($value) > 0) {
            $builder->whereIn($this->getAttribute(), collect($value)->map(fn (Option $option) => $option->value())->all());
        }
    }

    /**
     * Retrieve option by its identifier.
     */
    protected function findOptionById(string $id): ?Option
    {
        return collect($this->options)->first(fn (Option $option) => $option->getId() === $id);
    }

    /**
     * Retrieve all options for given filterable value.
     */
    protected function findOptionsForValue(mixed $value): Option|array|null
    {
        // If multiple values are allowed, we'll return array of options.
        if ($this->multiple) {
            return collect(Arr::wrap($value))
                ->map(fn ($option) => is_string($option) ? $this->findOptionById($option) : null)
                ->filter()
                ->all();
        }

        return is_string($value) ? $this->findOptionById($value) : null;
    }

    /**
     * Set the title of option for empty value when filterable
     * is displayed as regular select control.
     */
    public function emptyOptionTitle(?string $title): static
    {
        $this->emptyOptionTitle = $title;

        return $this;
    }

    /**
     * Retrieve title of the option for empty value when filterable
     * is displayed as regular select control.
     */
    public function getEmptyOptionTitle(): string
    {
        return $this->emptyOptionTitle ?: throw new \RuntimeException("The empty option title is not set.");
    }

    public function getEmptyValue(): mixed
    {
        if ($this->multiple) {
            return [];
        }

        return null;
    }

    public function toArray()
    {
        return array_merge(parent::toArray(), [
            'options' => $this->options,
            'multiple' => $this->multiple,
            'emptyOptionTitle' => $this->getEmptyOptionTitle(),
        ]);
    }
}
