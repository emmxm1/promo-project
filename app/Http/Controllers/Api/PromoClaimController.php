<?php

namespace App\Http\Controllers\Api;

use App\Enums\PromoCodeUsageStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\ClaimPromoCodeRequest;
use App\Models\PromoCode;
use App\Models\PromoCodeUsage;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class PromoClaimController extends Controller
{
    public function claim(ClaimPromoCodeRequest $request): JsonResponse
    {
        /** @var User $player */
        $player = $request->user();
        $code = $request->validated('code');

        $result = DB::transaction(function () use ($player, $code) {
            $player = User::query()->lockForUpdate()->findOrFail($player->id);
            $promoCode = PromoCode::query()->where('code', $code)->lockForUpdate()->first();

            if ($promoCode === null) {
                return ['error' => 'Promo code not found.'];
            }

            if ($player->promoCodeUsages()->where('promo_code_id', $promoCode->id)->exists()) {
                return ['error' => 'Promo code has already been used.'];
            }

            if ($promoCode->expires_at !== null && $promoCode->expires_at->isPast()) {
                PromoCodeUsage::query()->create([
                    'player_id' => $player->id,
                    'promo_code_id' => $promoCode->id,
                    'status' => PromoCodeUsageStatus::Rejected,
                ]);

                return ['error' => 'Promo code has expired.'];
            }

            $player->balance = bcadd((string) $player->balance, (string) $promoCode->bonus_amount, 2);
            $player->save();

            PromoCodeUsage::query()->create([
                'player_id' => $player->id,
                'promo_code_id' => $promoCode->id,
                'status' => PromoCodeUsageStatus::Success,
            ]);

            return [
                'balance' => $player->balance,
                'bonus_amount' => $promoCode->bonus_amount,
            ];
        });

        if (isset($result['error'])) {
            return response()->json(['message' => $result['error']], 422);
        }

        return response()->json([
            'balance' => $result['balance'],
            'bonus_amount' => $result['bonus_amount'],
        ]);
    }
}
