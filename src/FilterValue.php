<?php

namespace StackTrace\Mann;

use Illuminate\Support\Arr;

class FilterValue
{
    protected array $value = [];

    public function set(string $filterableId, mixed $value): static
    {
        Arr::set($this->value, $filterableId, $value);

        return $this;
    }

    public function getForFilterable(string $filterableId): mixed
    {
        return Arr::get($this->value, $filterableId);
    }

    public static function fromArray(array $value): static
    {
        $filterValue = new FilterValue();

        foreach ($value as $key => $filterVal) {
            $filterValue->set($key, $filterVal);
        }

        return $filterValue;
    }
}
