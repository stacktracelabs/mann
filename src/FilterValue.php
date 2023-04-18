<?php

namespace StackTrace\Mann;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class FilterValue
{
    public function __construct(
        protected array $value = []
    ) { }

    public function set(string $filterableId, mixed $value): static
    {
        Arr::set($this->value, $filterableId, $value);

        return $this;
    }

    public function forFilterable(string $filterableId, $default = null): mixed
    {
        return Arr::get($this->value, $filterableId, $default);
    }

    public static function fromRequest(Request $request, array $keys = []): static
    {
        return static::fromArray(empty($keys) ? $request->all() : $request->only($keys));
    }

    public static function fromArray(array $value): static
    {
        return new static($value);
    }
}
