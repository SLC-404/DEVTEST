<?php

namespace App\Livewire\Store;

use App\Models\Client;
use App\Models\Quote;
use App\Services\CartService;
use App\Services\OrderService;
use App\Services\PaymentService;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use RuntimeException;

#[Layout('layouts.store')]
#[Title('Checkout')]
class Checkout extends Component
{
    public ?int $quoteId = null;
    public string $email = '';
    public string $cardName = '';
    public string $cardNumber = '';
    public string $cardExpiry = '';
    public string $cardCvv = '';
    public ?string $paymentError = null;

    public function mount(CartService $cart, OrderService $orders)
    {
        $items = $cart->items();

        if ($items->isEmpty()) {
            return $this->redirectRoute('cart', navigate: true);
        }
        $client = Client::firstOrCreate(['user_id' => auth()->id()]);
        $this->quoteId = $orders->syncQuote($client, $items)->id;
        $this->email    = auth()->user()->email;
        $this->cardName = auth()->user()->name;
    }
    protected function rules(): array
    {
        return [
            'email'      => ['required', 'email', 'max:255'],
            'cardName'   => ['required', 'string', 'max:100'],
            'cardNumber' => ['required', 'regex:/^\d{16}$/'],
            'cardExpiry' => ['required', 'regex:/^(0[1-9]|1[0-2])\/\d{2}$/', function ($attribute, $value, $fail) {
                [$month, $year] = explode('/', $value);

                if (Carbon::create(2000 + (int) $year, (int) $month, 1)->endOfMonth()->isPast()) {
                    $fail('La tarjeta está vencida.');
                }
            }],
            'cardCvv'    => ['required', 'regex:/^\d{3}$/'],
        ];
    }
    protected function messages(): array
    {
        return [
            'required'         => 'Este campo es obligatorio.',
            'email.email'      => 'Escribe un correo válido.',
            'cardNumber.regex' => 'Deben ser 16 dígitos, sin espacios.',
            'cardExpiry.regex' => 'Usa el formato MM/AA.',
            'cardCvv.regex'    => 'Deben ser 3 dígitos.',
        ];
    }
    public function updated(string $property): void
    {
        if ($property !== 'quoteId') {
            $this->validateOnly($property);
        }
    }

    public function pay(PaymentService $payments, OrderService $orders, CartService $cart)
    {
        $this->paymentError = null;
        $this->validate();
        $quote = Quote::open()
            ->whereKey($this->quoteId)
            ->whereHas('client', fn($query) => $query->where('user_id', auth()->id()))
            ->first();

        if (! $quote) {
            return $this->redirectRoute('cart', navigate: true);
        }
        $result = $payments->charge((float) $quote->total, $this->cardNumber);
        if (! $result['approved']) {
            $this->paymentError = $result['message'];
            return;
        }

        try {
            $order = $orders->placeOrder($quote, $this->email, $result['reference']);
        } catch (RuntimeException $e) {
            $this->paymentError = $e->getMessage();
            return;
        }

        $cart->clear();
        $this->dispatch('cart-updated');
        return $this->redirectRoute('order.confirmation', $order, navigate: true);
    }
    public function render()
    {
        $quote  = Quote::with('items.product')->findOrFail($this->quoteId);
        $totals = OrderService::totals($quote->items);
        return view('livewire.store.checkout', compact('quote', 'totals'));
    }
}
