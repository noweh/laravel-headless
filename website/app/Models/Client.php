<?php

namespace App\Models;

/**
 * Class Client
 * @package App\Models
 *
 * @OA\Schema(
 *     description="Client model",
 *     title="Client model",
 *     required={"name"},
 *     @OA\Xml(
 *         name="Client"
 *     ),
 * )
 */
class Client extends AbstractModel
{
    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'client_id';

    /**
     * @OA\Property(),
     * @var integer
     */
    private $client_id;

    /**
     * @OA\Property()
     * @var boolean
     */
    private $is_activated;

    /**
     * @OA\Property(),
     * @var string
     */
    private $name;

    /**
     * @OA\Property()
     * @var string
     */
    private $logo_url;

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
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'is_activated',
        'name',
        'logo_url'
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

    public function adminUsers()
    {
        return $this->hasMany(AdminUser::class, 'client_id');
    }

    public function shows()
    {
        return $this->hasMany(Show::class, 'client_id');
    }
}
