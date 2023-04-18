<?php

namespace StackTrace\Mann;

use Illuminate\Http\Request;
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

    public static function fromRequest(Request $request, array $keys = []): static
    {
        return static::fromArray(empty($keys) ? $request->all() : $request->only($keys));
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
