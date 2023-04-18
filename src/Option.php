<?php

namespace StackTrace\Mann;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Arr;
use function call_user_func;
use function is_null;

class Option implements Arrayable
{
    /**
     * The value of the option.
     */
    protected \Closure|string|int|null $value = null;

    /**
     * Extra attributes of the option.
     */
    protected array $extra = [];

    public function __construct(
        protected string|int $id,
        protected string $title
    ) { }

    /**
     * Retrieve the identifier of the option.
     */
    public function getId(): string|int
    {
        return $this->id;
    }

    /**
     * Retrieve the title of the option.
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * Retrieves the value of the option. When no value is set,
     * the identifier is considered as option value.
     */
    public function value(): mixed
    {
        if ($this->value instanceof \Closure) {
            return call_user_func($this->value);
        }

        if (is_null($this->value)) {
            return $this->getId();
        }

        return $this->value;
    }

    /**
     * Set custom value for the option.
     */
    public function resolveValueUsing(\Closure|string|int $value): static
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Removes custom extra attribute from the option.
     */
    public function withoutExtra(string $key): static
    {
        Arr::forget($this->extra, $key);

        return $this;
    }

    /**
     * Set custom extra attribute on the option.
     */
    public function withExtra(string $key, $value): static
    {
        Arr::set($this->extra, $key, $value);

        return $this;
    }

    /**
     * Retrieve the extra attributes of the option.
     */
    public function getExtra(): array
    {
        return $this->extra;
    }

    public function toArray()
    {
        return [
            'id' => $this->getId(),
            'title' => $this->getTitle(),
            'extra' => $this->getExtra(),
        ];
    }
}
