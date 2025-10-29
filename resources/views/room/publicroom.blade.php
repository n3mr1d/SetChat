<x-layouts.app title="Public Room">

    <div class="flex flex-col items-center justify-center w-full h-screen">
        <iframe src="{{route('index.send')}}"></iframe>
        <iframe src="{{route('index.chat')}}"></iframe>
        <iframe src="{{route('index.setting')}}"></iframe>
    </div>
</x-layouts.app>
