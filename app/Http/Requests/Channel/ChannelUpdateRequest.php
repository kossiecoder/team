<?php

namespace App\Http\Requests\Channel;

use Illuminate\Foundation\Http\FormRequest;

class ChannelUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->user()->canEditChatChannels() || auth()->user()->canEditOwnChatChannels();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required_without_all:description|string|max:100',
            'description' => 'required_without_all:name|max:255'
        ];
    }
}
