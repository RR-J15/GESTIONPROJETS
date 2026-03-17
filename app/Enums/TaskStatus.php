<?php

namespace App\Enums;

enum TaskStatus: string
{
    case AFaire     = 'a_faire';
    case EnCours    = 'en_cours';
    case EnRevision = 'en_revision';
    case Termine    = 'termine';
}