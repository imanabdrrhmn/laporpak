<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReportResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id, 
            'user' => $this->whenLoaded('user', function () { 
                return [
                    'id' => $this->user->id, 
                    'name' => $this->user->name,
                    'avatar_url' => $this->user->avatar
                                    ? asset('storage/' . $this->user->avatar)
                                    : asset('/Default-Profile.png'), 
                ];
            }),
            'category' => $this->category,
            'description' => $this->description,
            'status' => $this->status,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'address' => $this->address,         
            'location' => $this->address,        
            'service' => $this->service,
            'source' => $this->source,
            'created_at' => $this->created_at->toDateTimeString(),
            'evidence' => $this->evidence
                            ? asset('storage/' . $this->evidence)
                            : null,
            'flags_count' => $this->whenCounted('flags', $this->flags_count, 0), 
            'reason' => $this->when($this->status === 'unpublished', $this->reason), 
        ];
    }
}