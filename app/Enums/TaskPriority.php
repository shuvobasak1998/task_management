<?php

namespace App\Enums;

enum TaskPriority: string
{
    case Low = 'low';
    case Medium = 'medium';
    case High = 'high';

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return array_map(
            static fn (self $priority): string => $priority->value,
            self::cases(),
        );
    }
}
