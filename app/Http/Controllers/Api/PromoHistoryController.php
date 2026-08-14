<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PromoHistoryRequest;
use App\Http\Resources\PromoCodeUsageResource;
use App\Models\User;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PromoHistoryController extends Controller
{
    public function index(PromoHistoryRequest $request): AnonymousResourceCollection
    {
        /** @var User $player */
        $player = $request->user();

        $usages = $player->promoCodeUsages()
            ->with('promoCode')
            ->when(
                $request->validated('status'),
                fn ($query, string $status) => $query->where('status', $status),
            )
            ->latest('created_at')
            ->paginate();

        return PromoCodeUsageResource::collection($usages);
    }
}
