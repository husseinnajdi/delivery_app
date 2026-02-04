<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
class UserResources extends JsonResource
{
    public function toArray($request)
    {
        return [
            'username' => $this->username,
            'full_name' => $this->full_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'role' => $this->role,
            'status' => $this->status,
            'picture' => $this->picture,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            
        ];
    }
}
