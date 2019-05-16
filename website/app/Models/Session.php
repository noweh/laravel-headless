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
     *     description="Session Themes",
     *     title="Session Themes",
     *     additionalItems=true,
     *     @OA\Xml(
     *         name="themes",
     *         wrapped=true
     *     ),
     * )
     *
     * @var Theme[]
     */
    private $themes;

    /**
     * @OA\Property(
     *     description="Session Questionnaires",
     *     title="Session Questionnaires",
     *     additionalItems=true,
     *     @OA\Xml(
     *         name="questionnaires",
     *         wrapped=true
     *     ),
     * )
     *
     * @var Questionnaire[]
     */
    private $questionnaires;

    /**
     * @OA\Property(
     *     description="Session Courses",
     *     title="Session Courses",
     *     additionalItems=true,
     *     @OA\Xml(
     *         name="courses",
     *         wrapped=true
     *     ),
     * )
     *
     * @var Course[]
     */
    private $courses;

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
        return $this->belongsToMany(Course::class)->orderBy('position', 'asc')->withPivot(['position']);
    }

    public function questionnaires()
    {
        return $this->belongsToMany(Questionnaire::class)->orderBy('position', 'asc')->withPivot(['position']);
    }

    public function themes()
    {
        return $this->belongsToMany(Theme::class);
    }
}
