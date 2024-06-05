<?php

namespace Tests\Feature\Http\Controllers;

use Tests\TestCase;
use App\Models\Chat;
use App\Models\User;
use OpenAI\Resources\Chat as OpenAIChat;
use OpenAI\Laravel\Facades\OpenAI;
use Saloon\Http\Faking\MockResponse;
use PHPUnit\Framework\Attributes\Test;
use Illuminate\Foundation\Testing\WithFaker;
use OpenAI\Responses\Embeddings\CreateResponse as CreateEmbeddingResponse;
use OpenAI\Responses\Chat\CreateResponse as CreateCompletionResponse;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ChatControllerTest extends TestCase
{
    use RefreshDatabase;

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
            CreateEmbeddingResponse::fake(),
            CreateCompletionResponse::fake([
                'choices' => [
                    [
                        'message' => [
                            'role' => 'assistant',
                            'content' => 'some answer'
                        ],
                    ],
                ],
            ]),
        ]);

        $this->post(route('chats.prompt', $chat), [
            'prompt' => 'New message by user',
        ]);

        // Assert that the chat create was called with the correct context
        OpenAI::assertSent(OpenAIChat::class, function (string $method, array $parameters): bool {
            
            return $method === 'create' &&
                $parameters['model'] === 'gpt-3.5-turbo' &&
               
                $parameters['messages'] == json_decode('[{"role":"system","content":"Use the following knowledge to answer if applies: ```Sample data\n```"},{"role":"user","content":"Demo message by user"},{"role":"assistant","content":"Demo message by GPT"},{"role":"user","content":"New message by user"}]', true);
        });        
    }
}
