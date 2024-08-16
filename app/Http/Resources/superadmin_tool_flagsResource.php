<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class superadmin_tool_flagsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        //return parent::toArray($request);
        return [
            'id'            => $this->id,
            'clientGroupId' =>  $this->clientgroupid,
            'networkid'     =>  $this->networkid,
            'terminal'      =>  $this->terminalno,
            'type'          =>  $this->type,
            'value'         =>  $this->value,
            'vatchange_date'    => $this->vatchange_date
        ];
    }
}
