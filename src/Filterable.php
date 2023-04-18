<?php

namespace StackTrace\Mann;

use function get_class;
use function is_null;
use function is_string;

class Filterable
{
    /**
     * Unique identifier of the filterable.
     *
     * @var string|null
     */
    protected ?string $id = null;

    /**
     * The title of the filterable.
     *
     * @var string|null
     */
    protected ?string $title = null;

    /**
     * The attribute to be filtered on.
     *
     * @var string|null
     */
    protected ?string $attribute = null;

    /**
     * Retrive unique identifier of the filterable.
     *
     * @return string
     */
    public function id(): string
    {
        if (is_string($this->id)) {
            return $this->id;
        }

        return $this->attribute();
    }

    /**
     * Set the identifier of the filterable.
     *
     * @param string|null $id
     * @return $this
     */
    public function withId(?string $id): static
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Retrieve the title of the filterable.
     *
     * @return string
     */
    public function title(): string
    {
        if (is_null($this->title)) {
            throw new \RuntimeException("The title of the filterable [".get_class($this)."] is not set.");
        }

        return $this->title;
    }

    /**
     * Set the title of the filterable.
     *
     * @param string|null $title
     * @return $this
     */
    public function withTitle(?string $title): static
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Retrieve the attribute to be filtered on.
     *
     * @return string
     */
    public function attribute(): string
    {
        if (is_string($this->attribute)) {
            return $this->attribute;
        }

        throw new \RuntimeException("The attribute is not set on [".get_class($this)."]");
    }

    /**
     * Set the attribute to be filtered on.
     *
     * @param string|null $attribute
     * @return $this
     */
    public function withAttribute(?string $attribute): static
    {
        $this->attribute = $attribute;

        return $this;
    }

    /**
     * Creates new filterable with title for given attribute.
     *
     * @param string $title
     * @param string $attribute
     * @return static
     */
    public static function make(string $title, string $attribute): static
    {
        return (new static)->withTitle($title)->withAttribute($attribute);
    }
}
