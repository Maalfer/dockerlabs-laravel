<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BunkerToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class TokenAdminController extends Controller
{
    public function index()
    {
        // Devuelve JSON con todos los tokens (para el modal)
        $tokens = BunkerToken::orderByDesc('id')
            ->get(['id','name','active','created_at']);

        return response()->json($tokens);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['nullable','string','max:120'],
        ]);

        $plain = Str::random(40);

        BunkerToken::create([
            'created_by' => $request->user()->id,
            'name'       => $data['name'] ?? null,
            'token_hash' => Hash::make($plain),
            'active'     => true, // permanentes
        ]);

        return response()->json([
            'ok'    => true,
            'plain' => $plain, // mostrar una vez en el modal
        ]);
    }

    public function toggle($id)
    {
        $token = BunkerToken::findOrFail($id);
        $token->active = !$token->active;
        $token->save();

        return response()->json(['ok' => true, 'active' => $token->active]);
    }

    public function destroy($id)
    {
        BunkerToken::whereKey($id)->delete();
        return response()->json(['ok' => true]);
    }
}
