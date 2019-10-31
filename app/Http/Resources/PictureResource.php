<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PictureResource extends JsonResource
{

    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'          => $this->pic_id,
            'title'       => $this->pic_title,
            'description' => $this->pic_description,
            'name'        => $this->pic_filename,
            'url'         => env('APP_URL') . '/api/picture/' . $this->pic_id,
            'source'      => $this->pic_url,
        ];
    }
}
