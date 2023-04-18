<?php

namespace StackTrace\Mann;

use Illuminate\Contracts\Support\Arrayable;
use function get_class;
use function is_null;
use function is_string;

class Filterable implements Arrayable
{
    /**
     * Unique identifier of the filterable.
     */
    protected ?string $id = null;

    /**
     * The title of the filterable.
     */
    protected ?string $title = null;

    /**
     * The attribute to be filtered on.
     */
    protected ?string $attribute = null;

    /**
     * The component of the filterable.
     */
    protected ?string $component = null;

    /**
     * The name of the query paramter.
     */
    protected ?string $queryParameter = null;

    /**
     * Set the query parameter name for the filterable value.
     */
    public function queryParameter(?string $name): static
    {
        $this->queryParameter = $name;

        return $this;
    }

    /**
     * Retrieve the query parameter name for the filterable value.
     */
    public function getQueryParameter(): string
    {
        if (is_null($this->queryParameter)) {
            return $this->getId();
        }

        return $this->queryParameter;
    }

    /**
     * Set the identifier of the filterable.
     */
    public function id(?string $id): static
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Retrive unique identifier of the filterable.
     */
    public function getId(): string
    {
        if (is_string($this->id)) {
            return $this->id;
        }

        return $this->getAttribute();
    }

    /**
     * Set the title of the filterable.
     */
    public function title(?string $title): static
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Retrieve the title of the filterable.
     */
    public function getTitle(): string
    {
        if (is_null($this->title)) {
            throw new \RuntimeException("The title of the filterable [".get_class($this)."] is not set.");
        }

        return $this->title;
    }

    /**
     * Set the attribute to be filtered on.
     */
    public function attribute(?string $attribute): static
    {
        $this->attribute = $attribute;

        return $this;
    }

    /**
     * Retrieve the attribute to be filtered on.
     */
    public function getAttribute(): string
    {
        if (is_string($this->attribute)) {
            return $this->attribute;
        }

        throw new \RuntimeException("The attribute is not set on [".get_class($this)."]");
    }

    /**
     * Set the component used to render the filterable.
     */
    public function component(?string $component): static
    {
        $this->component = $component;

        return $this;
    }

    /**
     * Retrieve component used to render the filterable.
     */
    protected function getComponent(): string
    {
        if ($this->component != null) {
            return $this->component;
        }

        throw new \RuntimeException("The component is not set on [".get_class($this)."]");
    }

    /**
     * Retrieve value of the empty filterable.
     */
    public function getEmptyValue(): mixed
    {
        return null;
    }

    /**
     * Sanitize given filter value.
     */
    public function sanitizeValue(mixed $value): mixed
    {
        return $value;
    }

    public function toArray()
    {
        return [
            'id' => $this->getId(),
            'title' => $this->getTitle(),
            'component' => $this->getComponent(),
            'emptyValue' => $this->getEmptyValue(),
            'queryParameter' => $this->getQueryParameter(),
        ];
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
        return (new static)->title($title)->attribute($attribute);
    }
}
