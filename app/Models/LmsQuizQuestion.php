<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LmsQuizQuestion extends Model
{
    protected $fillable = [
        'lms_quiz_id',
        'question_text',
        'question_type',
        'options',
        'correct_answer',
        'points',
        'explanation',
        'order',
    ];

    protected function casts(): array
    {
        return [
            'options' => 'array',
            'correct_answer' => 'array',
            'points' => 'integer',
            'order' => 'integer',
        ];
    }

    public function quiz(): BelongsTo
    {
        return $this->belongsTo(LmsQuiz::class, 'lms_quiz_id');
    }

    /**
     * Check if the given answer is correct based on question_type.
     */
    public function checkAnswer(mixed $answer): bool
    {
        $correct = $this->correct_answer;

        return match ($this->question_type) {
            'multiple_choice' => $this->checkMultipleChoice($answer, $correct),
            'true_false' => $this->checkTrueFalse($answer, $correct),
            'fill_blank' => $this->checkFillBlank($answer, $correct),
            'matching' => $this->checkMatching($answer, $correct),
            'short_answer' => false, // requires manual grading
            default => false,
        };
    }

    private function checkMultipleChoice(mixed $answer, array $correct): bool
    {
        $answer = is_array($answer) ? $answer : [$answer];
        sort($answer);
        sort($correct);

        return $answer === $correct;
    }

    private function checkTrueFalse(mixed $answer, array $correct): bool
    {
        $given = is_array($answer) ? ($answer[0] ?? null) : $answer;
        $expected = $correct[0] ?? null;

        return (bool) $given === (bool) $expected;
    }

    private function checkFillBlank(mixed $answer, array $correct): bool
    {
        $given = is_array($answer) ? ($answer[0] ?? '') : (string) $answer;

        foreach ($correct as $accepted) {
            if (mb_strtolower(trim($given)) === mb_strtolower(trim((string) $accepted))) {
                return true;
            }
        }

        return false;
    }

    private function checkMatching(mixed $answer, array $correct): bool
    {
        if (! is_array($answer) || ! is_array($correct)) {
            return false;
        }

        $normalize = fn (array $pairs) => collect($pairs)
            ->map(fn ($p) => mb_strtolower(trim($p['left'] ?? '')) . ':' . mb_strtolower(trim($p['right'] ?? '')))
            ->sort()
            ->values()
            ->toArray();

        return $normalize($answer) === $normalize($correct);
    }
}
