<?php

namespace App\Http\Controllers\Room;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\SystemMessage;
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
        $msgUser = Message::where('is_public', true)->get();
        $systemMessage = SystemMessage::whereNull('room_id')->get();

        $msg = $msgUser->concat($systemMessage)->sortBy('created_at')->values();

        $processed = $msg->map(function ($item) {
            return [
                'id' => $item->id,
                'type' => $item->type ?? 'default',
                'username' => $item->username->username ?? 'System',
                'content' => $item->content,
                'style' => $this->getStyle($item),
                'created_at' => $item->created_at,
            ];
        });

        return view('tools.view', ['messages' => $processed]);
    }

    private function getStyle($msg)
    {
        $type = $msg->type ?? 'default';

        return match ($type) {
            'user_join', 'user_promoted', 'user_unbanned' => 'bg-green-100 text-green-800',
            'user_leave', 'user_demoted', 'user_banned' => 'bg-red-100 text-red-800',
            'room_created' => 'bg-blue-100 text-blue-800',
            'settings_changed' => 'bg-yellow-100 text-yellow-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }
}
