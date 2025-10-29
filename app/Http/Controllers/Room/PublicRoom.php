<?php

namespace App\Http\Controllers\Room;

use App\Http\Controllers\Controller;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PublicRoom extends Controller
{
    public function index()
    {
        return view('room.publicroom');
    }

    // UPLOAD TOOLS TO PUBLIC ROOM
    public function send(Request $request)
    {
        if ($request->isMethod('get')) {
            return view('tools.send');
        } elseif ($request->isMethod('post')) {
            $validate = $request->validate([
                'content' => 'required|string|max:255',
            ]);
            Message::create([
                'content' => $validate['content'],
                'user_id' => Auth::user()->id,
                'is_public' => true,

            ]);

            return view('tools.send');
        }
    }

    public function setting()
    {
        return view('tools.setting');
    }

    public function view()
    {
        $msg = Message::where('is_public', true)->get();

        return view('tools.view', compact('msg'));
    }
}
