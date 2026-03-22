<?php

namespace App\Jobs;

use App\Models\Embedding;
use App\Models\GuideFaq;
use App\Models\People;
use App\Models\Roles;
use App\Models\Scenario;
use App\Services\EmbeddingService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class EmbeddingIndexJob implements ShouldQueue
{
    use Queueable;

    /**
     * @param  string|null  $resourceType  Limita la indexación a un tipo concreto (people, role, scenario,...)
     * @param  int|null  $organizationId  Limita a una organización concreta
     */
    public function __construct(
        protected ?string $resourceType = null,
        protected ?int $organizationId = null,
        protected bool $onlyRecent = false,
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        /** @var EmbeddingService $embeddingService */
        $embeddingService = app(EmbeddingService::class);

        if ($this->resourceType === null || $this->resourceType === 'people') {
            $this->indexPeople($embeddingService);
        }

        if ($this->resourceType === null || $this->resourceType === 'role') {
            $this->indexRoles($embeddingService);
        }

        if ($this->resourceType === null || $this->resourceType === 'scenario') {
            $this->indexScenarios($embeddingService);
        }

        if ($this->resourceType === null || $this->resourceType === 'guide_faq') {
            $this->indexGuideFaqs($embeddingService);
        }
    }

    protected function indexPeople(EmbeddingService $embeddingService): void
    {
        if (! class_exists(People::class)) {
            return;
        }

        $query = People::query();
        if ($this->organizationId) {
            $query->where('organization_id', $this->organizationId);
        }

        if ($this->onlyRecent) {
            $query->where('updated_at', '>=', now()->subHours(24));
        }

        $query->chunkById(100, function ($peopleChunk) use ($embeddingService) {
            foreach ($peopleChunk as $person) {
                $text = implode(' | ', array_filter([
                    $person->name ?? null,
                    $person->email ?? null,
                    $person->role_title ?? null,
                ]));

                $vector = $embeddingService->generate($text);
                if (! $vector) {
                    continue;
                }

                Embedding::updateOrCreate(
                    [
                        'organization_id' => $person->organization_id ?? null,
                        'resource_type' => 'people',
                        'resource_id' => $person->id,
                    ],
                    [
                        'metadata' => [
                            'name' => $person->name ?? null,
                            'email' => $person->email ?? null,
                            'role_title' => $person->role_title ?? null,
                        ],
                        'embedding' => $vector,
                    ]
                );
            }
        });
    }

    protected function indexRoles(EmbeddingService $embeddingService): void
    {
        if (! class_exists(Roles::class)) {
            return;
        }

        $query = Roles::query();
        if ($this->organizationId) {
            $query->where('organization_id', $this->organizationId);
        }

        if ($this->onlyRecent) {
            $query->where('updated_at', '>=', now()->subHours(24));
        }

        $query->chunkById(100, function ($rolesChunk) use ($embeddingService) {
            foreach ($rolesChunk as $role) {
                $vector = $embeddingService->forRole($role) ?? null;
                if (! $vector) {
                    continue;
                }

                Embedding::updateOrCreate(
                    [
                        'organization_id' => $role->organization_id ?? null,
                        'resource_type' => 'role',
                        'resource_id' => $role->id,
                    ],
                    [
                        'metadata' => [
                            'name' => $role->name ?? null,
                            'description' => $role->description ?? null,
                        ],
                        'embedding' => $vector,
                    ]
                );
            }
        });
    }

    protected function indexScenarios(EmbeddingService $embeddingService): void
    {
        if (! class_exists(Scenario::class)) {
            return;
        }

        $query = Scenario::query();
        if ($this->organizationId) {
            $query->where('organization_id', $this->organizationId);
        }

        if ($this->onlyRecent) {
            $query->where('updated_at', '>=', now()->subHours(24));
        }

        $query->chunkById(100, function ($scenariosChunk) use ($embeddingService) {
            foreach ($scenariosChunk as $scenario) {
                $vector = $embeddingService->forScenario($scenario) ?? null;
                if (! $vector) {
                    continue;
                }

                Embedding::updateOrCreate(
                    [
                        'organization_id' => $scenario->organization_id ?? null,
                        'resource_type' => 'scenario',
                        'resource_id' => $scenario->id,
                    ],
                    [
                        'metadata' => [
                            'name' => $scenario->name ?? null,
                            'description' => $scenario->description ?? null,
                        ],
                        'embedding' => $vector,
                    ]
                );
            }
        });
    }

    protected function indexGuideFaqs(EmbeddingService $embeddingService): void
    {
        if (! class_exists(GuideFaq::class)) {
            return;
        }

        $query = GuideFaq::query()->where('is_active', true);

        if ($this->organizationId) {
            $query->where(function ($q) {
                $q->whereNull('organization_id')
                    ->orWhere('organization_id', $this->organizationId);
            });
        }

        if ($this->onlyRecent) {
            $query->where('updated_at', '>=', now()->subHours(24));
        }

        $query->chunkById(100, function ($faqChunk) use ($embeddingService) {
            foreach ($faqChunk as $faq) {
                $text = implode("\n", array_filter([
                    $faq->title,
                    $faq->question,
                    $faq->answer,
                    is_array($faq->tags) ? implode(', ', $faq->tags) : null,
                ]));

                $vector = $embeddingService->generate($text);
                if (! $vector) {
                    continue;
                }

                Embedding::updateOrCreate(
                    [
                        'organization_id' => $faq->organization_id,
                        'resource_type' => 'guide_faq',
                        'resource_id' => $faq->id,
                    ],
                    [
                        'metadata' => [
                            'name' => $faq->title,
                            'category' => $faq->category,
                            'slug' => $faq->slug,
                        ],
                        'embedding' => $vector,
                    ]
                );
            }
        });
    }
}
