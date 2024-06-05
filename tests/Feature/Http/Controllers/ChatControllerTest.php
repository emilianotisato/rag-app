<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Chat;
use App\Models\User;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ChatControllerTest extends TestCase
{
    #[Test]
    public function it_uses_context_on_every_new_prompt()
    {
        $this->actingAs($user = User::factory()->create());

        $chat = Chat::factory()->create([
            'user_id' => $user->id,
        ]);
        $chat->messages()->create([
            'content' => 'Demo message by user',
            'is_user' => true,
        ]);
        $chat->messages()->create([
            'content' => 'Demo message by GPT',
            'is_user' => false,
        ]);

        $response = MockResponse::fixture('query_vectors');
        $this->mockPineconeClient('query', $response);
        OpenAI::fake([
            CreateResponse::fake(),
        ]);

        
    }
}
