<?php

namespace App\Http\Requests;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        $task = $this->route('task');

        return $task !== null && $this->user()?->can('update', $task);
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
            'estimated_minutes' => ['required', 'integer', 'min:1', 'max:10080'],
            'started_at' => ['nullable', 'date'],
            'due_at' => ['nullable', 'date'],
            'assigned_to' => ['nullable', 'integer', 'exists:users,id'],
        ];
    }
}
