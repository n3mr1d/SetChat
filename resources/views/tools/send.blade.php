<form action={{route('index.send')}} method="post">
    @csrf
    <input type="text" name="content" placeholder="Content">
    <button type="submit">Send</button>

</form>
