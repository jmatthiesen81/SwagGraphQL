<?php declare(strict_types=1);

namespace SwagGraphQL\Schema;

use GraphQL\Type\Definition\EnumType;
use GraphQL\Type\Definition\InputObjectType;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\MultiFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\RangeFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Sorting\FieldSorting;
use SwagGraphQL\Types\DateType;
use SwagGraphQL\Types\JsonType;

class CustomTypes
{
    /** @var DateType */
    private static $dateType;

    /** @var JsonType */
    private static $jsonType;

    /** @var EnumType */
    private static $sortDirection;

    /** @var ObjectType */
    private static $pageInfo;

    /** @var InputObjectType */
    private static $query;

    /** @var EnumType */
    private static $queryOperator;

    /** @var EnumType */
    private static $queryTypes;

    /** @var EnumType */
    private static $rangeOperator;

    /** @var EnumType */
    private static $aggregationTypes;

    /** @var InputObjectType */
    private static $aggregation;

    /** @var ObjectType */
    private static $aggregationResult;

    // Custom Scalars
    public static function date(): DateType
    {
        if (!static::$dateType) {
            static::$dateType = new DateType();
        }

        return static::$dateType;
    }

    public static function json(): JsonType
    {
        if (!static::$jsonType) {
            static::$jsonType = new JsonType();
        }

        return static::$jsonType;
    }

    // Enums
    public static function sortDirection(): EnumType
    {
        if (!static::$sortDirection) {
            static::$sortDirection = new EnumType([
                'name' => 'sortDirection',
                'values' => [
                    'ASC' => [
                        'value' => FieldSorting::ASCENDING
                    ],
                    'DESC' => [
                        'value' => FieldSorting::DESCENDING
                    ]
                ]
            ]);
        }

        return static::$sortDirection;
    }

    public static function queryOperator(): EnumType
    {
        if (!static::$queryOperator) {
            static::$queryOperator = new EnumType([
                'name' => 'queryOperator',
                'values' => [
                    'AND' => [
                        'value' => MultiFilter::CONNECTION_AND
                    ],
                    'OR' => [
                        'value' => MultiFilter::CONNECTION_OR
                    ]
                ]
            ]);
        }

        return static::$queryOperator;
    }

    public static function rangeOperator(): EnumType
    {
        if (!static::$rangeOperator) {
            static::$rangeOperator = new EnumType([
                'name' => 'rangeOperator',
                'values' => [
                    'GTE' => [
                        'value' => RangeFilter::GTE
                    ],
                    'GT' => [
                        'value' => RangeFilter::GT
                    ],
                    'LTE' => [
                        'value' => RangeFilter::LTE
                    ],
                    'LT' => [
                        'value' => RangeFilter::LT
                    ]
                ]
            ]);
        }

        return static::$rangeOperator;
    }

    public static function queryTypes(): EnumType
    {
        if (!static::$queryTypes) {
            static::$queryTypes = new EnumType([
                'name' => 'queryTypes',
                'values' => ['equals', 'contains', 'equalsAny', 'multi', 'not', 'range']
            ]);
        }

        return static::$queryTypes;
    }

    public static function aggregationTypes(): EnumType
    {
        if (!static::$aggregationTypes) {
            static::$aggregationTypes = new EnumType([
                'name' => 'aggregationTypes',
                'values' => ['avg', 'cardinality', 'count', 'max', 'min', 'stats', 'sum', 'value_count']
            ]);
        }

        return static::$aggregationTypes;
    }

    // Objects
    public static function pageInfo(): ObjectType
    {
        if (!static::$pageInfo) {
            static::$pageInfo = new ObjectType([
                'name' => 'pageInfo',
                'fields' => [
                    'endCursor' => ['type' => Type::id()],
//                'startCursor' => [
//                    'type' => Type::id()
//                ],
                    'hasNextPage' => ['type' => Type::boolean()],
//                'hasPreviousPage' => [
//                    'type' => Type::boolean()
//                ]
                ]
            ]);
        }

        return static::$pageInfo;
    }

    public static function aggregationResult(): ObjectType
    {
        if (!static::$aggregationResult) {
            static::$aggregationResult = new ObjectType([
                'name' => 'aggregationResults',
                'fields' => [
                    'name' => ['type' => Type::string()],
                    'results' => ['type' => Type::listOf(new ObjectType([
                        'name' => 'aggregationResult',
                        'fields' => [
                            'type' => ['type' => Type::string()],
                            'result' => ['type' => Type::float()]
                        ]
                    ]))]

                ]
            ]);
        }

        return static::$aggregationResult;
    }

    // Inputs
    public static function query(): InputObjectType
    {
        if (!static::$query) {
            static::$query = new InputObjectType([
                'name' => 'query',
                'fields' => function () {
                    return [
                        'type' => Type::nonNull(static::queryTypes()),
                        'operator' => static::queryOperator(),
                        'queries' => Type::listOf(static::query()),
                        'field' => Type::string(),
                        'value' => Type::string(),
                        'parameters' => Type::listOf(new InputObjectType([
                            'name' => 'parameter',
                            'fields' => [
                                'operator' => Type::nonNull(static::rangeOperator()),
                                'value' => Type::nonNull(Type::float())
                            ]
                        ]))
                    ];
                }
            ]);
        }

        return static::$query;
    }

    public static function aggregation(): InputObjectType
    {
        if (!static::$aggregation) {
            static::$aggregation = new InputObjectType([
                'name' => 'aggregation',
                'fields' => function () {
                    return [
                        'type' => Type::nonNull(static::aggregationTypes()),
                        'name' => Type::nonNull(Type::string()),
                        'field' => Type::nonNull(Type::string()),
                    ];
                }
            ]);
        }

        return static::$aggregation;
    }
}