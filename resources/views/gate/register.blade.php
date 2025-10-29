<x-layouts.app :title="$title">
    <form action="{{ route('index.register') }}" method="POST">
        @csrf
        <input type="hidden" name="action" value="challenge">
        <input type="text" name="username" placeholder="{{ __('Username') }}" value="{{ old('username') }}">

        <input type="password" name="password" placeholder="{{ __('Password') }}">

        <input type="password" name="password_confirmation" placeholder="{{ __('Confirm Password') }}">
        <textarea name="pgp_public" rows="8" cols="80" required></textarea>
        <button type="submit">
            {{ __('Register') }}
        </button>
    </form>

</x-layouts.app>
