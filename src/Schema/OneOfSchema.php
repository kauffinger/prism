<?php

declare(strict_types=1);

namespace Prism\Prism\Schema;

use Prism\Prism\Concerns\NullableSchema;
use Prism\Prism\Contracts\Schema;

class OneOfSchema implements Schema
{
    use NullableSchema;

    /**
     * @param  array<int, Schema>  $schemas
     */
    public function __construct(
        public readonly string $name,
        public readonly string $description,
        public readonly array $schemas,
        public readonly bool $nullable = false,
    ) {}

    #[\Override]
    public function name(): string
    {
        return $this->name;
    }

    #[\Override]
    public function toArray(): array
    {
        $schemas = array_map(fn (Schema $schema): array => $schema->toArray(), $this->schemas);

        $result = [
            'description' => $this->description,
            'oneOf' => $schemas,
        ];

        if ($this->nullable) {
            $result['oneOf'][] = ['type' => 'null'];
        }

        return $result;
    }
}
