<?php

namespace App\Http\Requests\Channel;

use Illuminate\Foundation\Http\FormRequest;

class ChannelStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->user()->canAddChatChannels();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name'          => 'required|unique:channels,name',
            'selectedUsers' => 'nullable|array',
            'selectedUsers.*' => 'nullable|exists:users,id',
        ];
    }

    /**
 * Get the error messages for the defined validation rules.
 *
 * @return array
 */
    public function messages()
    {
        return [
            'name.required' => 'channel name is required.'
        ];
    }
}
