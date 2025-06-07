<?php

namespace App\Enums;

use Illuminate\Support\Collection;

enum LineStatus: string
{
    case PENDING = 'pending';
    case PROMISED = 'promised';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';
    case CANCELLED = 'cancelled';
    case COMPLETED = 'completed';
    case DELAYED = 'delayed';

    public function label(): string
    {
        return match($this) {
            self::PENDING => 'Pending',
            self::PROMISED => 'Promised',
            self::APPROVED => 'Approved',
            self::REJECTED => 'Rejected',
            self::CANCELLED => 'Cancelled',
            self::COMPLETED => 'Completed',
            self::DELAYED => 'Delayed',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::PENDING => 'gray',
            self::PROMISED => 'warning',
            self::APPROVED => 'success',
            self::REJECTED => 'danger',
            self::CANCELLED => 'danger',
            self::COMPLETED => 'success',
            self::DELAYED => 'warning',
        };
    }

    public static function statuses(): Collection
    {
        return collect([
            [
                'id' => self::PENDING->value,
                'title' => self::PENDING->label(),
                'color' => self::PENDING->color(),
            ],
            [
                'id' => self::PROMISED->value,
                'title' => self::PROMISED->label(),
                'color' => self::PROMISED->color(),
            ],
            // [
            //     'id' => self::APPROVED->value,
            //     'title' => self::APPROVED->label(),
            //     'color' => self::APPROVED->color(),
            // ],
            // [
            //     'id' => self::REJECTED->value,
            //     'title' => self::REJECTED->label(),
            //     'color' => self::REJECTED->color(),
            // ],
            // [
            //     'id' => self::CANCELLED->value,
            //     'title' => self::CANCELLED->label(),
            //     'color' => self::CANCELLED->color(),
            // ],
            [
                'id' => self::DELAYED->value,
                'title' => self::DELAYED->label(),
                'color' => self::DELAYED->color(),
            ],
            [
                'id' => self::COMPLETED->value,
                'title' => self::COMPLETED->label(),
                'color' => self::COMPLETED->color(),
            ]
        ]);
    }
}
