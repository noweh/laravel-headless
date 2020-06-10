<?php

namespace App\Models\Traits;

use App\Models\Observers\SlugObserver;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use App;

trait SlugModelTrait
{
    /**
     * Currently only used on create and update.
     * it automate a call to setSlugs on model creation.
     */
    protected static function bootSlugModelTrait()
    {
        static::observe(new SlugObserver);
    }

    /**
     * converting those call to setSlugs  would be fore the best.
     */
    public function setSlugs()
    {
        if (method_exists($this, 'getSlugsParams')) {
            foreach ($this->getSlugsParams() as $slugParams) {
                $this->setSlug($slugParams);
            }
        }
    }

    public function setSlug($slugParams)
    {
        if ($slugParams) {
            //we cannot reuse a slug with active == false.
            unset($slugParams['active']);
            $slugParams['slug'] = sluggify($slugParams['slug']);

            $slug = $this->slugs()->firstOrNew($slugParams);
            $slug->active = true;

            foreach ($slugParams as $key => $value) {
                $slug->$key = $value;
            }

            if (!$slug->exists) {
                $slug->slug = $this->makeSlugUnique($slugParams);
                $slug->save();
            } else {
                if (!$slug->slug) {
                    $slug->slug = $this->makeSlugUnique($slugParams);
                    $slug->update(['slug' => $slug->slug]);
                }
                $this->slugs()->where($slugParams)
                    ->where($slug->getKeyName(), '<>', $slug->{$slug->getKeyName()})->update(['active' => 0]);
                $slug->update(['active' => 1]);
            }

            $this->slugs()->where('locale', $slugParams['locale'])
                ->where($slug->getKeyName(), '<>', $slug->{$slug->getKeyName()})->update(['active' => 0]);
        }
    }

    /**
     * return a collection of all 'slug' field value for the Model instance matching the
     * locale specified by $locale or the app current locale.
     *
     * @param string $locale
     * @return Collection
     */
    public function getSlugs($locale = null)
    {
        return $this->slugs()
            ->where('locale', '=', $locale ?: App::getLocale())
            ->get();
    }

    /**
     * return an active slug string for the specified locale or the app current locale
     * if no match is found, return an empty string
     *
     * @param  string $locale [description]
     * @return string
     */
    public function getSlug($locale = null)
    {
        $aSlug = $this->getSlugObject($locale ?: App::getLocale());

        return $aSlug ? $aSlug->slug : '';
    }

    /**
     * return an active slug Model instance matching the
     * locale specified by $locale or the app current locale.
     *
     * @param  string $locale
     * @return Model|Builder|null
     */
    public function getSlugObject($locale = null)
    {
        return $this->slugs()
            ->where('locale', '=', $locale ?: App::getLocale())
            ->where('active', 1)
            ->first()
        ;
    }

    /**
     * return a slug ensuring it is unique if necessary by postfixing a digit or failing that an id.
     * Also alter the $slugParam if altering the slug was needed to make it unique.
     *
     * @param array $slugParams
     * @return string
     */
    private function makeSlugUnique($slugParams)
    {
        $slugBackup = $slugParams['slug'];

        if (!$slugBackup) {
            $slugParams['slug'] = mt_rand();
        }

        $i = 0;
        while ($this->slugs()->getRelated()->where($slugParams)->first() != null) {
            ++$i;
            if ($slugBackup) {
                $slugParams['slug'] = $slugBackup . (($i>=10) ? "-" . mt_rand() : "-{$i}");
            } else {
                $slugParams['slug'] = mt_rand();
            }
        }

        return $slugParams['slug'];
    }

    /**
     * return slugs for locale
     *
     * @param $locale
     * @return array|null
     */
    public function getSlugParams($locale)
    {
        foreach ($this->getSlugsParams() as $slugsParam) {
            if ($slugsParam['locale'] == $locale) {
                return $slugsParam;
            }
        }

        return null;
    }
}
