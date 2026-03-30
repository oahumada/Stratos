<?php

namespace App\Services\Content;

use App\Services\AbacusClient;
use Illuminate\Support\Str;

class ContentAgentService
{
    protected AbacusClient $abacus;

    public function __construct(AbacusClient $abacus)
    {
        $this->abacus = $abacus;
    }

    /**
     * Generate an article draft for a given topic and options.
     * Returns ['title'=>..., 'excerpt'=>..., 'body'=>..., 'slug'=>...]
     */
    public function generateDraft(string $topic, array $options = []): array
    {
        $prompt = $this->buildPrompt($topic, $options);

        $resp = $this->abacus->generate($prompt, ['max_tokens' => $options['max_tokens'] ?? 800]);

        $content = ['title' => "Artículo sobre $topic", 'excerpt' => null, 'body' => ''];

        if (is_array($resp) && isset($resp['content'])) {
            $content['body'] = $resp['content'];
            $content['excerpt'] = Str::limit(strip_tags($resp['content']), 200);
            $content['title'] = Str::headline(Str::limit(strip_tags($resp['content']), 60));
        } else {
            $content['body'] = is_string($resp) ? $resp : json_encode($resp);
            $content['excerpt'] = Str::limit(strip_tags((string) $content['body']), 200);
        }

        $content['slug'] = Str::slug($content['title']).'-'.Str::random(6);

        return $content;
    }

    protected function buildPrompt(string $topic, array $options = []): string
    {
        $tone = $options['tone'] ?? 'informative and concise';
        $length = $options['length'] ?? 'about 400-600 words';

        $prompt = "Escribe un artículo en español sobre: $topic. Mantén el tono $tone, longitud $length. Incluye un párrafo inicial atrayente, 3-4 secciones con subtítulos, y una conclusión con llamada a la acción para aprendizaje continuo. Devuelve solo el contenido en HTML.";

        return $prompt;
    }
}
