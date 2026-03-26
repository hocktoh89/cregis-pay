<?php

namespace App\Http\Controllers;

use App\Services\CregisService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CregisController extends Controller
{
    protected $cregisService;

    public function __construct(CregisService $cregisService)
    {
        $this->cregisService = $cregisService;
    }

    /**
     * Get supported coins for the project
     * GET /api/cregis/coins
     */
    public function getCoins()
    {
        try {
            Log::info('-----    Cregis     getCoins');            
            $result = $this->cregisService->getCoins();
            Log::info($result);   
            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to fetch coins',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create a payout
     * POST /api/cregis/payout
     */
    public function createPayout(Request $request)
    {
        $validated = $request->validate([
            'currency' => 'required|string',
            'address' => 'required|string',
            'amount' => 'required|numeric|min:0',
            'third_party_id' => 'required|string',
            'callback_url' => 'nullable|url',
            'remark' => 'nullable|string',
        ]);

        try {
            $result = $this->cregisService->createPayout(
                currency: $validated['currency'],
                address: $validated['address'],
                amount: $validated['amount'],
                thirdPartyId: $validated['third_party_id'],
                callbackUrl: $validated['callback_url'] ?? null,
                remark: $validated['remark'] ?? null
            );

            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to create payout',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Query payout status
     * GET /api/cregis/payout/{thirdPartyId}
     */
    public function queryPayout($thirdPartyId)
    {
        try {
            $result = $this->cregisService->queryPayout($thirdPartyId);
            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to query payout',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
