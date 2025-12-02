<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreFarmerApplicationRequest;
use App\Http\Requests\ApproveFarmerRequest;
use App\Models\User;
use App\Models\FarmerProfile;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class FarmerApplicationController extends Controller
{   
    use AuthorizesRequests;
    /**
     * Get all pending farmer applications (Admin only).
     */
    public function index(): JsonResponse
    {
        $this->authorize('viewAnyFarmers', User::class);

        $farmers = User::where('role', 'farmer')
            ->with('farmerProfile')
            ->latest()
            ->paginate(15);

        return response()->json($farmers);
    }

    /**
     * Create a new farmer application.
     */
    public function store(StoreFarmerApplicationRequest $request): JsonResponse
    {
        $validated = $request->validated();

        // Create user with farmer role and pending status
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'farmer',
            'status' => 'pending',
        ]);

        // Create farmer profile
        FarmerProfile::create([
            'user_id' => $user->id,
            'business_name' => $validated['business_name'],
            'business_permit_url' => $validated['business_permit_url'],
            'location' => $validated['location'],
        ]);

        return response()->json([
            'message' => 'Farmer application submitted successfully. Awaiting admin approval.',
            'user' => $user->load('farmerProfile'),
        ], 201);
    }

    /**
     * Get a specific farmer's details.
     */
    public function show(User $farmer): JsonResponse
    {
        $this->authorize('viewFarmer', $farmer);

        return response()->json($farmer->load('farmerProfile', 'products'));
    }

    /**
     * Approve or reject a farmer application (Admin only).
     */
    public function approveFarmer(User $farmer, ApproveFarmerRequest $request): JsonResponse
    {
        $this->authorize('approveFarmer', User::class);

        if ($farmer->role !== 'farmer') {
            return response()->json([
                'message' => 'This user is not a farmer.',
            ], 400);
        }

        $validated = $request->validated();
        $farmer->update(['status' => $validated['status']]);

        return response()->json([
            'message' => "Farmer {$validated['status']} successfully.",
            'user' => $farmer->load('farmerProfile'),
        ]);
    }

    /**
     * Get pending farmer applications (Admin only).
     */
    public function getPendingApplications(): JsonResponse
    {
        $this->authorize('viewAnyFarmers', User::class);

        $pendingFarmers = User::where('role', 'farmer')
            ->where('status', 'pending')
            ->with('farmerProfile')
            ->latest()
            ->paginate(15);

        return response()->json($pendingFarmers);
    }
}
