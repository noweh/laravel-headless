<?php

namespace App\Models;

use Config;
use Str;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Show
 * @package App\Models
 *
 * @OA\Schema(
 *     description="Show model",
 *     title="Show model",
 *     required={"published"},
 *     @OA\Xml(
 *         name="Show"
 *     ),
 * )
 */
class Show extends AbstractModel
{
    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'show_id';

    public const TYPES = [
        'REPLAY' => 'replay',
        'LIVE' => 'live'
    ];

    /**
     * @OA\Property(),
     * @var string
     */
    private $show_id;

    /**
     * @OA\Property(),
     * @var string
     */
    private $slug;

    /**
     * @OA\Property()
     * @var boolean
     */
    private $published;

    /**
     * @OA\Property()
     * @var string
     */
    private $title;

    /**
     * @OA\Property(
     *     enum={"replay", "live"},
     * ),
     * @var string
     */
    private $type;

    /**
     * @OA\Property()
     * @var string
     */
    private $source_desktop_url;

    /**
     * @OA\Property()
     * @var string
     */
    private $source_mobile_url;

    /**
     * @OA\Property()
     * @var string
     */
    private $thumbnail_desktop_url;

    /**
     * @OA\Property()
     * @var string
     */
    private $thumbnail_mobile_url;

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
        'published',
        'title',
        'type',
        'source_desktop_url',
        'source_mobile_url',
        'thumbnail_desktop_url',
        'thumbnail_mobile_url',
        'client_id',
        'player_id'
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

    /**
     * The attributes used for slug
     *
     * @var array
     */
    public $slugAttributes = [
        'title',
    ];

    public function slugs()
    {
        return $this->hasMany(ShowSlug::class, 'show_id');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function (Model $model) {
            $model->setAttribute($model->getKeyName(), (string)Str::uuid());
        });
    }

    public function getIncrementing()
    {
        return false;
    }

    public function getKeyType()
    {
        return 'string';
    }

    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id', 'client_id');
    }

    public function getSlugsParams()
    {
        $slugs = [];
        $slugs[] = [
            'active' => true,
            'slug' => $this->getAttribute('title'),
            'locale' => Config::get('app.locale'),
        ];
        return $slugs;
    }
}
