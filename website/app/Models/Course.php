<?php

namespace App\Models;

use Dimsav\Translatable\Translatable;

/**
 * Class Course
 * @package App\Models
 *
 * @OA\Schema(
 *     description="Course model",
 *     title="Course model",
 *     required={"published", "format", "position", "module_id", "title", "description"},
 *     @OA\Xml(
 *         name="Course"
 *     ),
 * )
 */
class Course extends AbstractModel
{
    /**
     * @OA\Property(),
     * @var integer
     */
    private $id;

    /**
     * @OA\Property()
     * @var boolean
     */
    private $published;

    /**
     * @OA\Property(),
     * @var string
     */
    private $format;

    /**
     * @OA\Property(),
     * @var integer
     */
    private $module_id;

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
     * @OA\Property(
     *     default=true
     * );
     * @var boolean
     */
    private $active;

    /**
     * @OA\Property(),
     * @var string
     */
    private $title;

    /**
     * @OA\Property(),
     * @var string
     */
    private $description;

    /**
     * @OA\Property(),
     * @var integer
     */
    private $position;

    use Translatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'published',
        'format',
        'position',
        'module_id'
    ];

    /**
     * The attributes that are translated.
     *
     * @var array
     */
    public $translatedAttributes = [
        'active',
        'title',
        'description'
    ];

    public function module()
    {
        return $this->belongsTo(Module::class, 'module_id');
    }

    public function themes()
    {
        return $this->belongsToMany(Theme::class);
    }
}
