<?php

namespace App\Models\Traits;

use App\Models\MediaLibrary;
use Illuminate\Database\Eloquent\Collection;
use Cloudder;

trait MediaLibraryTraitPolymorphic
{
    public $pivotAttributes = [
        'id', 'crop_x', 'crop_y', 'crop_h', 'crop_w', 'ratio', 'position'
    ];

    /**
     * @return mixed
     */
    public function medias()
    {
        return $this->morphToMany(MediaLibrary::class, 'mediable');
    }

    /**
     * @return array
     */
    public function mediasWithPivotArray()
    {
        // Build an array considering the data structure in the creation form
        $elements = $this->mediasWithPivot()->toArray();
        $result   = [];

        foreach ($elements as $element) {
            foreach ($element as $id => $data) {
                $result = array_replace_recursive($result, [$id => $data]);
            }
        }

        return $result;
    }

    /**
     * @return Collection
     */
    public function mediasWithPivot()
    {
        return  $this->medias()
            ->withPivot($this->pivotAttributes)
            ->orderBy('pivot_position', 'asc')
            ->get();
    }

    /**
     * @return Collection
     */
    public function mediasWithAttributes()
    {
        return  $this->medias()->withPivot($this->pivotAttributes)->orderBy('pivot_position', 'asc');
    }

    /**
     * @param $ratio
     * @param array $transformations
     * @return string|null
     */
    public function getImage($ratio, $transformations = [])
    {
        $media = $this->medias()
            ->where('mediables.ratio', '=', $ratio)
            ->withPivot($this->pivotAttributes)
            ->first();

        if ($media) {
            return $this->buildCloudinaryUrl($media, $transformations);
        } else {
            return null;
        }
    }

    /**
     * @param $ratio
     * @param array $transformations
     * @return Collection
     */
    public function getImageCollection($ratio, $transformations = [])
    {
        $medias = $this->medias()
            ->where('mediables.ratio', '=', $ratio)
            ->withPivot($this->pivotAttributes)
            ->get();

        if (!empty($medias)) {
            $images = new Collection();
            foreach ($medias as $media) {
                $images->push($this->buildCloudinaryUrl($media, $transformations));
            }
            return $images;
        } else {
            return null;
        }
    }

    /**
     * @param $media
     * @param $transformations
     * @return string
     */
    public function buildCloudinaryUrl($media, $transformations)
    {
        $cropping_options = [];
        if (isset($media->pivot->crop_x) ||
            isset($media->pivot->crop_y) ||
            isset($media->pivot->crop_w) ||
            isset($media->pivot->crop_h)
        ) {
            $cropping_options = [
                'x'      => $media->pivot->crop_x,
                'width'  => $media->pivot->crop_w,
                'y'      => $media->pivot->crop_y,
                'height' => $media->pivot->crop_h,
                'crop' => 'crop'
            ];
        }

        if ('.gif' == substr(basename($media->url), -4, 4)) {
            $fetch_format = 'mp4';
        } else {
            $fetch_format = 'auto';
        }
        $transformations = ['transformation' => [$cropping_options, $transformations], 'secure' => true];
        $transformations['fetch_format'] = $fetch_format;

        return str_replace(',', '%2c', Cloudder::show($media->public_id, $transformations));
    }
}
