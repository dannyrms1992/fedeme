<?php

declare(strict_types=1);

namespace App\Interfaces\Http\Requests;

use App\Domain\Event\ValueObjects\EventStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

final class StoreEventRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create events') ?? false;
    }

    public function rules(): array
    {
        return [
            'name'             => ['required', 'string', 'max:255'],
            'slug'             => ['required', 'string', 'max:100', 'alpha_dash', Rule::unique('events', 'slug')],
            'subdomain'        => ['required', 'string', 'max:63', 'alpha_dash', Rule::unique('events', 'subdomain')],
            'description'      => ['nullable', 'string', 'max:2000'],
            'logo_path'        => ['nullable', 'string', 'max:500'],
            'primary_color'    => ['required', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'secondary_color'  => ['required', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'accent_color'     => ['nullable', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'bg_color'         => ['nullable', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'surface_color'    => ['nullable', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
        ];
    }
}
