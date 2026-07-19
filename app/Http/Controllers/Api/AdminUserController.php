<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class AdminUserController extends Controller
{
    public function index(Request $request)
    {
        return response()->json(
            User::query()
                ->when($request->filled('role'), fn ($q) => $q->where('role', $request->string('role')))
                ->paginate(20)
        );
    }

    public function show(User $user)
    {
        return response()->json($user);
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name' => ['sometimes', 'string', 'max:120'],
            'role' => ['sometimes', 'in:donor,recipient,bloodbank,admin'],
        ]);

        $user->update($data);

        return response()->json($user);
    }

    public function block(User $user)
    {
        $user->update(['status' => $user->status === 'blocked' ? 'active' : 'blocked']);

        return response()->json(['status' => $user->status]);
    }

    public function destroy(User $user)
    {
        $user->delete();

        return response()->json(['message' => 'User deleted.']);
    }
}
