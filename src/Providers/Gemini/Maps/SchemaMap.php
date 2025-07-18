<?php

namespace Prism\Prism\Providers\Gemini\Maps;

use Prism\Prism\Contracts\Schema;
use Prism\Prism\Schema\AnyOfSchema;
use Prism\Prism\Schema\ArraySchema;
use Prism\Prism\Schema\BooleanSchema;
use Prism\Prism\Schema\NumberSchema;
use Prism\Prism\Schema\ObjectSchema;
use Prism\Prism\Schema\OneOfSchema;

class SchemaMap
{
    public function __construct(
        private readonly Schema $schema,
    ) {}

    /**
     * @return array<mixed>
     */
    public function toArray(): array
    {
        // Handle AnyOfSchema and OneOfSchema specially
        if ($this->schema instanceof AnyOfSchema || $this->schema instanceof OneOfSchema) {
            return $this->mapCompositeSchema();
        }

        return array_merge([
            ...array_filter([
                ...$this->schema->toArray(),
                'type' => $this->mapType(),
                'additionalProperties' => null,
            ]),
        ], array_filter([
            'items' => property_exists($this->schema, 'items') ?
                (new self($this->schema->items))->toArray() :
                null,
            // Only include 'properties' field for ObjectSchema
            'properties' => $this->schema instanceof ObjectSchema && property_exists($this->schema, 'properties') ?
                array_reduce($this->schema->properties, fn (array $carry, Schema $property) => [
                    ...$carry,
                    $property->name() => (new self($property))->toArray(),
                ], []) :
                null,
            'nullable' => property_exists($this->schema, 'nullable')
                ? $this->schema->nullable
                : null,
        ]));
    }

    protected function mapType(): string
    {
        if ($this->schema instanceof ArraySchema) {
            return 'array';
        }
        if ($this->schema instanceof BooleanSchema) {
            return 'boolean';
        }
        if ($this->schema instanceof NumberSchema) {
            return 'number';
        }
        if ($this->schema instanceof ObjectSchema) {
            return 'object';
        }

        return 'string';
    }

    /**
     * @return array<mixed>
     */
    protected function mapCompositeSchema(): array
    {
        $schemaArray = $this->schema->toArray();
        $result = [
            'description' => $schemaArray['description'],
        ];

        // Map the schemas within anyOf/oneOf
        if ($this->schema instanceof AnyOfSchema) {
            $result['anyOf'] = array_map(
                fn (array $schema): array => $this->mapNestedSchema($schema),
                $schemaArray['anyOf']
            );
        } elseif ($this->schema instanceof OneOfSchema) {
            $result['oneOf'] = array_map(
                fn (array $schema): array => $this->mapNestedSchema($schema),
                $schemaArray['oneOf']
            );
        }

        // Handle nullable if present
        if (property_exists($this->schema, 'nullable') && $this->schema->nullable) {
            $result['nullable'] = true;
        }

        return $result;
    }

    /**
     * @param  array<mixed>  $schema
     * @return array<mixed>
     */
    protected function mapNestedSchema(array $schema): array
    {
        // If it's just a null type, return as is
        if ($schema === ['type' => 'null']) {
            return $schema;
        }

        // Map the schema while preserving Gemini-specific formatting
        $result = $schema;

        // Remove additionalProperties if present (Gemini doesn't use it)
        unset($result['additionalProperties']);

        return $result;
    }
}
