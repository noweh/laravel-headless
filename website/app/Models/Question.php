<?php

namespace App\Models;

use Dimsav\Translatable\Translatable;

/**
 * Class Question
 * @package App\Models
 *
 * @OA\Schema(
 *     description="Question model",
 *     title="Question model",
 *     required={"published", "format", "position", "questionnaire_id",
 *     "question_type_id", "good_answer_id", "title", "description"},
 *     @OA\Xml(
 *         name="Question"
 *     ),
 * )
 */
class Question extends AbstractModel
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
     *     enum={"text", "image", "video"},
     * ),
     * @var string
     */
    private $format;

    /**
     * @OA\Property(),
     * @var integer
     */
    private $duration_min;

    /**
     * @OA\Property(),
     * @var integer
     */
    private $duration_max;

    /**
     * @OA\Property(
     *     description="Question Questionnaires",
     *     title="Question Questionnaires",
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
     * @OA\Property(),
     * @var integer
     */
    private $question_type_id;

    /**
     * @OA\Property(),
     * @var integer
     */
    private $good_answer_id;

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
        'duration_min',
        'duration_max',
        'position',
        'question_type_id',
        'good_answer_id'
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

    public function questionnaires()
    {
        return $this->belongsToMany(Questionnaire::class)->orderBy('position', 'asc');
    }

    public function type()
    {
        return $this->belongsTo(QuestionType::class, 'question_type_id');
    }

    public function possibleAnswers()
    {
        return $this->hasMany(PossibleAnswer::class)->orderBy('position', 'asc');
    }

    public function availablePossibleAnswers()
    {
        return $this->hasMany(PossibleAnswer::class)->orderBy('position', 'asc')
            ->published()->withActiveTranslations();
    }

    public function goodAnswer()
    {
        return $this->hasOne(PossibleAnswer::class, 'id', 'good_answer_id');
    }
}
