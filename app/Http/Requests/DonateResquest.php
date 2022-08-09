<?php

namespace App\Http\Requests;

class DonateResquest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'account' => 'required',
            'product_id' => 'required',
            'pay_method' => 'required',
            'amount' => $this->input('product_id') == -1 ? 'required|numeric|integer' : 'nullable',
            'code' => 'required'
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
            'account' => '帳號',
            'product_id' => '贊助方式',
            'amount' => '金額',
            'pay_method' => '付款方式',
            'code' => '驗證碼'
        ];
    }
}
