<?php

namespace App\Models;

use App\Models\Traits\SaveModelOverrideTrait;
use App\Models\Traits\StaticMethodsTrait;
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

    use StaticMethodsTrait;
    use SoftDeletes;
    use Cachable;
    use TranslatableTrait;
    use SaveModelOverrideTrait;

    public function scopePublished($query): void
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
}
