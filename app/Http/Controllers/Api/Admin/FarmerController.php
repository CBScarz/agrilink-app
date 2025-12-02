<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class FarmerController
{   
    use AuthorizesRequests;
    public function index(Request $request)
    {
        $query = User::where('role', 'farmer');

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                    ->orWhere('email', 'like', "%{$request->search}%");
            });
        }

        $farmers = $query->paginate($request->per_page ?? 20);

        return response()->json([
            'data' => $farmers->items(),
            'pagination' => [
                'total' => $farmers->total(),
                'per_page' => $farmers->perPage(),
                'current_page' => $farmers->currentPage(),
                'last_page' => $farmers->lastPage(),
            ],
        ]);
    }

    public function approve(User $farmer)
    {
        $this->authorize('update', $farmer);

        if ($farmer->role !== 'farmer') {
            return response()->json(['message' => 'User is not a farmer'], Response::HTTP_BAD_REQUEST);
        }

        $farmer->update(['status' => 'active']);

        return response()->json([
            'message' => 'Farmer approved successfully',
            'farmer' => $farmer,
        ]);
    }

    public function reject(User $farmer)
    {
        $this->authorize('update', $farmer);

        if ($farmer->role !== 'farmer') {
            return response()->json(['message' => 'User is not a farmer'], Response::HTTP_BAD_REQUEST);
        }

        $farmer->update(['status' => 'rejected']);

        return response()->json([
            'message' => 'Farmer rejected successfully',
            'farmer' => $farmer,
        ]);
    }

    public function delete(User $farmer)
    {
        $this->authorize('delete', $farmer);

        $farmer->delete();

        return response()->json(['message' => 'Farmer deleted successfully']);
    }

    public function downloadPermit(User $farmer)
    {
        $this->authorize('view', $farmer);

        $profile = $farmer->farmerProfile;

        if (!$profile || !$profile->business_permit_url) {
            return response()->json(['message' => 'Permit not found'], Response::HTTP_NOT_FOUND);
        }

        $filePath = storage_path('app/' . $profile->business_permit_url);

        if (!file_exists($filePath)) {
            return response()->json(['message' => 'File not found'], Response::HTTP_NOT_FOUND);
        }

        return response()->download($filePath);
    }

    public function getStats()
    {
        return response()->json([
            'total_farmers' => User::where('role', 'farmer')->count(),
            'pending' => User::where('role', 'farmer')->where('status', 'pending')->count(),
            'active' => User::where('role', 'farmer')->where('status', 'active')->count(),
            'rejected' => User::where('role', 'farmer')->where('status', 'rejected')->count(),
        ]);
    }
}
