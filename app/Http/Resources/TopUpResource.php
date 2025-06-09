<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TopUpResource extends JsonResource
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
            'amount' => $this->amount,
            'status' => $this->status,
            'proof' => $this->proof ? asset('storage/' . $this->proof) : null,
            'payment_method' => $this->payment_method,
            'created_at' => $this->created_at->toDateTimeString(),
            'user' => $this->whenLoaded('user', function () {
                return [
                    'id' => $this->user->id,
                    'name' => $this->user->name,
                    'email' => $this->user->email,
                    'avatar_url' => $this->user->avatar
                                    ? asset('storage/' . $this->user->avatar)
                                    : asset('/Default-Profile.png'),
                ];
            }),
        ];
    }
}