<?php

namespace App\Http\Resources;

class ClientResource extends AbstractResource
{
    /**
     * Override method to add some data to resource
     * @return array
     */
    public function addSpecificData()
    {
        return [
            'adminUsers' => $this->when($this->relationLoaded('adminUsers'), function () {
                return AdminUserResource::collection($this->adminUsers);
            }),
            'shows' => $this->when($this->relationLoaded('shows'), function () {
                return ShowResource::collection($this->shows);
            })
        ];
    }
}
