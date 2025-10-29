<?php

namespace App\Http\Controllers;

use App\Models\Biouser;
use App\Models\SystemMessage;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use OpenPGP\OpenPGP;

class GateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function loginindex()
    {
        $title = 'Login';

        return view('gate.login', compact('title'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function loginstore(Request $request)
    {
        // validate
        $validate = $request->validate([
            'username' => 'required|string|max:255|min:4',
            'password' => 'required|string|max:255',
        ]);
        if (Auth::attempt($validate)) {
            $user = User::find(Auth::user()->id);
            $user->is_online = true;
            $user->last_seen = Carbon::now();
            $user->save();
            // create system message
            SystemMessage::create([
                'type' => 'user_join',
                'content' => "$user->username entered the chat.",

            ]);

            return redirect()->route('index.room');
        } else {
            echo 'kamu gagal login';
        }

    }

    public function register(Request $request)
    {
        if ($request->isMethod('get')) {
            if ($request->session()->has('pgp_token')) {
                return view('verification.gpg');
            }
            $title = 'Register';
            $encryptedArmored = session('pgp_encrypted_armored');

            return view('gate.register', compact('title', 'encryptedArmored'));
        }

        if ($request->isMethod('post')) {
            $action = $request->input('action', 'challenge');

            if ($action === 'challenge') {
                $validated = $request->validate([
                    'username' => 'required|exists:users,username|string|max:255|min:4|regex:/^[a-zA-Z0-9]+$/',
                    'password' => 'required|string|max:255|min:6|confirmed',
                    'pgp_public' => 'required|string',
                ]);

                try {
                    $key = OpenPGP::readPublicKey($validated['pgp_public']);
                    if (! $key) {
                        throw new Exception('Public key tidak valid atau tidak terbaca.');
                    }

                    $token = bin2hex(random_bytes(32));

                    $message = OpenPGP::createLiteralMessage("Your verification token is: {$token}");
                    $encrypted = OpenPGP::encrypt($message, [$key]);
                    $armored = $encrypted->armor();
                    session([
                        'pgp_token' => $token,
                        'pgp_token_expires_at' => Carbon::now()->addMinutes(15)->toDateTimeString(),
                        'pgp_pending_username' => $validated['username'],
                        'pgp_pending_password_hash' => Hash::make($validated['password']),
                        'pgp_pending_pgp_public' => $validated['pgp_public'],
                        'finger' => bin2hex($key->getFingerprint()),
                        'keyid' => bin2hex($key->getKeyPacket()->getKeyID()),
                        'username' => $validated['username'],
                        'created' => Carbon::parse($key->getCreationTime())->diffForHumans(),
                        'armored' => $armored,

                    ]);

                    return view('verification.gpg');

                } catch (Exception $e) {
                    return back()->withInput()->withErrors([
                        'pgp_public' => 'Public key invalid or not supported ',
                    ]);
                }
            }
            if ($action === 'resetgpg') {
                $this->clearsession($request);

                return redirect()->route('index.register');

            }
            if ($action === 'verify') {
                $enteredToken = $request->validate([
                    'pgp_decrypted_token' => 'required|string',
                ])['pgp_decrypted_token'];

                $sessionToken = session('pgp_token');
                $expiresAt = session('pgp_token_expires_at');

                if (! $sessionToken || ! $expiresAt || Carbon::now()->gt(Carbon::parse($expiresAt))) {
                    $this->clearPgpSession();

                    return redirect()
                        ->route('index.register')
                        ->withErrors(['pgp_decrypted_token' => 'Token invalid. Please try again.']);
                }

                if (hash_equals($sessionToken, $enteredToken)) {
                    $username = session('pgp_pending_username');
                    $password = session('pgp_pending_password_hash');
                    $pgpPublic = session('pgp_pending_pgp_public');

                    if (User::where('username', $username)->exists()) {
                        $this->clearPgpSession();

                        return redirect()
                            ->route('index.register')
                            ->withErrors(['username' => 'Username already taken.']);
                    }
                    // penyimpanan gpg to database
                    $storage = Storage::disk('public');
                    $uuid = Str::uuid();
                    $storage->put("gpg/$username-$uuid.txt", $pgpPublic);
                    // setup bio model

                    // setup user model
                    $user = User::create([
                        'username' => $username,
                        'password' => $password,
                        'is_online' => true,
                        'last_seen' => Carbon::now(),
                    ]);
                    $bio = Biouser::create([
                        'user_id' => $user->id,
                        'name' => 'Guest-'.Str::random('10'),
                        'pgp_public' => Storage::url("/gpg/$username-$uuid.txt"),
                        'path_avatar' => '/public/avatar/avatar.png',

                    ]);

                    $this->clearPgpSession();

                    Auth::login($user);

                    return redirect()
                        ->route('index.room')
                        ->with('status', 'Registered successfully.');
                }

                return back()->withInput()->withErrors([
                    'pgp_decrypted_token' => 'Token invalid or expired.',
                ]);
            }

            return redirect()
                ->route('index.register')
                ->withErrors(['action' => 'Aksi tidak valid.']);
        }

        return redirect()->route('index.register');
    }

    protected function clearsession(Request $request): void
    {
        $request->session()->flush();
    }

    private function clearPgpSession(): void
    {
        session()->forget([
            'pgp_token',
            'pgp_token_expires_at',
            'pgp_pending_username',
            'pgp_pending_password_hash',
            'pgp_pending_pgp_public',
            'pgp_encrypted_armored',
        ]);
    }

    public function logout(Request $request)
    {
        $user = User::find(Auth::user()->id);
        $user->is_online = false;
        $user->last_seen = Carbon::now();
        $user->save();
        Auth::logout();
        $request->session()->regenerate();

        return redirect('/');
    }
}
