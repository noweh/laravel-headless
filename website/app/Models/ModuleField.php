<?php

namespace App\Models;

/**
 * Class ModuleField
 * @package App\Models
 *
 * @OA\Schema(
 *     description="ModuleField model",
 *     title="ModuleField model",
 *     required={"published, 'structure_name"},
 *     @OA\Xml(
 *         name="ModuleField"
 *     ),
 * )
 */
class ModuleField extends AbstractModel
{
    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'module_field_id';

    public const STRUCTURE_TYPES = [
        'STRING' => 'string',
        'INT' => 'int',
        'FILE' => 'file'
    ];

    public const STRUCTURE_INFO = [
        'FIELD_TITLE' => [
            'name' => 'title',
            'type' => self::STRUCTURE_TYPES['STRING'],
            'is_readable' => true
        ],
        'FIELD_TEXT' => [
            'name' => 'text',
            'type' => self::STRUCTURE_TYPES['STRING'],
            'is_readable' => true
        ],
        'FIELD_HASHTAG' => [
            'name' => 'hashtag',
            'type' => self::STRUCTURE_TYPES['STRING'],
            'is_readable' => true
        ],
        'FIELD_TIME' => [
            'name' => 'time',
            'type' => self::STRUCTURE_TYPES['INT'],
            'is_readable' => true
        ],
        'FIELD_LINK' => [
            'name' => 'link',
            'type' => self::STRUCTURE_TYPES['STRING'],
            'is_readable' => true
        ],
        'FIELD_COVER' => [
            'name' => 'cover',
            'type' => self::STRUCTURE_TYPES['FILE'],
            'is_readable' => false
        ],
        'FIELD_COVER_LINK' => [
            'name' => 'cover_link',
            'type' => self::STRUCTURE_TYPES['STRING'],
            'is_readable' => true
        ],
        'FIELD_VIDEO' => [
            'name' => 'video',
            'type' => self::STRUCTURE_TYPES['FILE'],
            'is_readable' => false
        ],
        'FIELD_VIDEO_LINK' => [
            'name' => 'video_link',
            'type' => self::STRUCTURE_TYPES['STRING'],
            'is_readable' => true
        ],
        'FIELD_AR_OBJECT' => [
            'name' => 'ar_object',
            'type' => self::STRUCTURE_TYPES['FILE'],
            'is_readable' => false
        ],
        'FIELD_USDZ_LINK' => [
            'name' => 'usdz_link',
            'type' => self::STRUCTURE_TYPES['STRING'],
            'is_readable' => true
        ],
        'FIELD_OBJ_LINK' => [
            'name' => 'obj_link',
            'type' => self::STRUCTURE_TYPES['STRING'],
            'is_readable' => true
        ],
        'FIELD_DURATION' => [
            'name' => 'duration',
            'type' => self::STRUCTURE_TYPES['INT'],
            'is_readable' => true
        ]
    ];

    /**
     * @OA\Property(),
     * @var integer
     */
    private $module_field_id;

    /**
     * @OA\Property(
     *     example="2017-02-02 18:31:45",
     *     format="datetime",
     *     type="string"
     * )
     *
     * @var \DateTime
     */
    private $created_at;

    /**
     * @OA\Property(
     *     example="2017-02-02 18:31:45",
     *     format="datetime",
     *     type="string"
     * )
     *
     * @var \DateTime
     */
    private $updated_at;

    /**
     * @OA\Property()
     * @var boolean
     */
    private $published;

    /**
     * @OA\Property(),
     * @var string
     */
    private $structure_name;

    /**
     * @OA\Property(),
     * @var string
     */
    private $value;

    /**
     * @OA\Property(),
     * @var integer
     */
    private $position;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'published',
        'structure_name'
    ];

    /**
     * The attributes that are translated.
     *
     * @var array
     */
    protected $translatedAttributes = [];

    /**
     * The used relationships
     * @var array
     */
    public $relationships = [];

    /**
     * The relations to eager load on every query.
     *
     * @var array
     */
    public $with = [];

    /**
     * The dates attributes
     *
     * @var array
     */
    protected $dates = [
        'deleted_at',
        'created_at',
        'updated_at'
    ];
}
