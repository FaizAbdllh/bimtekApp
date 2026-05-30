<?php

namespace App\Http\Controllers;

use App\Mail\ActivationTokenMail;
use App\Models\ActivationToken;
use App\Models\Bimtek;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class ActivationController extends Controller
{
    public function generate(Request $request, Bimtek $bimtek, User $user)
    {
        // Authorization: only PIC or panitia can generate
        $auth = Auth::user();
        $isPic = $bimtek->pic_user_id === $auth->id;
        $isPanitia = $bimtek->panitia()->where('users.id', $auth->id)->exists();
        if (!$isPic && !$isPanitia && !$auth->isAdminIt()) {
            abort(403);
        }

        [$token, $raw] = ActivationToken::generateFor($user, 7, $auth->id);
        // associate bimtek when token is created via bimtek context
        $token->bimtek_id = $bimtek->id;
        $token->save();

        // Send email if available
        $sentEmail = false;
        if (!empty($user->email)) {
            try {
                Mail::to($user->email)->queue(new ActivationTokenMail($user, $bimtek, $raw));
                $sentEmail = true;
            } catch (\Exception $e) {
                // log but continue
            }
        }

        // If the request is an AJAX request (X-Requested-With), return raw token in JSON
        if ($request->header('X-Requested-With') === 'XMLHttpRequest') {
            return response()->json([
                'raw_token' => $raw,
                'sent_email' => $sentEmail,
            ]);
        }

        return redirect()->back()->with('success', 'Token aktivasi dibuat.' . ($sentEmail ? ' Token dikirim via email.' : " Token: $raw"));
    }

    public function showActivate(string $rawToken)
    {
        $token = ActivationToken::findByRawToken($rawToken);
        if (!$token || $token->used_at) {
            return view('activation.invalid');
        }
        if ($token->isExpired()) {
            return view('activation.expired');
        }
        return view('activation.activate', ['token' => $rawToken, 'user' => $token->user]);
    }

    public function activate(Request $request, string $rawToken)
    {
        $token = ActivationToken::findByRawToken($rawToken);
        if (!$token || $token->used_at || $token->isExpired()) {
            return redirect()->route('welcome')->with('error', 'Token tidak valid atau kadaluarsa.');
        }

        $user = $token->user;

        $validated = $request->validate([
            'nip' => 'nullable|string|max:50',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if (!empty($validated['nip'])) {
            // If user has nip and it mismatches, block (optional)
            if ($user->nip && $user->nip !== $validated['nip']) {
                return back()->withErrors(['nip' => 'NIP tidak cocok dengan data peserta.']);
            }
            $user->nip = $validated['nip'];
        }

        $user->password = Hash::make($validated['password']);
        $user->save();

        $token->markUsed();

        Auth::login($user);
        return redirect()->route('dashboard')->with('success', 'Akun berhasil diaktifkan.');
    }
}
