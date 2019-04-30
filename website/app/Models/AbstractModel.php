<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App;

abstract class AbstractModel extends Model
{
    public function scopePublished($query)
    {
        $query->where($this->table . '.published', 1);
    }

    public function scopePublishedOrPreview($query)
    {
        $query->where($this->table . '.published', 1);
    }

    public function scopeWithActiveTranslations($query, $locale = null)
    {
        if (method_exists($query->getModel(), 'translations')) {
            $locale = $locale==null ? App::getLocale() : $locale;

            $query->whereHas('translations', function ($query) use ($locale) {
                $query->whereActive(true);
                $query->whereLocale($locale);
            });

            $query->with(['translations' => function ($query) use ($locale) {
                $query->whereActive(true);
                $query->whereLocale($locale);
            }]);
        }
    }

    public function getTranslatedAttributes()
    {
        return $this->translatedAttributes;
    }
}
