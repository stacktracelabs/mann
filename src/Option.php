<?php

namespace StackTrace\Mann;

use Illuminate\Contracts\Support\Arrayable;

class Option implements Arrayable
{
    public function __construct(
        protected string $id,
        protected string $title
    ) { }

    /**
     * Retrieve the ID of the filter.
     *
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * Retrieve the value of the option.
     *
     * @return mixed
     */
    public function value(): mixed
    {
        return $this->getId();
    }

    public function toArray()
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
        ];
    }
}
