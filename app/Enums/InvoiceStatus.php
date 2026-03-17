<?php

namespace App\Enums;

enum InvoiceStatus: string
{
    case EnAttente = 'en_attente';
    case Payee     = 'payee';
    case EnRetard  = 'en_retard';
}