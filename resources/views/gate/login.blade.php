<x-layouts.app :title="$title">
    <form method="post" action="{{route('store.login')}}">
        @csrf
        <input placeholder="{{__('Username')}}" type="text" name="username" value="{{old('username')}}">
        <input placeholder="{{__('Password')}}" type="password" name="password">
        <button type="submit">{{__('Login')}}</button>

    </form>
</x-layouts.app>
