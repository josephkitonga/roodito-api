<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UsersController extends Controller
{
    /**
     * Display a listing of users.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        // If user is authenticated via token, return only their data
        if ($request->has('user')) {
            return response()->json([
                'success' => true,
                'data' => $request->user
            ]);
        }

        // Otherwise return all users (for admin purposes)
        $users = User::all();
        return response()->json([
            'success' => true,
            'data' => $users
        ]);
    }

    /**
     * Get current authenticated user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function me(Request $request)
    {
        return response()->json([
            'success' => true,
            'data' => $request->user
        ]);
    }

    /**
     * Login user with email or phone number.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $validated = $request->validate([
            'identifier' => 'required|string', // Can be email or phone number
            'password' => 'required|string',
        ]);

        // Find user by email or phone number
        $user = User::where('email', $validated['identifier'])
            ->orWhere('phone_number', $validated['identifier'])
            ->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials. User not found.'
            ], 401);
        }

        // Check password (using sha1 as per existing code)
        if (sha1($validated['password']) !== $user->password) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials. Password is incorrect.'
            ], 401);
        }

        // Generate API token
        $apiToken = Str::random(60);

        // Update user with new API token and login status
        $user->update([
            'api_token' => $apiToken,
            'is_logged_in' => true,
            'last_login' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'data' => [
                'user' => $user,
                'api_token' => $apiToken
            ]
        ]);
    }

    /**
     * Logout user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        $token = $request->header('Authorization');

        if ($token) {
            $token = str_replace('Bearer ', '', $token);
            $user = User::where('api_token', $token)->first();

            if ($user) {
                $user->update([
                    'api_token' => null,
                    'is_logged_in' => false,
                    'last_logout' => now(),
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Logout successful'
        ]);
    }

    /**
     * Store a newly created user in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255', // First Name
            'middle_name' => 'nullable|string|max:255', // Middle Name
            'last_name' => 'required|string|max:255', // Last Name
            'username' => 'required|string|max:255|unique:users,username', // Username
            'email' => 'required|email|unique:users,email', // Email
            'phone_number' => 'required|string|max:20', // Student Phone
            'dob' => 'required|string|max:255', // Date of birth
            'parent_phone_number' => 'nullable|string|max:20', // Parent/Guardian Phone
            'parent_email' => 'nullable|email', // Parent Email
            'password' => 'required|string|min:6', // Password
            'password_confirmation' => 'required|same:password', // Confirm Password
        ]);

        // Process student phone number
        $studentPhone = filter_var($validated['phone_number'], FILTER_SANITIZE_NUMBER_INT);
        if (preg_match('/(^(0|254|\+?254)7(\d){8}$)/', $studentPhone)) {
            $phone_str = '254' . substr($studentPhone, -9);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Invalid phone number format. Please use a valid Kenyan phone number.'
            ], 422);
        }

        // Process parent phone number if provided
        $parentPhone = null;
        if (!empty($validated['parent_phone_number'])) {
            $parentPhoneRaw = filter_var($validated['parent_phone_number'], FILTER_SANITIZE_NUMBER_INT);
            if (preg_match('/(^(0|254|\+?254)7(\d){8}$)/', $parentPhoneRaw)) {
                $parentPhone = '254' . substr($parentPhoneRaw, -9);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid parent phone number format. Please use a valid Kenyan phone number.'
                ], 422);
            }
        }

        $user = User::create([
            'name' => $validated['name'],
            'middle_name' => $validated['middle_name'],
            'last_name' => $validated['last_name'],
            'username' => $validated['username'],
            'user_type' => 'de8786ddf7c161',
            'email' => $validated['email'],
            'phone_number' => $phone_str,
            'dob' => $validated['dob'],
            'parent_phone_number' => $parentPhone,
            'parent_email' => $validated['parent_email'],
            'password' => sha1($validated['password']),
        ]);

        return response()->json([
            'success' => true,
            'data' => $user
        ], 201);
    }

    /**
     * Display the specified user.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $user
        ]);
    }

    /**
     * Update the specified user in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        }

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255', // First Name
            'middle_name' => 'sometimes|nullable|string|max:255', // Middle Name
            'last_name' => 'sometimes|string|max:255', // Last Name
            'username' => 'sometimes|string|max:255|unique:users,username,' . $id, // Username
            'email' => 'sometimes|email|unique:users,email,' . $id, // Email
            'phone_number' => 'sometimes|string|max:20', // Student Phone
            'dob' => 'sometimes|string|max:255', // Date of birth
            'parent_phone_number' => 'sometimes|nullable|string|max:20', // Parent/Guardian Phone
            'parent_email' => 'sometimes|nullable|email', // Parent Email
            'password' => 'sometimes|string|min:6', // Password
            'password_confirmation' => 'sometimes|required_with:password|same:password', // Confirm Password
        ]);

        if (isset($validated['password'])) {
            $validated['password'] = sha1($validated['password']);
        }

        // Process student phone number if provided
        if (isset($validated['phone_number'])) {
            $studentPhone = filter_var($validated['phone_number'], FILTER_SANITIZE_NUMBER_INT);
            if (preg_match('/(^(0|254|\+?254)7(\d){8}$)/', $studentPhone)) {
                $validated['phone_number'] = '254' . substr($studentPhone, -9);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid phone number format. Please use a valid Kenyan phone number.'
                ], 422);
            }
        }

        // Process parent phone number if provided
        if (isset($validated['parent_phone_number'])) {
            if (!empty($validated['parent_phone_number'])) {
                $parentPhoneRaw = filter_var($validated['parent_phone_number'], FILTER_SANITIZE_NUMBER_INT);
                if (preg_match('/(^(0|254|\+?254)7(\d){8}$)/', $parentPhoneRaw)) {
                    $validated['parent_phone_number'] = '254' . substr($parentPhoneRaw, -9);
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid parent phone number format. Please use a valid Kenyan phone number.'
                    ], 422);
                }
            } else {
                $validated['parent_phone_number'] = null;
            }
        }

        $user->update($validated);

        return response()->json([
            'success' => true,
            'data' => $user
        ]);
    }

    /**
     * Remove the specified user from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        }

        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'User deleted successfully'
        ]);
    }

    /**
     * Search for users by partial email or phone number.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request)
    {
        $validated = $request->validate([
            'query' => 'required|string|min:2',
        ]);

        $query = $validated['query'];

        // Search for users by partial email or phone number
        $users = User::where('email', 'LIKE', "%{$query}%")
            ->orWhere('phone_number', 'LIKE', "%{$query}%")
            ->select(['id', 'name', 'email', 'phone_number', 'username'])
            ->limit(10)
            ->get();

        if ($users->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No users found with the provided search term.',
                'data' => []
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Users found.',
            'data' => $users
        ]);
    }

    /**
     * Check if a specific email or phone number exists.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkExists(Request $request)
    {
        $validated = $request->validate([
            'identifier' => 'required|string', // Can be email or phone number
        ]);

        $identifier = $validated['identifier'];

        // Check if user exists by exact email or phone number
        $user = User::where('email', $identifier)
            ->orWhere('phone_number', $identifier)
            ->select(['id', 'name', 'email', 'phone_number', 'username'])
            ->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User does not exist.',
                'exists' => false
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'User exists.',
            'exists' => true,
            'data' => $user
        ]);
    }
}
