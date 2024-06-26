<?php

use App\Service\QueryEmbedding;
use Illuminate\Support\Facades\Route;
use App\Service\Scraper;
use App\Service\Tokenizer;
use LLPhant\Chat\OpenAIChat;
use LLPhant\Embeddings\DocumentSplitter\DocumentSplitter;
use LLPhant\Embeddings\EmbeddingGenerator\OpenAI\OpenAI3SmallEmbeddingGenerator;
use LLPhant\Embeddings\VectorStores\Memory\MemoryVectorStore;
use LLPhant\OpenAIConfig;
use LLPhant\Query\SemanticSearch\QuestionAnswering;

Route::view('/', 'welcome');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::get('/ai-test',function(){


    $scapper = new Scraper();
    $result = $scapper->handle("https://zubairidrisaweda.medium.com/introduction-to-web-scraping-with-laravel-a217e1444f7c");
    $result = preg_replace('/\s+/', '', $result);

    $tokenService = new Tokenizer();

    $tokenService->setContent($result);
    $splitDocuments = DocumentSplitter::splitDocuments($tokenService->getDocuments(), 800);

    $embbededService = new QueryEmbedding();

    $embeddedDocuments = $embbededService->embedDocuments($splitDocuments);

    $vectorStore = new MemoryVectorStore();

    $vectorStore->addDocuments($embeddedDocuments);

    $embeddingGenerator = new OpenAI3SmallEmbeddingGenerator();
    $config = new OpenAIConfig();
    $config->apiKey = config('app.open_ai_api_key');
    $config->model= 'gpt-3.5-turbo';

    $qa = new QuestionAnswering(
        $vectorStore,
        $embeddingGenerator,
        new OpenAIChat($config)
    );

    $answer = $qa->answerQuestion('how to scrape');

    dd($answer);

});


Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

require __DIR__.'/auth.php';
