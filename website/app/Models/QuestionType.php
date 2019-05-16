<?php

namespace App\Models;

use Dimsav\Translatable\Translatable;

/**
 * Class QuestionType
 * @package App\Models
 *
 * @OA\Schema(
 *     description="QuestionType model",
 *     title="QuestionType model",
 *     required={"published", "code", "label"},
 *     @OA\Xml(
 *         name="QuestionType"
 *     ),
 * )
 */
class QuestionType extends AbstractModel
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
     *     description="QuestionTypes Questions",
     *     title="QuestionTypes Questions",
     *     additionalItems=true,
     *     @OA\Xml(
     *         name="questions",
     *         wrapped=true
     *     ),
     * )
     *
     * @var Question[]
     */
    private $questions;

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
    public $translatedAttributes = [
        'active',
        'label'
    ];

    public function questions()
    {
        return $this->hasMany(Question::class);
    }
}
