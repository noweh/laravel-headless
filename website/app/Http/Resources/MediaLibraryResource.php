<?php

namespace App\Http\Resources;

use Illuminate\Database\Eloquent\Collection;
use Cache;
use Exception;

class MediaLibraryResource extends AbstractResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return Collection|mixed
     */
    public function toArray($request)
    {
        $items = [];
        if (get_class($this->resource) == Collection::class) {
            foreach ($this->resource as $resource) {
                $items[] = $resource;
            }
        } else {
            $items[] = $this->resource;
        }

        $medias = new Collection();
        foreach ($items as $item) {
            if (!$medias->contains('id', $item->id)) {
                $media = new \stdClass();
                $media->id = $item->id;
                $media->internal_title = $item->internal_title;
                $media->slug = $item->getSlug();
                $media->url = $item->url;
                $media->width = $item->width;
                $media->height = $item->height;
                $media->public_id = $item->public_id;
                $media->artist = $item->artist;
                $media->format = $item->format;
                $media->created_at = date(DATE_ISO8601, strtotime((string) $item->created_at));
                $media->updated_at = date(DATE_ISO8601, strtotime((string) $item->updated_at));

                if (request('mode') == 'contribution') {
                    foreach ($item->getTranslationsArray() as $locale => $localeTranslations) {
                        foreach ($localeTranslations as $field => $value) {
                            $media->{$field . '_' . $locale} = ($field == 'active') ? (boolean) $value : $value;
                        }
                    }
                } else {
                    foreach ($item->getTranslation()->toArray() as $field => $value) {
                        if (in_array($field, $item->getTranslatedAttributes())) {
                            $media->$field = ($field == 'active') ? (boolean) $value : $value;
                        }
                    }
                }

                $this->when($item->relationLoaded('tags'), function () use ($media, $item) {
                    $media->tags = TagResource::collection($item->tags);
                });

                $medias->add($media);
            }

            if ($item->pivot) {
                $media = $medias->filter(function ($media) use ($item) {
                    return $media->id == $item->id;
                })->first();

                if ('.gif' == substr(basename($item->url), -4, 4)) {
                    $resizedVideoMobile = Cache::rememberForever(
                        md5("images.{$item->id}..modes.mobile.ratios.{$item->pivot->ratio}" .
                            ".x{$item->pivot->crop_x}y{$item->pivot->crop_y}" .
                            "w{$item->pivot->crop_w}h{$item->pivot->crop_h}"),
                        function () use ($item) {
                            // If image not in cache, construct the resizedImage
                            $resizedImage = null;
                            try {
                                $resizedImage = $item->buildCloudinaryUrl($item, ['width' => 828]);
                            } catch (Exception $e) {
                            }
                            return $resizedImage;
                        }
                    );

                    $resizedVideoTablet = Cache::rememberForever(
                        md5("images.{$item->id}..modes.tablet.ratios.{$item->pivot->ratio}" .
                            ".x{$item->pivot->crop_x}y{$item->pivot->crop_y}" .
                            "w{$item->pivot->crop_w}h{$item->pivot->crop_h}"),
                        function () use ($item) {
                            // If image not in cache, construct the resizedImage
                            $resizedImage = null;
                            try {
                                $resizedImage = $item->buildCloudinaryUrl($item, ['width' => 1024]);
                            } catch (Exception $e) {
                            }
                            return $resizedImage;
                        }
                    );

                    $media->formats[] = [
                        $item->pivot->ratio => [
                            'crop_x' => $item->pivot->crop_x,
                            'crop_y' => $item->pivot->crop_y,
                            'crop_w' => $item->pivot->crop_w,
                            'crop_h' => $item->pivot->crop_h,
                            'resizedVideoMobile' => $resizedVideoMobile,
                            'resizedVideoTablet' => $resizedVideoTablet,
                        ]
                    ];
                } else {
                    $resizedImage = Cache::rememberForever(
                        md5("images.{$item->id}.ratios.{$item->pivot->ratio}" .
                            ".x{$item->pivot->crop_x}y{$item->pivot->crop_y}" .
                            "w{$item->pivot->crop_w}h{$item->pivot->crop_h}"),
                        function () use ($item) {
                            // If image not in cache, construct the resizedImage
                            $resizedImage = null;
                            try {
                                $resizedImage = $item->buildCloudinaryUrl($item, ['width' => 1024]);
                            } catch (Exception $e) {
                            }
                            return $resizedImage;
                        }
                    );

                    $media->formats[] = [
                        $item->pivot->ratio => [
                            'crop_x' => $item->pivot->crop_x,
                            'crop_y' => $item->pivot->crop_y,
                            'crop_w' => $item->pivot->crop_w,
                            'crop_h' => $item->pivot->crop_h,
                            'resizedImage' => $resizedImage,
                        ]
                    ];
                }

                $media->position = $item->pivot->position;
            } else {
                return $medias->first();
            }
        }

        return $medias;
    }
}
