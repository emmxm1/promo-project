<?php

namespace App\Http\Resources;

use App\Enums\PromoCodeUsageStatus;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\PromoCodeUsage */
class PromoCodeUsageResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'code' => $this->promoCode->code,
            'bonus_amount' => $this->status === PromoCodeUsageStatus::Success
                ? $this->promoCode->bonus_amount
                : null,
            'applied_at' => $this->created_at,
            'status' => $this->status->value,
        ];
    }
}
