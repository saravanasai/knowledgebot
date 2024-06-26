<?php

namespace App\Service;

use Exception;
use Illuminate\Support\Facades\Log;
use LLPhant\Chat\OpenAIChat;
use LLPhant\Embeddings\EmbeddingGenerator\OpenAI\OpenAI3SmallEmbeddingGenerator;
use LLPhant\OpenAIConfig;
use OpenAI\Laravel\Facades\OpenAI;

class QueryEmbedding
{

    public $openAIembeddingGenerator;
    public $openAI;
    public function __construct()
    {

        $config = new OpenAIConfig();
        $config->apiKey = config('app.open_ai_api_key');
        $this->openAI = new OpenAIChat($config);
        $this->openAIembeddingGenerator = new OpenAI3SmallEmbeddingGenerator();
    }

    public function getQueryEmbedding($question): array
    {

        if (config('app.open_ai_fake')) {
            return [
                0.004917062,
                -0.067290515,
                -0.021593224,
                0.008693784,
                -0.00547677,
                -0.04069653,
                0.0017955122,
                -0.0030339318,
                -0.01734572,
                -0.02887466,
                -0.01443733,
                0.009316263,
                -0.031741202,
                -0.05009126,
                0.018632526,
                -0.006538646,
                -0.033101242,
                -0.03188767,
                0.07754311
            ];
        }

        $result = $this->openAIembeddingGenerator->embedText($question);



        if (!$result) {
            throw new Exception("Failed to generated query embedding!");
        }

        return $result;
    }


    public function embedDocuments(array $documents): array
    {

        if (config('app.open_ai_fake')) {
            return [

            ];
        }

        $result = $this->openAIembeddingGenerator->embedDocuments($documents);



        if (!$result) {
            throw new Exception("Failed to generated query embedding!");
        }

        return $result;
    }

    // public function askQuestionStreamed($context, $question)
    // {
    //     $system_template = "
    //     Use the following pieces of context to answer the users question.
    //     If you don't know the answer, just say that you don't know, don't try to make up an answer.
    //     ----------------
    //     {context}
    //     ";
    //     $system_prompt = str_replace("{context}", $context, $system_template);

    //     return OpenAI::chat()->createStreamed([
    //         'model' => 'gpt-3.5-turbo',
    //         'temperature' => 0.8,
    //         'messages' => [
    //             ['role' => 'system', 'content' => $system_prompt],
    //             ['role' => 'user', 'content' => $question],
    //         ],
    //     ]);
    // }
}
