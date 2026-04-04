<?php

namespace App\Services\Lms;

use App\Models\LmsMicroContent;

class MicrolearningService
{
    public function createMicroContent(int $lessonId, int $orgId, array $cards, int $estimatedMinutes): LmsMicroContent
    {
        return LmsMicroContent::create([
            'organization_id' => $orgId,
            'lesson_id' => $lessonId,
            'cards' => $cards,
            'estimated_minutes' => min($estimatedMinutes, 5),
        ]);
    }

    public function updateCards(int $microContentId, array $cards): LmsMicroContent
    {
        $micro = LmsMicroContent::findOrFail($microContentId);
        $micro->update(['cards' => $cards]);
        $micro->refresh();

        return $micro;
    }

    /**
     * Stub: generates micro cards from lesson content.
     */
    public function generateFromContent(string $lessonContent, int $orgId): array
    {
        $words = str_word_count($lessonContent);
        $chunks = array_chunk(str_split($lessonContent, 200), 1);

        $cards = [];
        foreach (array_slice($chunks, 0, 5) as $i => $chunk) {
            $cards[] = [
                'type' => $i === 0 ? 'text' : ($i % 2 === 0 ? 'tip' : 'flashcard'),
                'title' => 'Card '.($i + 1),
                'content' => implode('', $chunk),
                'media_url' => null,
                'quiz_data' => null,
            ];
        }

        return $cards;
    }

    public function getForLesson(int $lessonId): ?LmsMicroContent
    {
        return LmsMicroContent::where('lesson_id', $lessonId)->first();
    }
}
