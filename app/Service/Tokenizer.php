<?php

namespace App\Service;

use LLPhant\Embeddings\DataReader\DataReader;
use LLPhant\Embeddings\Document;

class Tokenizer implements DataReader
{
    public string $sourceType = 'url';

    public string $content = "";
    /**
     * @template T of Document
     *
     * @param  class-string<T>  $documentClassName
     * @param  string[]  $extensions
     */
    public function __construct(public readonly string $documentClassName = Document::class)
    {
    }

    public function setContent(string $content): void
    {
        $this->content = $content;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @return Document[]
     */
    public function getDocuments(): array
    {
        return [$this->getDocument($this->getContent(), $this->sourceType)];
    }


    private function getDocument(string $content, string $entry): mixed
    {
        $document = new $this->documentClassName();
        $document->content = $content;
        $document->sourceType = $this->sourceType;
        $document->sourceName = $entry;

        return $document;
    }

    public function tokenize($text, $chunk)
    {
        $normalizedText = preg_replace("/\n+/", "\n", $text);
        $words = explode(' ', $normalizedText);
        $words = array_filter($words);
        // return $words;
        $result = array_chunk($words, $chunk);
        return $result;
    }
}
