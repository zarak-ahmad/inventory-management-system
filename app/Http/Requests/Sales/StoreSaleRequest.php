<?php

namespace App\Http\Requests\Sales;

use App\Models\Sale;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSaleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasPermission('manage_sales') ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'customer_id' => ['nullable', 'integer', 'exists:customers,id'],
            'discount' => ['nullable', 'numeric', 'min:0'],
            'tax' => ['nullable', 'numeric', 'min:0'],
            'payment_method' => ['required', Rule::in(Sale::paymentMethods())],
            'payment_status' => ['nullable', Rule::in([
                Sale::STATUS_PAID,
                Sale::STATUS_UNPAID,
                Sale::STATUS_PARTIAL,
            ])],
            'notes' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'integer', 'exists:products,id'],
            'items.*.quantity' => ['required', 'numeric', 'min:0.01'],
            'items.*.selling_price' => ['nullable', 'numeric', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'items.required' => 'Add at least one product to the cart.',
            'items.min' => 'Add at least one product to the cart.',
            'payment_method.required' => 'Select a payment method.',
        ];
    }
}
