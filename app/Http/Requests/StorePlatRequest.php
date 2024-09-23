<?php

namespace App\Http\Requests;

use App\Models\Plat;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StorePlatRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {

            $platId = $this->route('plat') ? $this->route('plat')->id : null;

        return [
            'titre' => [
                'required',
                'max:255',
                $platId ? "unique:plats,titre,{$platId}" : 'unique:plats'
            ],
            'recette' => 'required|max:2048',
            'likes' => 'required|integer',
        ];
    }
}
