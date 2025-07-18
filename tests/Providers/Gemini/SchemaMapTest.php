<?php

declare(strict_types=1);

use Prism\Prism\Providers\Gemini\Maps\SchemaMap;
use Prism\Prism\Schema\AnyOfSchema;
use Prism\Prism\Schema\ArraySchema;
use Prism\Prism\Schema\BooleanSchema;
use Prism\Prism\Schema\EnumSchema;
use Prism\Prism\Schema\NumberSchema;
use Prism\Prism\Schema\ObjectSchema;
use Prism\Prism\Schema\OneOfSchema;
use Prism\Prism\Schema\StringSchema;

it('maps array schema correctly', function (): void {
    $map = (new SchemaMap(new ArraySchema(
        name: 'testArray',
        description: 'test array description',
        items: new StringSchema(
            name: 'testName',
            description: 'test string description',
            nullable: true,
        ),
        nullable: true,
    )))->toArray();

    expect($map)->toBe([
        'description' => 'test array description',
        'type' => 'array',
        'items' => [
            'description' => 'test string description',
            'type' => 'string',
            'nullable' => true,
        ],
        'nullable' => true,
    ]);
});

it('maps boolean schema correctly', function (): void {
    $map = (new SchemaMap(new BooleanSchema(
        name: 'testBoolean',
        description: 'test description',
        nullable: true,
    )))->toArray();

    expect($map)->toBe([
        'description' => 'test description',
        'type' => 'boolean',
        'nullable' => true,
    ]);
});

it('maps enum schema correctly', function (): void {
    $map = (new SchemaMap(new EnumSchema(
        name: 'testEnum',
        description: 'test description',
        options: ['option1', 'option2'],
        nullable: true,
    )))->toArray();

    expect($map)->toBe([
        'description' => 'test description',
        'enum' => ['option1', 'option2'],
        'type' => 'string',
        'nullable' => true,
    ]);
});

it('maps number schema correctly', function (): void {
    $map = (new SchemaMap(new NumberSchema(
        name: 'testNumber',
        description: 'test description',
        nullable: true,
    )))->toArray();

    expect($map)->toBe([
        'description' => 'test description',
        'type' => 'number',
        'nullable' => true,
    ]);
});

it('maps string schema correctly', function (): void {
    $map = (new SchemaMap(new StringSchema(
        name: 'testName',
        description: 'test description',
        nullable: true,
    )))->toArray();

    expect($map)->toBe([
        'description' => 'test description',
        'type' => 'string',
        'nullable' => true,
    ]);
});

it('maps object schema correctly', function (): void {
    $map = (new SchemaMap(new ObjectSchema(
        name: 'testObject',
        description: 'test object description',
        properties: [
            new StringSchema(
                name: 'testName',
                description: 'test string description',
            ),
        ],
        requiredFields: ['testName'],
        allowAdditionalProperties: true,
        nullable: true,
    )))->toArray();

    expect($map)->toBe([
        'description' => 'test object description',
        'type' => 'object',
        'properties' => [
            'testName' => [
                'description' => 'test string description',
                'type' => 'string',
            ],
        ],
        'required' => ['testName'],
        'nullable' => true,
    ]);
});

it('maps anyOf schema correctly', function (): void {
    $map = (new SchemaMap(new AnyOfSchema(
        name: 'testAnyOf',
        description: 'test anyOf description',
        schemas: [
            new StringSchema('value', 'String value'),
            new NumberSchema('value', 'Number value'),
        ],
        nullable: false,
    )))->toArray();

    expect($map)->toBe([
        'description' => 'test anyOf description',
        'anyOf' => [
            [
                'description' => 'String value',
                'type' => 'string',
            ],
            [
                'description' => 'Number value',
                'type' => 'number',
            ],
        ],
    ]);
});

it('maps anyOf schema with nullable correctly', function (): void {
    $map = (new SchemaMap(new AnyOfSchema(
        name: 'testAnyOf',
        description: 'test anyOf description',
        schemas: [
            new StringSchema('value', 'String value'),
            new NumberSchema('value', 'Number value'),
        ],
        nullable: true,
    )))->toArray();

    expect($map)->toBe([
        'description' => 'test anyOf description',
        'anyOf' => [
            [
                'description' => 'String value',
                'type' => 'string',
            ],
            [
                'description' => 'Number value',
                'type' => 'number',
            ],
            [
                'type' => 'null',
            ],
        ],
        'nullable' => true,
    ]);
});

it('maps oneOf schema correctly', function (): void {
    $map = (new SchemaMap(new OneOfSchema(
        name: 'testOneOf',
        description: 'test oneOf description',
        schemas: [
            new ObjectSchema(
                name: 'option1',
                description: 'First option',
                properties: [
                    new StringSchema('name', 'Name field'),
                ],
                requiredFields: ['name']
            ),
            new ObjectSchema(
                name: 'option2',
                description: 'Second option',
                properties: [
                    new NumberSchema('count', 'Count field'),
                ],
                requiredFields: ['count']
            ),
        ],
        nullable: false,
    )))->toArray();

    expect($map)->toBe([
        'description' => 'test oneOf description',
        'oneOf' => [
            [
                'description' => 'First option',
                'type' => 'object',
                'properties' => [
                    'name' => [
                        'description' => 'Name field',
                        'type' => 'string',
                    ],
                ],
                'required' => ['name'],
            ],
            [
                'description' => 'Second option',
                'type' => 'object',
                'properties' => [
                    'count' => [
                        'description' => 'Count field',
                        'type' => 'number',
                    ],
                ],
                'required' => ['count'],
            ],
        ],
    ]);
});

it('maps oneOf schema with nullable correctly', function (): void {
    $map = (new SchemaMap(new OneOfSchema(
        name: 'testOneOf',
        description: 'test oneOf description',
        schemas: [
            new StringSchema('value', 'String value'),
            new NumberSchema('value', 'Number value'),
        ],
        nullable: true,
    )))->toArray();

    expect($map)->toBe([
        'description' => 'test oneOf description',
        'oneOf' => [
            [
                'description' => 'String value',
                'type' => 'string',
            ],
            [
                'description' => 'Number value',
                'type' => 'number',
            ],
            [
                'type' => 'null',
            ],
        ],
        'nullable' => true,
    ]);
});
