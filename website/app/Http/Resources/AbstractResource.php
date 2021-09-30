<?php

namespace App\Http\Resources;

use App;
use Cache;
use Carbon\Carbon;
use Config;
use Illuminate\Http\Resources\Json\JsonResource;

abstract class AbstractResource extends JsonResource
{
    /**
     * Check if cache can be activated
     * @return bool
     */
    protected function checkIfCacheCanBeActivated(): bool
    {
        return request()->method() === 'GET' && filter_var(request('removeCache'), FILTER_VALIDATE_BOOLEAN) !== true;
    }

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request): array
    {
        if ($this->checkIfCacheCanBeActivated()) {
            $commonData = Cache::remember(
                'resources:commondata:type-' . get_class($this->resource) . '.id-' . $this->resource->{$this->resource->getKeyName()},
                Carbon::now()->addMinutes(Config::get('cache.cache_control_maxage.small')),
                function () {
                    return $this->retrieveCommonData();
                }
            );

            $localeData = Cache::remember(
                'resources:localedata:type-' . get_class($this->resource) .
                '.id-' . $this->resource->{$this->resource->getKeyName()} . '.locale-' . App::getLocale(),
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
    private function retrieveCommonData(): array
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
                    $data[$key] = date(DATE_ATOM, strtotime($value));
                }
            }
        }

        if (method_exists($this->resource, 'slugs')) {
            $data['slug'] = $this->resource->getSlug();
        }

        // Remove all hidden fields
        foreach ($this->resource->getHidden() as $fieldName) {
            unset($data[$fieldName]);
        }

        unset($data['translations']);

        return $data;
    }

    /**
     * Retrieve all translated data
     * @return array
     */
    private function retrieveLocaleData(): array
    {
        if (method_exists($this->resource, 'getTranslation') && !empty($this->resource->getTranslatedAttributes()) && $this->resource->getTranslation()) {
            $translatedData = [];

            foreach ($this->resource->getTranslation()->toArray() as $field => $value) {
                if (in_array($field, $this->resource->getTranslatedAttributes())) {
                    $translatedData[$field] = ($field == 'active') ? (boolean) $value : $value;
                }
            }

            if (method_exists($this->resource, 'slugs')) {
                $translatedData['slug'] = $this->resource->getSlug();
            }

            return $translatedData;
        } else {
            return [];
        }
    }

    /**
     * Override this to add spectific data in resource
     * @return array
     */
    protected function addSpecificData(): array
    {
        return [];
    }
}
