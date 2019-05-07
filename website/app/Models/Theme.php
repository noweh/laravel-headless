<?php

namespace App\Models;

use Dimsav\Translatable\Translatable;

/**
 * Class Theme
 * @package App\Models
 *
 * @OA\Schema(
 *     description="Theme model",
 *     title="Theme model",
 *     required={"published", "code", "label"},
 *     @OA\Xml(
 *         name="Theme"
 *     ),
 * )
 */
class Theme extends AbstractModel
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
     * @var integer
     */
    private $code;

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
    private $label;

    use Translatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'published',
        'code'
    ];

    /**
     * The attributes that are translated.
     *
     * @var array
     */
    protected $translatedAttributes = [
        'active',
        'label'
    ];

    public function modules()
    {
        return $this->belongsToMany(Module::class)->orderBy('position', 'asc');
    }

    public function questionnaires()
    {
        return $this->belongsToMany(Questionnaire::class)->orderBy('position', 'asc');
    }

    public function courses()
    {
        return $this->belongsToMany(Course::class)->orderBy('position', 'asc');
    }
}
