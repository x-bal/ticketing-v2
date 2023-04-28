<?php

namespace App\Http\Requests\Member;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMemberRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            // 'rfid' => 'required|string|unique:members,rfid,' . $this->member,
            'nama' => 'required|string',
            'alamat' => 'required|string',
            'no_ktp' => 'required|numeric',
            'no_hp' => 'required|numeric',
            'tanggal_lahir' => 'required'
        ];
    }
}
