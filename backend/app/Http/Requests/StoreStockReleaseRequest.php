<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreStockReleaseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        $farmId = $this->header('X-Farm-ID');

        return [
            'pond_id' => [
                'required',
                'integer',
                Rule::exists('ponds', 'id')->where('farm_id', $farmId),
            ],
            'species' => ['required', 'string', 'max:100'],
            'quantity' => ['required', 'integer', 'min:1'],
            'avg_weight_gram' => ['required', 'numeric', 'min:0.01'],
            'cost_bdt' => ['required', 'numeric', 'min:0'],
            'release_date' => ['required', 'date', 'before_or_equal:today'],
            'notes' => ['nullable', 'string', 'max:500'],
        ];
    }
}
