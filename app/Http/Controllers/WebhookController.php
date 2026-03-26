<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    /**
     * Handle Cregis webhook callbacks
     */
    public function handle(Request $request)
    {
        // Log the incoming webhook for debugging
        Log::info('Cregis webhook received', $request->all());

        // Verify the webhook signature (optional but recommended)
        // if (!$this->verifySignature($request)) {
        //     return response()->json(['error' => 'Invalid signature'], 401);
        // }

        // Process the webhook data
        $data = $request->all();

        // Handle different webhook types based on your needs
        // Example: payout status updates, deposit notifications, etc.

        return response()->json(['status' => 'success'], 200);
    }

    /**
     * Verify webhook signature (implement based on Cregis webhook docs)
     */
    private function verifySignature(Request $request)
    {
        // TODO: Implement signature verification
        // Similar to the signature generation in CregisService
        return true;
    }
}
