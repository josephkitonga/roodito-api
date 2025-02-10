<?php

namespace App\Http\Controllers;

use App\Models\SafBundle;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Config;

class SafBundleController extends Controller
{
    private $safProdToRooditoPackage = [
        'roodito0001' => ['name' => 'SAF_PACKAGE_ID', 'account' => 3, 'amount' => 20],
        'roodito0002' => ['name' => 'SAF_PACKAGE_ID', 'account' => 3, 'amount' => 20],
        'roodito0004' => ['name' => 'SAF_PACKAGE_ID_10', 'account' => 4, 'amount' => 10],
    ];

    public function __construct()
    {
        config(['app.timezone' => 'Africa/Nairobi']);
    }

    public function index(Request $request)
    {
        // Log incoming request
        $apiLog = $this->logRequest($request);

        if ($request->method() !== 'POST') {
            return $this->errorResponse('Invalid Request Method: ' . $request->method(), $apiLog);
        }

        try {
            // Validate incoming request
            $validator = Validator::make($request->all(), [
                'id' => 'required',
                'desc' => 'required',
                'status' => 'required',
                'created.value' => 'required',
                'relatedSusbscription' => 'required|array',
            ]);

            if ($validator->fails()) {
                return $this->errorResponse('Invalid request data: ' . $validator->errors()->first(), $apiLog);
            }

            // Extract subscription details
            $subscriptionDetails = $this->extractSubscriptionDetails($request);
            if (!$subscriptionDetails) {
                return $this->errorResponse('Invalid subscription details', $apiLog);
            }

            // Check if products are allowed
            $allowedProducts = explode(',', config('settings.saf_allowed_products', implode(',', array_keys($this->safProdToRooditoPackage))));
            if (!in_array($subscriptionDetails['productId'], $allowedProducts)) {
                return $this->errorResponse('Invalid Product ID: ' . $subscriptionDetails['productId'], $apiLog);
            }

            // Validate transaction reference
            if (!$this->isValidTransactionReference($subscriptionDetails['requestId'], $subscriptionDetails['tranRef'])) {
                return $this->errorResponse('Invalid transaction reference or duplicate request', $apiLog);
            }

            // Process the bundle
            return $this->processBundle($subscriptionDetails, $request->all(), $apiLog);

        } catch (\Exception $e) {
            Log::error('SafBundle processing error: ' . $e->getMessage());
            return $this->errorResponse('Server Error: ' . $e->getMessage(), $apiLog);
        }
    }

    private function extractSubscriptionDetails(Request $request): ?array
    {
        $subscriptions = collect($request->relatedSusbscription);
        
        try {
            $msisdn = '254' . substr($this->getSubscriptionValue($subscriptions, 'MSISDN'), -9);
            
            return [
                'safid' => $request->id,
                'safDesc' => $request->desc,
                'safStatus' => $request->status,
                'safCrValue' => $request->created['value'],
                'msisdn' => $msisdn,
                'requestId' => $this->getSubscriptionValue($subscriptions, 'REQUESTID'),
                'productName' => $this->getSubscriptionValue($subscriptions, 'PRODUCTNAME'),
                'productId' => $this->getSubscriptionValue($subscriptions, 'PRODUCTID'),
                'subscriptionDate' => $this->getSubscriptionValue($subscriptions, 'SUBSCRIPTIONDATE'),
                'userID' => $this->getSubscriptionValue($subscriptions, 'UserID'),
                'tranRef' => $this->getSubscriptionValue($subscriptions, 'TransactionReference'),
                'userPhone' => $this->getSubscriptionValue($subscriptions, 'UserPhone'),
            ];
        } catch (\Exception $e) {
            Log::error('Error extracting subscription details: ' . $e->getMessage());
            return null;
        }
    }

    private function getSubscriptionValue($subscriptions, $name)
    {
        return $subscriptions->firstWhere('name', $name)['desc'] ?? null;
    }

    private function isValidTransactionReference($requestId, $tranRef): bool
    {
        if (!$requestId || !$tranRef) {
            return false;
        }

        // Check if either requestId or tranRef already exists in the database
        $existingTransaction = DB::table('saf_bundles')
            ->where('saf_request_id', $requestId)
            ->orWhere('saf_tran_ref', $tranRef)
            ->exists();

        // Return true if no existing transaction is found
        return !$existingTransaction;
    }

    private function processBundle(array $details, array $rawRequest, array $apiLog)
    {
        $expiryDate = $details['safStatus'] == '1000' 
            ? Carbon::now()->addHours(24)
            : Carbon::now();

        $productConfig = $this->safProdToRooditoPackage[$details['productId']] ?? null;
        $productAmount = $productConfig['amount'] ?? 0.00;

        // Create bundle record
        $bundle = SafBundle::create([
            'saf_ref_id' => $details['safid'],
            'saf_desc' => $details['safDesc'],
            'saf_status' => $details['safStatus'],
            'saf_created_value' => $details['safCrValue'],
            'msisdn' => $details['msisdn'],
            'product_name' => $details['productName'],
            'product_id' => $details['productId'],
            'subscription_date' => $details['subscriptionDate'],
            'txn_date' => Carbon::now(),
            'txn_status' => $details['safStatus'] != '1000',
            'amount' => $productAmount,
            'payload' => $rawRequest,
            'expiry_date' => $expiryDate,
            'saf_request_id' => $details['requestId'],
            'saf_tran_ref' => $details['tranRef']
        ]);

        if ($details['safStatus'] == '1000') {
            // Handle successful transaction
            return $this->handleSuccessfulTransaction($bundle, $details, $productConfig, $apiLog);
        }

        return $this->errorResponse('Failed ' . $details['safDesc'], $apiLog);
    }

    private function handleSuccessfulTransaction(SafBundle $bundle, array $details, ?array $productConfig, array $apiLog)
    {
        // Expire any active bundles
        SafBundle::where('msisdn', $details['msisdn'])
            ->where('id', '!=', $bundle->id)
            ->where('txn_status', 0)
            ->update([
                'expiry_date' => Carbon::now(),
                'txn_status' => 1
            ]);

        // Get or create user
        $user = User::firstOrCreate(
            ['phone_number' => $details['msisdn']],
            [
                'user_id' => time(),
                'username' => $details['msisdn'],
                'password' => sha1('123456'), // You should use proper password hashing in production
                'price_package_id' => $productConfig['name'] ?? 1600047888000,
                'user_type' => 'de8786ddf7c161',
                'activation_status' => 1,
                'state' => 1
            ]
        );

        if ($user->wasRecentlyCreated) {
            $message = 'Welcome and proceed to roodito.com/student-register to register and start revising. Expiry ' . $bundle->expiry_date;
        } else {
            // Update existing user package
            $user->update([
                'price_package_id' => $productConfig['name'] ?? 1600047888000,
                'account_type' => $productConfig['account'] ?? 3,
            ]);
            $message = $user->name . ', your subscription has been renewed for 24hrs. Visit roodito.com, Expiry: ' . $bundle->expiry_date;
        }

        // TODO: Implement SMS sending
        // $this->sendSms($details['msisdn'], $message);

        $apiLog['description'] = "Safaricom Bundle success ID: {$bundle->id}, MSISDN: {$details['msisdn']}";
        Log::info($apiLog['description']);

        return response()->json([
            'refId' => $bundle->id,
            'expiryDate' => $bundle->expiry_date,
            'status' => true,
            'msisdn' => $details['msisdn']
        ]);
    }

    private function logRequest(Request $request): array
    {
        $log = [
            'request_type' => $request->method(),
            'endpoint' => $request->fullUrl(),
            'request_body' => $request->all() ? json_encode($request->all()) : '',
            'response_body' => '',
            'created_at' => Carbon::now(),
            'state' => 0,
            'source' => 'ROODITO_SAF_BUNDLE',
            'description' => 'New Request'
        ];

        Log::info('SafBundle Request', $log);

        return $log;
    }

    private function errorResponse(string $message, array $apiLog)
    {
        $apiLog['description'] = $message;
        Log::error($message);

        return response()->json([
            'refId' => uniqid(),
            'expiryDate' => Carbon::now(),
            'status' => false,
            'message' => $message
        ]);
    }
}
