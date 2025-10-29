<form method="post" target="_top" action="{{route('logout')}}">

    @csrf
    <button type="submit">Logout</button>

</form>
