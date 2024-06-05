<?php

namespace App\Services;

use App\Models\Document;
use PhpParser\Comment\Doc;
use App\Enums\DocumentType;
use App\Jobs\ProcessPDFDocument;
use App\Jobs\ProcessTextDocument;
use Illuminate\Support\Collection;
use OpenAI\Laravel\Facades\OpenAI;
use App\Jobs\ProcessWebPageDocument;
use Probots\Pinecone\Client as Pinecone;

class DocumentManager
{
    public function process(Document $document): void
    {
        match ($document->type) {
            DocumentType::PDF => ProcessPDFDocument::dispatch($document),
            DocumentType::WEB_PAGE => ProcessWebPageDocument::dispatch($document),
            DocumentType::RAW_TEXT =>  ProcessTextDocument::dispatch($document),
            default => throw new \Exception('Unsupported document type'),
        };
    }

    public function chunk(Document $document): Collection
    {
        $chunks = collect(explode("\n", $document->content))->reduce(function ($carry, $item) {
			$last = count($carry) - 1;
			if (isset($carry[$last]) && strlen($carry[$last].$item) <= config('services.chunker.size')) {
				$carry[$last] .= $item;
			} else {
				$carry[] = $item;
			}
		
			return $carry;
		}, []); // initial value is an empty array
		
		return collect($chunks);
    }

    public function search(string $query): string
    {
        $embeddings = OpenAI::embeddings()->create([
            'model' => 'text-embedding-3-small',
            'input' => $query,
        ])->embeddings;

        
        $pinecone = new Pinecone(config('services.pinecone.api_key'), config('services.pinecone.index_host'));
       
        $response = $pinecone->data()->vectors()->query(
            vector: $embeddings[0]->embedding,
            topK: 4,
        );

        $vectorTexts = '';
        if($response->successful()) {
            foreach($response->array()['matches'] as $data) {
                $vectorTexts .= $data['metadata']['text'] . "\n";
            }
        } else {
            // TODO, should we throw an exception here?
        }

        return $vectorTexts;
    }
}
