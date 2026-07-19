<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class DonorController extends Controller
{
    /**
     * Toggle the authenticated donor's availability.
     */
    public function toggleAvailability(Request $request)
    {
        $user = $request->user();
        abort_unless($user->role === 'donor', 403, 'Only donors have availability status.');

        $user->update(['availability' => ! $user->availability]);

        return response()->json(['availability' => $user->availability]);
    }

    /**
     * Find nearby, eligible, blood-group-compatible donors.
     * GET /donors/nearby?blood_group=O+&lat=..&lng=..&radius_km=10
     */
    public function nearby(Request $request)
    {
        $request->validate([
            'blood_group' => ['required', 'in:O+,O-,A+,A-,B+,B-,AB+,AB-'],
            'lat' => ['required', 'numeric'],
            'lng' => ['required', 'numeric'],
            'radius_km' => ['nullable', 'numeric', 'min:1', 'max:100'],
        ]);

        $lat = $request->float('lat');
        $lng = $request->float('lng');
        $radiusKm = $request->float('radius_km', 10);
        $compatibleGroups = User::compatibleDonorGroups($request->string('blood_group'));

        $donors = User::query()
            ->donors()
            ->available()
            ->eligible()
            ->whereIn('blood_group', $compatibleGroups)
            ->selectRaw(
                '*, (6371 * acos(cos(radians(?)) * cos(radians(latitude)) *
                     cos(radians(longitude) - radians(?)) + sin(radians(?)) *
                     sin(radians(latitude)))) AS distance_km',
                [$lat, $lng, $lat]
            )
            ->havingRaw('distance_km <= ?', [$radiusKm])
            ->orderBy('distance_km')
            ->get();

        return response()->json($donors);
    }
}
