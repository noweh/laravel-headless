<?php

namespace App\Models\Traits;

use App;
use Astrotomic\Translatable\Translatable;

trait TranslatableTrait
{
    use Translatable;

    /**
     * The attributes that are translated.
     *
     * @var array
     */
    protected $translatedAttributes = [];

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

    /**
     * @param string|null $locale
     *
     * @return bool
     */
    public function hasActiveTranslation($locale = null)
    {
        $locale = $locale ?: $this->locale();

        foreach ($this->translations as $translation) {
            if ($translation->getAttribute($this->getLocaleKey()) == $locale) {
                return $translation->active;
            }
        }

        return false;
    }

    public function getTranslatedAttributes()
    {
        return $this->translatedAttributes;
    }
}
