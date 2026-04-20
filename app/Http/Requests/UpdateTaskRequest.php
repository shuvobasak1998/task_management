<?php

namespace App\Http\Requests;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;

class UpdateTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        $task = $this->route('task');

        return $task !== null && $this->user()?->can('update', $task);
    }

    protected function prepareForValidation(): void
    {
        if (! $this->filled('due_at')) {
            return;
        }

        $this->merge([
            'due_at' => $this->normalizeDueAt($this->input('due_at')),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'status' => ['required', Rule::in(TaskStatus::values())],
            'progress_percent' => ['required', 'integer', 'between:0,100'],
            'priority' => ['required', Rule::in(TaskPriority::values())],
            'started_at' => ['nullable', 'date'],
            'due_at' => ['nullable', 'date'],
            'assigned_to' => ['nullable', 'integer', 'exists:users,id'],
        ];
    }

    private function normalizeDueAt(string $value): string
    {
        $formats = ['d/m/y', 'd/m/Y', 'Y-m-d\TH:i', 'Y-m-d H:i:s', 'Y-m-d'];

        foreach ($formats as $format) {
            try {
                $date = Carbon::createFromFormat($format, $value, config('app.timezone'));

                if (in_array($format, ['d/m/y', 'd/m/Y', 'Y-m-d'], true)) {
                    $date->endOfDay();
                }

                return $date->toDateTimeString();
            } catch (\Throwable) {
                continue;
            }
        }

        return $value;
    }
}
