<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\FarmerProfile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Illuminate\Support\Facades\Storage;

class FarmerController extends Controller
{
    /**
     * Accept a farmer application
     */
    public function accept(Request $request, User $farmer): RedirectResponse
    {
        if ($farmer->role !== 'farmer') {
            return back()->with('error', 'Invalid farmer.');
        }

        $farmer->update(['status' => 'active']);

        return back()->with('status', 'Farmer application accepted successfully.');
    }

    /**
     * Reject a farmer application
     */
    public function reject(Request $request, User $farmer): RedirectResponse
    {
        if ($farmer->role !== 'farmer') {
            return back()->with('error', 'Invalid farmer.');
        }

        $farmer->update(['status' => 'rejected']);

        return back()->with('status', 'Farmer application rejected.');
    }

    /**
     * Delete a farmer account
     */
    public function delete(Request $request, User $farmer): RedirectResponse
    {
        if ($farmer->role !== 'farmer') {
            return back()->with('error', 'Invalid farmer.');
        }

        // Delete farmer profile first
        FarmerProfile::where('user_id', $farmer->id)->delete();

        // Delete farmer user
        $farmer->delete();

        return back()->with('status', 'Farmer account deleted successfully.');
    }

    /**
     * Download farmer business permit (admin only)
     */
    public function downloadPermit(User $farmer)
    {
        if ($farmer->role !== 'farmer') {
            abort(404, 'Farmer not found');
        }

        $farmerProfile = FarmerProfile::where('user_id', $farmer->id)->firstOrFail();
        
        if (!$farmerProfile->business_permit_url) {
            abort(404, 'Permit not found');
        }

        $fullPath = Storage::disk('public')->path($farmerProfile->business_permit_url);
        
        if (!file_exists($fullPath)) {
            abort(404, 'File not found');
        }

        return response()->file($fullPath);
    }
}