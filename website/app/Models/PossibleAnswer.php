<?php

namespace App\Models;

use Dimsav\Translatable\Translatable;

/**
 * Class PossibleAnswer
 * @package App\Models
 *
 * @OA\Schema(
 *     description="PossibleAnswer model",
 *     title="PossibleAnswer model",
 *     required={"published", "format", "position", "question_id", "text"},
 *     @OA\Xml(
 *         name="PossibleAnswer"
 *     ),
 * )
 */
class PossibleAnswer extends AbstractModel
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
    private $question_id;

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
    private $text;

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
        'question_id'
    ];

    /**
     * The attributes that are translated.
     *
     * @var array
     */
    public $translatedAttributes = [
        'active',
        'text',
        'description'
    ];

    public function question()
    {
        return $this->belongsTo(Question::class, 'question_id');
    }
}
