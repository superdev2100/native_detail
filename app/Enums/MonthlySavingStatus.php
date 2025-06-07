<?php

namespace App\Enums;

use Mokhosh\FilamentKanban\Concerns\IsKanbanStatus;

enum MonthlySavingStatus: string
{
    use IsKanbanStatus;

    case PENDING = 'pending';
    case PROMISED = 'promised';
    case DONE = 'completed';
}