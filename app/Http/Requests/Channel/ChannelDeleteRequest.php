<?php

namespace App\Http\Requests\Channel;

use Illuminate\Foundation\Http\FormRequest;

class ChannelDeleteRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->user()->canDeleteChatChannels() || auth()->user()->canDeleteOwnChatChannels();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            //
        ];
    }
}
