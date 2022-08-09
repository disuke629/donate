<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\BaseRequest;

class ProductResquest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'server_id' => 'required',
            'name' => 'required',
            'amount' => 'required',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'server_id' => '伺服器',
            'name' => '商品名稱',
            'amount' => '金額',
        ];
    }
}
