<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\BaseRequest;

class ServerResquest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required',
            'url_suffix' => 'required|unique:server',
            'sql_ip' => 'required',
            'sql_port' => 'required',
            'sql_database' => 'required',
            'sql_username' => 'required',
            'sql_password' => 'required',
            'blue_online' => 'required',
            'blue_number' => 'required',
            'blue_hash_key' => 'required',
            'blue_hash_iv' => 'required',
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
            'name' => '伺服器名稱',
            'url_suffix' => '網頁後綴詞',
            'sql_ip' => '資料庫ip',
            'sql_port' => '資料庫port',
            'sql_database' => '資料庫名稱',
            'sql_username' => '資料庫帳號',
            'sql_password' => '資料庫密碼',
            'blue_online' => '藍新環境',
            'blue_number' => '藍新商家代碼',
            'blue_hash_key' => '藍新Hash Key',
            'blue_hash_iv' => '藍新Hash Iv',
        ];
    }
}
