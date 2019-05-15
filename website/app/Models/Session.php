<?php

namespace App\Models;

use Dimsav\Translatable\Translatable;

/**
 * Class Session
 * @package App\Models
 *
 * @OA\Schema(
 *     description="Session model",
 *     title="Session model",
 *     required={"published", "position", "title"},
 *     @OA\Xml(
 *         name="Session"
 *     ),
 * )
 */
class Session extends AbstractModel
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
        'position'
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

    public function courses()
    {
        return $this->hasMany(Course::class)->orderBy('position', 'asc');
    }

    public function questionnaires()
    {
        return $this->hasMany(Questionnaire::class)->orderBy('position', 'asc');
    }

    public function themes()
    {
        return $this->belongsToMany(Theme::class)->orderBy('position', 'asc');
    }
}
