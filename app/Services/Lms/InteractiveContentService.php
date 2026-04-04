<?php

namespace App\Services\Lms;

use App\Models\LmsInteractiveContent;

class InteractiveContentService
{
    public function create(int $lessonId, int $orgId, string $widgetType, array $config, string $title): LmsInteractiveContent
    {
        return LmsInteractiveContent::create([
            'organization_id' => $orgId,
            'lesson_id' => $lessonId,
            'widget_type' => $widgetType,
            'config' => $config,
            'title' => $title,
        ]);
    }

    public function update(int $id, array $config, string $title): LmsInteractiveContent
    {
        $content = LmsInteractiveContent::findOrFail($id);
        $content->update([
            'config' => $config,
            'title' => $title,
        ]);
        $content->refresh();

        return $content;
    }

    public function getForLesson(int $lessonId): \Illuminate\Database\Eloquent\Collection
    {
        return LmsInteractiveContent::where('lesson_id', $lessonId)->get();
    }

    public function getAvailableWidgetTypes(): array
    {
        return [
            [
                'type' => 'accordion',
                'label' => 'Accordion',
                'schema' => ['panels' => [['title' => 'string', 'content' => 'string']]],
            ],
            [
                'type' => 'tabs',
                'label' => 'Tabs',
                'schema' => ['tabs' => [['label' => 'string', 'content' => 'string']]],
            ],
            [
                'type' => 'timeline',
                'label' => 'Timeline',
                'schema' => ['events' => [['date' => 'string', 'title' => 'string', 'description' => 'string']]],
            ],
            [
                'type' => 'hotspot',
                'label' => 'Hotspot Image',
                'schema' => ['image_url' => 'string', 'spots' => [['x' => 'number', 'y' => 'number', 'label' => 'string']]],
            ],
            [
                'type' => 'drag_drop',
                'label' => 'Drag & Drop',
                'schema' => ['items' => [['text' => 'string', 'zone' => 'string']], 'zones' => ['string']],
            ],
            [
                'type' => 'fill_blanks',
                'label' => 'Fill in the Blanks',
                'schema' => ['text' => 'string', 'blanks' => [['position' => 'number', 'answer' => 'string']]],
            ],
        ];
    }
}
