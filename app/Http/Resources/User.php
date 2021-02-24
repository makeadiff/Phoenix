<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class User extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        // return parent::toArray($request);
        return ['data' => $this->collection];
    }

    public function with($request)
    {
        return [
            'ver'   => '1.0.0',
            'document_author'   => 'Make A Difference'
        ];
    }
}
