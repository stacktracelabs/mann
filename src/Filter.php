<?php

namespace StackTrace\Mann;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Support\Collection;
use function collect;
use function get_class;
use function is_array;
use function method_exists;

class Filter
{
    /**
     * List of filterables within the filter.
     *
     * @var array
     */
    protected array $filterables = [];

    /**
     * The value of the filter.
     *
     * @var \StackTrace\Mann\FilterValue|null
     */
    protected ?FilterValue $value = null;

    /**
     * Add filterable to the filter.
     *
     * @param \StackTrace\Mann\Filterable $filterable
     * @return $this
     */
    public function addFilterable(Filterable $filterable): static
    {
        $this->filterables[] = $filterable;

        return $this;
    }

    /**
     * Retrieve filterables of the filter.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getFilterables(): Collection
    {
        return collect($this->filterables);
    }

    /**
     * Set the value of the filter.
     *
     * @param \StackTrace\Mann\FilterValue $value
     * @return $this
     */
    public function setValue(FilterValue $value): static
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Applies filter on the source.
     *
     * @param mixed $source
     * @param \StackTrace\Mann\FilterValue|null $value
     * @return mixed
     */
    public function apply(mixed $source, ?FilterValue $value = null): mixed
    {
        if ($value instanceof FilterValue) {
            $this->setValue($value);
        }

        if (is_array($source)) {
            return $this->filterCollection(collect($source));
        }

        if ($source instanceof Collection) {
            return $this->filterCollection($source);
        }

        if ($source instanceof EloquentBuilder) {
            return $this->filterEloquentBuilder($source);
        }

        throw new \RuntimeException("The source is not filterable.");
    }

    /**
     * Applies filter on eloquent builder.
     *
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function filterEloquentBuilder(EloquentBuilder $builder): EloquentBuilder
    {
        /** @var \StackTrace\Mann\Filterable $filterable */
        foreach ($this->filterables as $filterable) {
            if (! method_exists($filterable, 'applyOnEloquentBuilder')) {
                throw new \RuntimeException("Filterable [".get_class($filterable)."] does not support filtering on eloquent builder.");
            }

            $value = $this->value->getForFilterable($filterable->id());

            $filterable->applyOnEloquentBuilder($builder, $value);
        }

        return $builder;
    }

    /**
     * Applies filter on collection.
     *
     * @param \Illuminate\Support\Collection $collection
     * @return \Illuminate\Support\Collection
     */
    protected function filterCollection(Collection $collection): Collection
    {
        $result = $collection;

        /** @var \StackTrace\Mann\Filterable $filterable */
        foreach ($this->filterables as $filterable) {
            if (! method_exists($filterable, 'applyOnCollection')) {
                throw new \RuntimeException("Filterable [".get_class($filterable)."] does not support filtering collections.");
            }

            $value = $this->value->getForFilterable($filterable->id());

            $result = $filterable->applyOnCollection($result, $value);
        }

        return $result;
    }
}
