<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateStockReleaseRequest extends FormRequest
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
                'nullable',
                'integer',
                Rule::exists('ponds', 'id')->where('farm_id', $farmId),
            ],
            'species' => ['nullable', 'string', 'max:100'],
            'quantity' => ['nullable', 'integer', 'min:1'],
            'avg_weight_gram' => ['nullable', 'numeric', 'min:0.01'],
            'cost_bdt' => ['nullable', 'numeric', 'min:0'],
            'release_date' => ['nullable', 'date', 'before_or_equal:today'],
            'notes' => ['nullable', 'string', 'max:500'],
        ];
    }
}
