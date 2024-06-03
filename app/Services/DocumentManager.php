<?php 

namespace App\Services;

use App\Models\Document;
use App\Enums\DocumentType;
use App\Jobs\ProcessPDFDocument;
use App\Jobs\ProcessWebPageDocument;

class DocumentManager
{
	public function process(Document $document): void
	{
		if ($document->type === DocumentType::PDF) {
			ProcessPDFDocument::dispatch($document);
		} elseif ($document->type === DocumentType::WEB_PAGE) {
			ProcessWebPageDocument::dispatch($document);
		}
	}
}