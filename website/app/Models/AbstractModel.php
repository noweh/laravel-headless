<?php

namespace App\Models;

use App\Models\Traits\SlugModelTrait;
use Carbon\Carbon;
use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use App\Models\Traits\TranslatableTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App;

abstract class AbstractModel extends Model
{
    public const STATUSES = [
        'status_canceled' => 'canceled',
        'status_pending' => 'pending',
        'status_finished' => 'finished'
    ];

    /**
     * @OA\Property()
     * @var boolean
     */
    protected $softDelete = true;

    /**
     * @OA\Property(
     *     example="2017-02-02 18:31:45",
     *     format="datetime",
     *     type="string"
     * )
     *
     * @var \DateTime
     */
    protected $deleted_at;

    /**
     * The relationships
     * @var array
     */
    public $relationships = [];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = ['deleted_at'];

	use SoftDeletes;
    use Cachable;
    use TranslatableTrait;
    use SlugModelTrait;

    /**
     * Save the model to the database.
     *
     * @param array $options
     * @return bool
     * @throws \Exception
     */
    public function save(array $options = [])
    {
        foreach ($this->getAttributes() as $key => $value) {
            if (!empty($value)) {
                if (preg_match('/_at$/i', $key)) {
                    if (false === ($ts = strtotime($value))) {
                        $this->setAttribute($key, null);
                    }
                    $this->setAttribute($key, date('Y-m-d H:i:s', $ts));
                }
            }
        }

        return parent::save($options);
    }

    public function scopePublished($query)
    {
        $query->where($this->table . '.published', true);
        if (in_array('publication_started_at', $this->fillable)) {
            $query->where(function ($query) {
                $query
                    ->where('publication_started_at', null)
                    ->orWhere('publication_started_at', '<=', Carbon::now());
            });
        }
        if (in_array('publication_ended_at', $this->fillable)) {
            $query->where(function ($query) {
                $query
                    ->where('publication_ended_at', null)
                    ->orWhere('publication_ended_at', '>=', Carbon::now());
            });
        }
        $query->whereHas('translations', function ($q) {
            $q->where('locale', request('locale') ? request('locale') : App::getLocale());
            $q->where('active', true);
        });
    }

    public static function formatDefinition()
    {
        return [
            'square' => ['aspectRatio' => '1/1'],
            'landscape' => ['aspectRatio' => '16/9'],
            'portrait' => ['aspectRatio' => '9/16'],
            'fiche' => ['aspectRatio' => '4/3'],
            'vertical' => ['aspectRatio' => '4/5'],
            'free' => ['aspectRatio' => null]
        ];
    }

    public static function orientationDefinition()
    {
        return [
            'landscape', 'portrait', 'square'
        ];
    }
}
