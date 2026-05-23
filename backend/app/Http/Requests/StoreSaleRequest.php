<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSaleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'pond_id' => [
                'required',
                'integer',
                Rule::exists('ponds', 'id')->where('farm_id', $this->user()?->current_farm_id),
            ],
            'sale_type' => ['required', 'string', 'in:wholesale,retail'],
            'date' => ['required', 'date', 'date_format:Y-m-d'],
            'fish_type' => ['required', 'string', 'max:255'],
            'avg_fish_weight_g' => ['required', 'numeric', 'min:0.01'],
            'quantity_kg' => ['required', 'numeric', 'min:0.01'],
            'rate_per_kg' => ['required', 'numeric', 'min:0.01'],
            'customer_name' => ['nullable', 'string', 'max:255'],
            'custom_tags' => ['nullable', 'array'],
            'custom_tags.*' => ['string', 'max:100'],
            'amount_paid' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
