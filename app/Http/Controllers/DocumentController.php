<?php

namespace App\Http\Controllers;

use App\Enums\DocumentType;
use App\Http\Requests\DocumentRequest;
use Inertia\Inertia;
use App\Models\Document;
use App\Services\DocumentManager;
use Illuminate\Http\Request;

class DocumentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('Documents/Index', [
            // TODO paginate documents in frontend
            'documents' => Document::all(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Documents/Form', [
            'categories' => DocumentType::getSelectList(),
            'document' => new Document(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(DocumentRequest $request)
    {
        $data = $request->validated();
        if($request->hasFile('file')){
            $data['path'] = $request->file('file')->store();
        }
        unset($data['file']);

        $document = Document::create($data);
        app(DocumentManager::class)->process($document);

        return to_route('document.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Document $document)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Document $document)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Document $document)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Document $document)
    {
        //
    }
}
