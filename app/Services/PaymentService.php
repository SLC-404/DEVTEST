<?php

namespace App\Services;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class PaymentService
{
    /**
     * @return array{approved: bool, reference: ?string, message: string}
     */
    public function charge(float $amount, string $cardNumber): array
    {
        usleep(800_000);
        $card = preg_replace('/\D/', '', $cardNumber);
        $approved = ! str_ends_with($card, '0000');
        $result = [
            'approved'  => $approved,
            'reference' => $approved ? 'SIM-' . strtoupper(Str::random(10)) : null,
            'message'   => $approved ? 'Pago aprobado.' : 'Pago rechazado: fondos insuficientes.',
        ];

        Log::info('Pago simulado', [
            'amount'   => $amount,
            'card'     => '**** ' . substr($card, -4),
            'approved' => $approved,
        ]);

        return $result;
    }
}
