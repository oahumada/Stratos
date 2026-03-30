<?php

namespace App\Jobs;

use App\Models\LmsArticle;
use App\Services\Content\ContentAgentService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GenerateLmsArticle implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $organizationId;
    public string $topic;
    public array $options;

    public function __construct(int $organizationId, string $topic, array $options = [])
    {
        $this->organizationId = $organizationId;
        $this->topic = $topic;
        $this->options = $options;
    }

    public function handle(ContentAgentService $service): void
    {
        $generated = $service->generateDraft($this->topic, $this->options);

        $status = $this->options['auto_publish'] ?? false ? 'published' : 'pending_review';

        $article = LmsArticle::create([
            'organization_id' => $this->organizationId,
            'author_id' => $this->options['author_id'] ?? null,
            'title' => $generated['title'] ?? 'Artículo generado',
            'slug' => $generated['slug'] ?? uniqid('art_'),
            'topic' => $this->topic,
            'excerpt' => $generated['excerpt'] ?? null,
            'body' => $generated['body'] ?? null,
            'status' => $status,
            'published_at' => $status === 'published' ? now() : null,
        ]);

        // Optionally: notify reviewers / send events (left for future enhancement)
    }
}
