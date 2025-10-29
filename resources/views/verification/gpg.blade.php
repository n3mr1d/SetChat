<x-layouts.app title="{{ __('GPG Verification') }}">
    <div class="p-4 max-w-2xl mx-auto">
        <div class="mb-4 p-3 rounded bg-green-100 text-green-800">
            <strong>{{ __('PGP Challenge Created expire at'). session('pgp_token_expires_at')}}</strong> —
            {{ __('Decrypt the message below locally and submit the plaintext token for verification.') }}
        </div>
        <div class="space-y-1 mb-4">
            <div><strong>{{ __('Fingerprint') }}:</strong> {{ session('finger') }}</div>
            <div><strong>{{ __('Key ID') }}:</strong> {{ session('keyid') }}</div>
            <div><strong>{{ __('Username') }}:</strong> {{ session('username') }} </div>
            <div><strong>{{ __('Created') }}:</strong> {{ session('created') }}</div>

        </div>


        <div class="mb-4">
            <label class="font-semibold block mb-1">{{ __('Encrypted message') }}:</label>

            <textarea readonly rows="12"
                class="w-full border rounded p-2 text-sm bg-gray-900 text-green-400 font-mono leading-tight"
                style="white-space: pre; overflow-x: auto;">{{ trim(session('armored')) }}</textarea>

            <p class="text-xs text-gray-500 mt-1">
                {{ __('Copy the entire content above and decrypt it with your private key to obtain the verification
                token.') }}
            </p>
            <form action="{{ route('index.register') }}" method="POST" class="space-y-3">
                @csrf
                <input type="hidden" name="action" value="resetgpg">
                <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">
                    {{ __('Reset') }}
                </button>
            </form>
        </div>
    </div>

    {{-- Verification form --}}
    <form method="POST" action="{{ route('index.register') }}" class="space-y-3">
        @csrf
        <input type="hidden" name="action" value="verify">

        <div>
            <label for="pgp_decrypted_token" class="font-semibold">{{ __('Decrypted token') }}:</label>
            <input id="pgp_decrypted_token" name="pgp_decrypted_token" type="text" class="w-full border rounded p-2"
                required placeholder="{{ __('Enter your decrypted token here') }}">
        </div>

        @if ($errors->any())
        <div class="bg-red-100 text-red-700 p-2 rounded">
            @foreach ($errors->all() as $error)
            <div>{{ __($error) }}</div>
            @endforeach
        </div>
        @endif

        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
            {{ __('Verify Token') }}
        </button>
    </form>
    </div>
</x-layouts.app>
