<?php

namespace App\Models;

/**
 * Class UserQuestionnaire
 * @package App\Models
 *
 * @OA\Schema(
 *     description="UserQuestionnaire model",
 *     title="UserQuestionnaire model",
 *     required={"user_id", "questionnaire_id", "note"},
 *     @OA\Xml(
 *         name="UserQuestionnaire"
 *     ),
 * )
 */
class UserQuestionnaire extends AbstractModel
{
    /**
     * @OA\Property(),
     * @var integer
     */
    private $id;

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
     * @OA\Property(),
     * @var integer
     */
    private $user_id;

    /**
     * @OA\Property(),
     * @var integer
     */
    private $questionnaire_id;

    /**
     * @OA\Property(),
     * @var float
     */
    private $note;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'questionnaire_id',
        'note'
    ];

    public function questionnaires()
    {
        return $this->belongsToMany(Questionnaire::class);
    }
}
