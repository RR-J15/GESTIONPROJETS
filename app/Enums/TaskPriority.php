<?php

namespace App\Enums;

enum TaskPriority: string
{
    case Basse    = 'basse';
    case Normale  = 'normale';
    case Haute    = 'haute';
    case Critique = 'critique';
}