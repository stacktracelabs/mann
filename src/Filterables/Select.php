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
     *
     * @var string|null
     */
    protected ?string $component = 'select';

    /**
     * List of filter options.
     *
     * @var array
     */
    protected array $options = [];

    /**
     * Determine if multiple options can be select.
     *
     * @var bool
     */
    protected bool $multiple = false;

    /**
     * The title of option when nothing is selected.
     *
     * @var string|null
     */
    protected ?string $emptyTitle = 'Please choose a value';

    /**
     * Add option to the select.
     *
     * @param \StackTrace\Mann\Option $option
     * @return $this
     */
    public function addOption(Option $option): static
    {
        $this->options[] = $option;

        return $this;
    }

    /**
     * Enables or disables multiple options in filterable.
     *
     * @param bool $multiple
     * @return $this
     */
    public function multiple(bool $multiple = true): static
    {
        $this->multiple = $multiple;

        return $this;
    }

    /**
     * Allows multiple options to be selected with the filterable.
     *
     * @return $this
     */
    public function allowMultipleSelections(): static
    {
        return $this->multiple();
    }

    /**
     * Applies filter on the Collection.
     *
     * @param \Illuminate\Support\Collection $collection
     * @param mixed $value
     * @return \Illuminate\Support\Collection
     */
    public function applyOnCollection(Collection $collection, mixed $value): Collection
    {
        $value = $this->getOptionsForValue($value);

        if ($value instanceof Option) {
            return $collection->where($this->attribute(), $value->value());
        } else if (is_array($value) && count($value) > 0) {
            return $collection->whereIn($this->attribute(), collect($value)->map(fn (Option $option) => $option->value())->all());
        }

        return $collection;
    }

    /**
     * Applies filterable on eloquent builder.
     *
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @param mixed $value
     * @return void
     */
    public function applyOnEloquentBuilder(Builder $builder, mixed $value): void
    {
        $value = $this->getOptionsForValue($value);

        if ($value instanceof Option) {
            $builder->where($this->attribute(), $value->value());
        } else if (is_array($value) && count($value) > 0) {
            $builder->whereIn($this->attribute(), collect($value)->map(fn (Option $option) => $option->value())->all());
        }
    }

    /**
     * Retrieve option by its identifier.
     *
     * @param string $id
     * @return \StackTrace\Mann\Option|null
     */
    protected function getOptionById(string $id): ?Option
    {
        return collect($this->options)->first(fn (Option $option) => $option->getId() === $id);
    }

    protected function getOptionsForValue(mixed $value): Option|array|null
    {
        // If multiple values are allowed, we'll return array of options.
        if ($this->multiple) {
            return collect(Arr::wrap($value))
                ->map(fn ($option) => is_string($option) ? $this->getOptionById($option) : null)
                ->filter()
                ->all();
        }

        return is_string($value) ? $this->getOptionById($value) : null;
    }

    public function withEmptyTitle(?string $title): static
    {
        $this->emptyTitle = $title;

        return $this;
    }

    public function emptyTitle(): string
    {
        return $this->emptyTitle ?: throw new \RuntimeException("The empty title is not set.");
    }

    public function toArray()
    {
        return array_merge(parent::toArray(), [
            'options' => $this->options,
            'multiple' => $this->multiple,
            'emptyTitle' => $this->emptyTitle(),
            'emptyValue' => null, // TODO: Make configurable
        ]);
    }
}
