<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Services\DocumentManager;
use OpenAI\Laravel\Facades\OpenAI;

class ChatController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('Chats/Index', [
            // TODO paginate documents in frontend
            'chats' => Chat::all(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $chat = Chat::create([
            'user_id' => auth()->id(),
        ]);

        return redirect()->route('chats.show', $chat);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function prompt(Request $request, Chat $chat)
    {
        $request->validate([
            'prompt' => 'required|string|min:3',
        ]);
        
        // $document = DocumentManager::create($request->input('content'));

        $chat->messages()->create([
            'content' => $request->input('prompt'),
            'is_user' => true,
        ]);

        $searchResults = app(DocumentManager::class)->search($request->input('prompt'));

        $previousMessages = $chat->messages()->latest()->take(10)->get()
        ->sortBy('created_at')
        ->map(function ($message) {
            return [
                'role' => $message->is_user ? 'user' : 'assistant',
                'content' => $message->content,
            ];
        })->toArray();

        if($searchResults) {
            $manipulateContext = [
                'role' => 'system',
                'content' => "Use the following knowledge to answer if applies: ```$searchResults```"
            ];
          $context = [$manipulateContext, ...$previousMessages];
        }

        $message = OpenAI::chat()->create([
            'model' => 'gpt-3.5-turbo',
            'messages' => [...$context],
        ])->choices[0]->message->content;

        $chat->messages()->create([
            'content' => $message,
            'is_user' => false,
        ]);

        return redirect()->route('chats.show', $chat);
    }

    /**
     * Display the specified resource.
     */
    public function show(Chat $chat)
    {
        return Inertia::render('Chats/Chat', [
            'chat' => $chat,
            'messages' => $chat->messages,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Chat $chat)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Chat $chat)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Chat $chat)
    {
        //
    }
}
