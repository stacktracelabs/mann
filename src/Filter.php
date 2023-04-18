<?php

namespace StackTrace\Mann;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use function collect;
use function get_class;
use function is_array;
use function method_exists;

class Filter implements Arrayable
{
    /**
     * List of filterables within the filter.
     */
    protected array $filterables = [];

    /**
     * The value of the filter.
     */
    protected ?FilterValue $value = null;

    /**
     * Add filterable to the filter.
     */
    public function addFilterable(Filterable $filterable): static
    {
        $this->filterables[] = $filterable;

        return $this;
    }

    /**
     * Retrieve filterables of the filter.
     */
    public function getFilterables(): Collection
    {
        return collect($this->filterables);
    }

    /**
     * Set the value of the filter.
     */
    public function setValue(FilterValue $value): static
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Set the value of the filter from request.
     */
    public function setValueFromRequest(Request $request): static
    {
        $keys = $this->getFilterables()->map(fn (Filterable $filterable) => $filterable->getId())->all();

        return $this->setValue(FilterValue::fromRequest($request, $keys));
    }

    /**
     * When define method exists on the filter, we'll call
     * this method to define filterables on the filter.
     */
    protected function ensureFilterDefined(): void
    {
        if (method_exists($this, 'define')) {
            collect($this->define())->map(function (Filterable $filterable) {
                $this->addFilterable($filterable);
            });
        }
    }

    /**
     * Applies filter on the source.
     */
    public function apply(mixed $source, ?FilterValue $value = null): mixed
    {
        $this->ensureFilterDefined();

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
     */
    protected function filterEloquentBuilder(EloquentBuilder $builder): EloquentBuilder
    {
        $this->getFilterables()->each(function (Filterable $filterable) use ($builder) {
            if (! method_exists($filterable, 'applyOnEloquentBuilder')) {
                throw new \RuntimeException("Filterable [".get_class($filterable)."] does not support filtering on eloquent builder.");
            }

            $value = $this->getValueForFilterable($filterable);

            $filterable->applyOnEloquentBuilder($builder, $value);
        });

        return $builder;
    }

    /**
     * Applies filter on collection.
     */
    protected function filterCollection(Collection $collection): Collection
    {
        return $this->getFilterables()->reduce(function (Collection $collection, Filterable $filterable) {
            if (! method_exists($filterable, 'applyOnCollection')) {
                throw new \RuntimeException("Filterable [".get_class($filterable)."] does not support filtering collections.");
            }

            $value = $this->getValueForFilterable($filterable);

            return $filterable->applyOnCollection($collection, $value);
        }, $collection);
    }

    /**
     * Retrieve current value for given filterable.
     */
    protected function getValueForFilterable(Filterable $filterable): mixed
    {
        return $filterable->sanitizeValue(
            $this->value?->forFilterable($filterable->getId(), fn () => $filterable->getEmptyValue())
        );
    }

    public function toArray()
    {
        return [
            'filterables' => $this->getFilterables()->map(function (Filterable $filterable) {
                return [
                    'filterable' => $filterable,
                    'value' => $this->getValueForFilterable($filterable),
                ];
            })->all(),
        ];
    }
}
