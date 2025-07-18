<?php

declare(strict_types=1);

namespace Tests;

use Prism\Prism\Schema\AnyOfSchema;
use Prism\Prism\Schema\ArraySchema;
use Prism\Prism\Schema\BooleanSchema;
use Prism\Prism\Schema\EnumSchema;
use Prism\Prism\Schema\NumberSchema;
use Prism\Prism\Schema\ObjectSchema;
use Prism\Prism\Schema\OneOfSchema;
use Prism\Prism\Schema\StringSchema;

it('they can have nested properties', function (): void {
    $schema = new ObjectSchema(
        name: 'user',
        description: 'a user object',
        properties: [
            new StringSchema('name', 'the users name'),
            new NumberSchema('age', 'the users age'),
            new EnumSchema(
                name: 'status',
                description: 'the users status',
                options: [
                    'active',
                    'inactive',
                    'suspended',
                ]
            ),
            new ArraySchema(
                name: 'hobbies',
                description: 'the users hobbies',
                items: new StringSchema('hobby', 'the users hobby')
            ),
            new ObjectSchema(
                name: 'address',
                description: 'the users address',
                properties: [
                    new StringSchema('street', 'the street part of the address'),
                    new StringSchema('city', 'the city part of the address'),
                    new StringSchema('country', 'the country part of the address'),
                    new NumberSchema('zip', 'the zip code part of the address'),
                ],
                requiredFields: ['street', 'city', 'country', 'zip']
            ),
        ]
    );

    expect($schema->toArray())->toBe([
        'description' => 'a user object',
        'type' => 'object',
        'properties' => [
            'name' => [
                'description' => 'the users name',
                'type' => 'string',
            ],
            'age' => [
                'description' => 'the users age',
                'type' => 'number',
            ],
            'status' => [
                'description' => 'the users status',
                'enum' => [
                    'active',
                    'inactive',
                    'suspended',
                ],
                'type' => 'string',
            ],
            'hobbies' => [
                'description' => 'the users hobbies',
                'type' => 'array',
                'items' => [
                    'description' => 'the users hobby',
                    'type' => 'string',
                ],
            ],
            'address' => [
                'description' => 'the users address',
                'type' => 'object',
                'properties' => [
                    'street' => [
                        'description' => 'the street part of the address',
                        'type' => 'string',
                    ],
                    'city' => [
                        'description' => 'the city part of the address',
                        'type' => 'string',
                    ],
                    'country' => [
                        'description' => 'the country part of the address',
                        'type' => 'string',
                    ],
                    'zip' => [
                        'description' => 'the zip code part of the address',
                        'type' => 'number',
                    ],
                ],
                'required' => ['street', 'city', 'country', 'zip'],
                'additionalProperties' => false,
            ],
        ],
        'required' => [],
        'additionalProperties' => false,
    ]);
});

it('they can be nullable', function (): void {
    $schema = new ObjectSchema(
        name: 'user',
        description: 'a user object',
        properties: [
            new StringSchema('name', 'the users name', nullable: true),
            new NumberSchema('age', 'the users age', nullable: true),
            new EnumSchema(
                name: 'status',
                description: 'the users status',
                options: [
                    'active',
                    'inactive',
                    'suspended',
                ],
                nullable: true
            ),
            new ArraySchema(
                name: 'hobbies',
                description: 'the users hobbies',
                items: new StringSchema('hobby', 'the users hobby'),
                nullable: true
            ),
            new BooleanSchema(name: 'is_admin', description: 'is an administrative user', nullable: true),
            new ObjectSchema(
                name: 'address',
                description: 'the users address',
                properties: [
                    new StringSchema('street', 'the street part of the address'),
                    new StringSchema('city', 'the city part of the address'),
                    new StringSchema('country', 'the country part of the address'),
                    new NumberSchema('zip', 'the zip code part of the address'),
                ],
                requiredFields: ['street', 'city', 'country', 'zip']
            ),
        ],
        nullable: true
    );

    expect($schema->toArray())->toBe([
        'description' => 'a user object',
        'type' => ['object', 'null'],
        'properties' => [
            'name' => [
                'description' => 'the users name',
                'type' => ['string', 'null'],
            ],
            'age' => [
                'description' => 'the users age',
                'type' => ['number', 'null'],
            ],
            'status' => [
                'description' => 'the users status',
                'enum' => [
                    'active',
                    'inactive',
                    'suspended',
                ],
                'type' => ['string', 'null'],
            ],
            'hobbies' => [
                'description' => 'the users hobbies',
                'type' => ['array', 'null'],
                'items' => [
                    'description' => 'the users hobby',
                    'type' => 'string',
                ],
            ],
            'is_admin' => [
                'description' => 'is an administrative user',
                'type' => ['boolean', 'null'],
            ],
            'address' => [
                'description' => 'the users address',
                'type' => 'object',
                'properties' => [
                    'street' => [
                        'description' => 'the street part of the address',
                        'type' => 'string',
                    ],
                    'city' => [
                        'description' => 'the city part of the address',
                        'type' => 'string',
                    ],
                    'country' => [
                        'description' => 'the country part of the address',
                        'type' => 'string',
                    ],
                    'zip' => [
                        'description' => 'the zip code part of the address',
                        'type' => 'number',
                    ],
                ],
                'required' => ['street', 'city', 'country', 'zip'],
                'additionalProperties' => false,
            ],
        ],
        'required' => [],
        'additionalProperties' => false,
    ]);
});

it('nullable enums include types', function (): void {
    $enumSchema = new EnumSchema(
        name: 'temp',
        description: 'sick or fever temp',
        options: [98.6, 100, 'unknown', 105],
        nullable: true
    );

    expect($enumSchema->toArray())->toBe([
        'description' => 'sick or fever temp',
        'enum' => [98.6, 100, 'unknown', 105],
        'type' => [
            'number',
            'string',
            'null',
        ],
    ]);
});

it('non-nullable enum with single type returns single type', function (): void {
    $enumSchema = new EnumSchema(
        name: 'user_type',
        description: 'the type of user',
        options: ['admin', 'super_admin', 'standard']
    );

    expect($enumSchema->toArray())->toBe([
        'description' => 'the type of user',
        'enum' => ['admin', 'super_admin', 'standard'],
        'type' => 'string',
    ]);
});

it('can use anyOf schema for multiple types', function (): void {
    $anyOfSchema = new AnyOfSchema(
        name: 'flexible_id',
        description: 'An ID that can be either a string or number',
        schemas: [
            new StringSchema('id', 'String ID'),
            new NumberSchema('id', 'Numeric ID'),
        ]
    );

    expect($anyOfSchema->toArray())->toBe([
        'description' => 'An ID that can be either a string or number',
        'anyOf' => [
            [
                'description' => 'String ID',
                'type' => 'string',
            ],
            [
                'description' => 'Numeric ID',
                'type' => 'number',
            ],
        ],
    ]);
});

it('can use anyOf schema with complex types', function (): void {
    $anyOfSchema = new AnyOfSchema(
        name: 'address',
        description: 'Address that can be simple string or complex object',
        schemas: [
            new StringSchema('address', 'Simple address string'),
            new ObjectSchema(
                name: 'address',
                description: 'Structured address object',
                properties: [
                    new StringSchema('street', 'Street address'),
                    new StringSchema('city', 'City name'),
                    new StringSchema('zip', 'Zip code'),
                ],
                requiredFields: ['street', 'city']
            ),
        ]
    );

    expect($anyOfSchema->toArray())->toBe([
        'description' => 'Address that can be simple string or complex object',
        'anyOf' => [
            [
                'description' => 'Simple address string',
                'type' => 'string',
            ],
            [
                'description' => 'Structured address object',
                'type' => 'object',
                'properties' => [
                    'street' => [
                        'description' => 'Street address',
                        'type' => 'string',
                    ],
                    'city' => [
                        'description' => 'City name',
                        'type' => 'string',
                    ],
                    'zip' => [
                        'description' => 'Zip code',
                        'type' => 'string',
                    ],
                ],
                'required' => ['street', 'city'],
                'additionalProperties' => false,
            ],
        ],
    ]);
});

it('can make anyOf schema nullable', function (): void {
    $anyOfSchema = new AnyOfSchema(
        name: 'nullable_value',
        description: 'Value that can be string, number, or null',
        schemas: [
            new StringSchema('value', 'String value'),
            new NumberSchema('value', 'Numeric value'),
        ],
        nullable: true
    );

    expect($anyOfSchema->toArray())->toBe([
        'description' => 'Value that can be string, number, or null',
        'anyOf' => [
            [
                'description' => 'String value',
                'type' => 'string',
            ],
            [
                'description' => 'Numeric value',
                'type' => 'number',
            ],
            [
                'type' => 'null',
            ],
        ],
    ]);
});

it('can use oneOf schema for mutually exclusive types', function (): void {
    $oneOfSchema = new OneOfSchema(
        name: 'payment_method',
        description: 'Payment method must be exactly one of these types',
        schemas: [
            new ObjectSchema(
                name: 'credit_card',
                description: 'Credit card payment',
                properties: [
                    new StringSchema('card_number', 'Card number'),
                    new StringSchema('cvv', 'CVV code'),
                ],
                requiredFields: ['card_number', 'cvv']
            ),
            new ObjectSchema(
                name: 'bank_transfer',
                description: 'Bank transfer payment',
                properties: [
                    new StringSchema('account_number', 'Bank account number'),
                    new StringSchema('routing_number', 'Routing number'),
                ],
                requiredFields: ['account_number', 'routing_number']
            ),
        ]
    );

    expect($oneOfSchema->toArray())->toBe([
        'description' => 'Payment method must be exactly one of these types',
        'oneOf' => [
            [
                'description' => 'Credit card payment',
                'type' => 'object',
                'properties' => [
                    'card_number' => [
                        'description' => 'Card number',
                        'type' => 'string',
                    ],
                    'cvv' => [
                        'description' => 'CVV code',
                        'type' => 'string',
                    ],
                ],
                'required' => ['card_number', 'cvv'],
                'additionalProperties' => false,
            ],
            [
                'description' => 'Bank transfer payment',
                'type' => 'object',
                'properties' => [
                    'account_number' => [
                        'description' => 'Bank account number',
                        'type' => 'string',
                    ],
                    'routing_number' => [
                        'description' => 'Routing number',
                        'type' => 'string',
                    ],
                ],
                'required' => ['account_number', 'routing_number'],
                'additionalProperties' => false,
            ],
        ],
    ]);
});

it('can use oneOf schema with simple types', function (): void {
    $oneOfSchema = new OneOfSchema(
        name: 'strict_id',
        description: 'ID must be exactly string or exactly number',
        schemas: [
            new StringSchema('id', 'String ID format'),
            new NumberSchema('id', 'Numeric ID format'),
        ]
    );

    expect($oneOfSchema->toArray())->toBe([
        'description' => 'ID must be exactly string or exactly number',
        'oneOf' => [
            [
                'description' => 'String ID format',
                'type' => 'string',
            ],
            [
                'description' => 'Numeric ID format',
                'type' => 'number',
            ],
        ],
    ]);
});

it('can make oneOf schema nullable', function (): void {
    $oneOfSchema = new OneOfSchema(
        name: 'optional_format',
        description: 'Data in one specific format or null',
        schemas: [
            new ObjectSchema(
                name: 'json_format',
                description: 'JSON formatted data',
                properties: [
                    new StringSchema('format', 'Format type'),
                    new StringSchema('data', 'JSON string data'),
                ],
                requiredFields: ['format', 'data']
            ),
            new ObjectSchema(
                name: 'xml_format',
                description: 'XML formatted data',
                properties: [
                    new StringSchema('format', 'Format type'),
                    new StringSchema('xml', 'XML string data'),
                ],
                requiredFields: ['format', 'xml']
            ),
        ],
        nullable: true
    );

    expect($oneOfSchema->toArray())->toBe([
        'description' => 'Data in one specific format or null',
        'oneOf' => [
            [
                'description' => 'JSON formatted data',
                'type' => 'object',
                'properties' => [
                    'format' => [
                        'description' => 'Format type',
                        'type' => 'string',
                    ],
                    'data' => [
                        'description' => 'JSON string data',
                        'type' => 'string',
                    ],
                ],
                'required' => ['format', 'data'],
                'additionalProperties' => false,
            ],
            [
                'description' => 'XML formatted data',
                'type' => 'object',
                'properties' => [
                    'format' => [
                        'description' => 'Format type',
                        'type' => 'string',
                    ],
                    'xml' => [
                        'description' => 'XML string data',
                        'type' => 'string',
                    ],
                ],
                'required' => ['format', 'xml'],
                'additionalProperties' => false,
            ],
            [
                'type' => 'null',
            ],
        ],
    ]);
});
