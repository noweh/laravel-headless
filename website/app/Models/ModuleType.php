<?php

namespace App\Models;

/**
 * Class ModuleType
 * @package App\Models
 *
 * @OA\Schema(
 *     description="ModuleType model",
 *     title="ModuleType model",
 *     required={"published, 'structure_name"},
 *     @OA\Xml(
 *         name="ModuleType"
 *     ),
 * )
 */
class ModuleType extends AbstractModel
{
    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'module_type_id';

    public const STRUCTURE_INFO = [
        'TYPE_NOTE' => ['name' => 'note'],
        'TYPE_HASHTAG' => ['name' => 'hashtag'],
        'TYPE_LINK' => ['name' => 'link'],
        'TYPE_VIDEO' => ['name' => 'video'],
        'TYPE_AR' => ['name' => 'AR']
    ];

    /**
     * @OA\Property(),
     * @var integer
     */
    private $module_type_id;

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

    public function moduleFields()
    {
        return $this
            ->belongsToMany(
                ModuleField::class,
                'module_type_field_associations',
                'module_type_id',
                'module_field_id'
            )
            ->withTimestamps()
            ->withPivot('position')
            ->orderBy('position');
    }
}
