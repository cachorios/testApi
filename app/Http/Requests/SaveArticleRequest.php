<?php

namespace App\Http\Requests;

use App\Rules\Slug;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SaveArticleRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        $id = $this->route('article') ? $this->route('article')->id : null;
        return [
            'data.attributes.title' => 'required|min:4',
            'data.attributes.slug' => [
                'required',
                'alpha_dash',
                 new Slug(),
                Rule::unique('articles','slug')->ignore($id)],
            'data.attributes.content' => 'required',
        ];
    }

    public function validated($key = null, $default = null)
    {
        return parent::validated()['data']['attributes'];
    }


}
