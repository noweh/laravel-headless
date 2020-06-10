<?php

namespace App\Http\Resources;

class ShowResource extends AbstractResource
{
    /**
     * Override method to add some data to resource
     * @return array
     */
    public function addSpecificData()
    {
        return [
            'client' => $this->when($this->relationLoaded('client'), function () {
                return ClientResource::make($this->client);
            })
        ];
    }
}
