<?php

namespace App\Http\Requests\Order;

use Illuminate\Foundation\Http\FormRequest;

class CheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'site' => 'required|in:FR,IT,BE',
            'address_id' => 'required|exists:addresses,id',
            'payment_method' => 'required|in:bank_transfer'
        ];
    }
}
