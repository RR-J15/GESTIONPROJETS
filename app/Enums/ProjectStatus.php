<?php

namespace App\Enums;

enum ProjectStatus: string
{
    case Brouillon = 'brouillon';
    case EnCours   = 'en_cours';
    case EnPause   = 'en_pause';
    case Termine   = 'termine';
}