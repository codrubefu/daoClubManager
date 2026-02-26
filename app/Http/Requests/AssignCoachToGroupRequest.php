<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AssignCoachToGroupRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'coach_id' => [
                'required',
                'integer',
                Rule::exists(User::class, 'id')->where(static fn ($query) => $query->where('role', 'coach')),
            ],
        ];
    }
}
