<?php

namespace App\Models;

use Astrotomic\Translatable\Translatable;

/**
 * Class MediaLibrary
 * @package App\Models
 *
 * @OA\Schema(
 *     description="MediaLibrary model",
 *     title="MediaLibrary model",
 *     required={"url", "width", "height", "public_id", "format", "title", "artist", "legend"},
 *     @OA\Xml(
 *         name="MediaLibrary"
 *     ),
 * )
 */
class MediaLibrary extends AbstractModel
{
    /**
     * @OA\Property(),
     * @var integer
     */
    private $id;

    /**
     * @OA\Property(),
     * @var string
     */
    private $internal_title;
    
    /**
     * @OA\Property(),
     * @var string
     */
    private $slug;

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
     * @var string
     */
    private $url;

    /**
     * @OA\Property(),
     * @var integer
     */
    private $width;

    /**
     * @OA\Property(),
     * @var integer
     */
    private $height;

    /**
     * @OA\Property(),
     * @var string
     */
    private $public_id;

    /**
     * @OA\Property(),
     * @var string
     */
    private $artist;

    /**
     * @OA\Property(),
     * @var string
     */
    private $legend;

    /**
     * @OA\Property(),
     * @var integer
     */
    private $format;

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

    public static $formats = [
        0 => 'Image',
        1 => 'PDF',
    ];

    public static function formatId($name)
    {
        return array_search($name, self::$formats);
    }

    use Translatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'updated_at',
        'internal_title',
        'url',
        'width',
        'height',
        'public_id',
        'artist',
        'format'
    ];

    public $pivotAttributes = [];

    /**
     * The attributes that are translated.
     *
     * @var array
     */
    protected $translatedAttributes = [
        'active',
        'title',
        'description',
        'legend'
    ];

    /**
     * The attributes used for slug
     *
     * @var array
     */
    public $slugAttributes = [
        'title'
    ];

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
        'updated_at'
    ];

    public function slugs()
    {
        return $this->hasMany(MediaLibrarySlug::class);
    }

    public function getSlugsParams()
    {
        $slugs = [];
        foreach ($this->translations as $translation) {
            $slugs[] = [
                'active' => $translation->active,
                'slug' => $translation->title,
                'locale' => $translation->locale,
            ];
        }
        return $slugs;
    }
}
