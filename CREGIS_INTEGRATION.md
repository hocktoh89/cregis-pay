# Cregis API Integration Guide

## Setup

1. **Get your credentials from Cregis dashboard:**
   - Download Cregis PC Client from https://www.cregis.com/download
   - Register and log in
   - Create a wallet (e.g., "my-wallet")
   - Create a project and get:
     - API Key
     - Gateway Server URL
     - Project NO (Project ID)

2. **Configure your `.env` file:**
```env
CREGIS_API_URL=https://your-gateway-server.com
CREGIS_API_KEY=f502a9ac9ca54327986f29c03b271491
CREGIS_PROJECT_ID=1382528827416576
```

## Usage Examples

### 1. Get Supported Coins

**Endpoint:** `GET /api/cregis/coins`

```bash
curl http://localhost:8000/api/cregis/coins
```

**Response:**
```json
{
  "code": "00000",
  "msg": "ok",
  "data": {
    "payout_coins": [
      {
        "coin_name": "TRON#Shasta",
        "chain_id": "195",
        "token_id": "195"
      }
    ],
    "address_coins": [...]
  }
}
```

### 2. Create a Payout

**Endpoint:** `POST /api/cregis/payout`

```bash
curl -X POST http://localhost:8000/api/cregis/payout \
  -H "Content-Type: application/json" \
  -d '{
    "currency": "195@195",
    "address": "TXsmKpEuW7qWnXzJLGP9eDLvWPR2GRn1FS",
    "amount": "10.5",
    "third_party_id": "payout_123456",
    "callback_url": "http://localhost:8000/api/cregis/callback",
    "remark": "User withdrawal"
  }'
```

### 3. Query Payout Status

**Endpoint:** `GET /api/cregis/payout/{thirdPartyId}`

```bash
curl http://localhost:8000/api/cregis/payout/payout_123456
```

### Using in PHP Code

```php
use App\Services\CregisService;

$cregis = app(CregisService::class);

// Get supported coins
$coins = $cregis->getCoins();

// Create payout
$result = $cregis->createPayout(
    currency: '195@195',  // USDT on TRON
    address: 'TXsmKpEuW7qWnXzJLGP9eDLvWPR2GRn1FS',
    amount: '10.5',
    thirdPartyId: uniqid('payout_'),
    callbackUrl: url('/api/cregis/callback'),
    remark: 'User withdrawal'
);

// Query payout status
$status = $cregis->queryPayout('payout_123456');
```

## Available API Endpoints

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/cregis/coins` | Get supported coins for your project |
| POST | `/api/cregis/payout` | Create a new payout transaction |
| GET | `/api/cregis/payout/{id}` | Query payout status by third_party_id |
| POST | `/api/cregis/callback` | Webhook callback endpoint |

### Handle Webhooks

Webhooks are automatically handled at `/api/cregis/callback`. Check `app/Http/Controllers/WebhookController.php` to customize the logic.

## How the Signature Works

Every request includes these system parameters:
- `timestamp`: Current time in milliseconds
- `nonce`: 6-character random string
- `sign`: MD5 signature

The signature is calculated as:
1. Remove empty values and sort parameters alphabetically
2. Concatenate as `key1value1key2value2...`
3. Prepend API Key
4. Calculate MD5 hash (lowercase)

Example:
```
API Key: f502a9ac9ca54327986f29c03b271491
Parameters: {pid: 123, amount: "1.1", nonce: "abc123"}
Sorted concat: amount1.1nonceabc123pid123
With API Key: f502a9ac9ca54327986f29c03b271491amount1.1nonceabc123pid123
MD5 hash: c9bae061ae3f5f8d3bfde817f6966c36
```

## API Reference

- [Quick Start](https://developer.cregis.com/quickstart)
- [Request Format](https://developer.cregis.com/api-reference/request)
- [Signature Algorithm](https://developer.cregis.com/api-reference/signature)
