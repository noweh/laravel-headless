<?php

namespace App\Http\Resources;

use App;
use Cache;
use Carbon\Carbon;
use Config;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Database\Eloquent\Collection;

abstract class AbstractResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        if (request('mode') != 'contribution') {
            $commonData = Cache::remember(
                'resources:commondata:type-' . get_class($this->resource) . '.id-' . $this->resource->{$item->getKeyName()},
                Carbon::now()->addMinutes(Config::get('cache.cache_control_maxage.small')),
                function () {
                    return $this->retrieveCommonData();
                }
            );

            $localeData = Cache::remember(
                'resources:localedata:type-' . get_class($this->resource) .
                '.id-' . $this->resource->{$item->getKeyName()} . '.locale-' . App::getLocale(),
                Carbon::now()->addMinutes(Config::get('cache.cache_control_maxage.small')),
                function () {
                    return $this->retrieveLocaleData();
                }
            );
        } else {
            $commonData = $this->retrieveCommonData();
            $localeData = $this->retrieveLocaleData();
        }

        return array_merge($commonData + $localeData, $this->addSpecificData());
    }

    /**
     * Retrieve all common data
     * @return array
     */
    private function retrieveCommonData()
    {
        if (is_null($this->resource)) {
            return [];
        }

        $data = is_array($this->resource)
            ? $this->resource
            : $this->resource->getAttributes();

        foreach ($data as $key => $value) {
            if (in_array($key, ['published']) ||
                preg_match('/^is_/i', $key) ||
                preg_match('/^has_/i', $key) ||
                preg_match('/^accept_/i', $key)
            ) {
                $data[$key] = (boolean) $value;
            }

            if (preg_match('/_at$/i', $key)) {
                if (preg_match('/^[0-9]{4}\-[0-9]{2}\-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}$/', $value)) {
                    $data[$key] = date(DATE_ISO8601, strtotime($value));
                }
            }
        }
        unset($data['password']);
        unset($data['translations']);

        if (request('mode') == 'contribution') {
            if ($this->resource->relationships) {
                $data['relationships'] = [];
                foreach ($this->resource->relationships as $relationship) {
                    $relationshipContent = $this->resource->$relationship()->get();
                    $data['relationships'][$relationship] = [];
                    if (get_class($relationshipContent) == Collection::class) {
                        foreach ($relationshipContent as $item) {
                            $object = new \stdClass();
                            $object->{$item->getKeyName()} = $item->{$item->getKeyName()};
                            if ($item->pivot && $item->pivot->position) {
                                $object->position = $item->pivot->position;
                            }

                            $data['relationships'][$relationship][] = $object;
                        }
                    }
                }
            }
        }

        return $data;
    }

    /**
     * Retrieve all translated data
     * @return array
     */
    private function retrieveLocaleData()
    {
        if (method_exists($this->resource, 'getTranslation') && !empty($this->resource->getTranslatedAttributes()) && $this->resource->getTranslation()) {
            if (request('mode') == 'contribution') {
                $translatedData = [];

                foreach ($this->resource->getTranslationsArray() as $locale => $localeTranslations) {
                    foreach ($localeTranslations as $field => $value) {
                        $translatedData[$field . '_' . $locale] = ($field == 'active') ? (boolean) $value : $value;
                    }
                }
                return $translatedData;
            } else {
                $translatedData = [];

                foreach ($this->resource->getTranslation()->toArray() as $field => $value) {
                    if (in_array($field, $this->resource->getTranslatedAttributes())) {
                        $translatedData[$field] = ($field == 'active') ? (boolean) $value : $value;
                    }
                }
                return $translatedData;
            }
        } else {
            return [];
        }
    }

    /**
     * Override this to add spectific data in resource
     * @return array
     */
    protected function addSpecificData()
    {
        return [];
    }

    /**
     * Set media calls in cache
     * @return MediaLibraryResource|mixed
     */
    protected function retrieveMediaLibraryData()
    {
        if (request('mode') != 'contribution') {
            return Cache::remember(
                'resources:medialibrary:type-' . get_class($this->resource) . '.id-' . $this->resource->{$item->getKeyName()},
                Carbon::now()->addMinutes(Config::get('cache.cache_control_maxage.small')),
                function () {
                    return MediaLibraryResource::make($this->mediasWithAttributes);
                }
            );
        } else {
            return MediaLibraryResource::make($this->mediasWithAttributes);
        }
    }
}
