<?php

declare(strict_types=1);

namespace App\Interfaces\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class AccessCodeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'code' => ['required', 'string', 'min:1', 'max:128'],
        ];
    }

    public function messages(): array
    {
        return [
            'code.required' => 'El código de acceso es requerido.',
        ];
    }
}
