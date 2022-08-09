<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\DonateResquest;
use App\Models\ProductModel;
use App\Models\ServerModel;
use App\Models\DonateRecordModel;
use App\Libraries\Bluenew;
use Config, DB;

class DonateController extends Controller
{
    // [GET] 畫面
    public function index(Request $request, $serverSuffix = '')
    {
        // 伺服器檢查
        $server = ServerModel::where('url_suffix', $serverSuffix)->first();
        if (empty($server)) {
            abort(404);
        }

        return view('index', [
            'server' => $server,
            'code' => captcha_img(),
            'products' => ProductModel::where('server_id', $server->id)->orderBy('sort', 'asc')->get(),
            'ip' => $request->ip()
        ]);
    }

    // [GET] 刷新驗證碼
    public function refreshCaptcha()
    {
        return response()->json([
            'code'=> captcha_img()
        ]);
    }

    // [POST] 檢查並導向
    public function checkForm(DonateResquest $request, $serverSuffix = '')
    {
        try {
            // 伺服器檢查
            $server = ServerModel::where('url_suffix', $serverSuffix)->first();
            if (empty($server)) {
                return response()->json([
                    'message' => '查無伺服器',
                ], 400);
            }

            // 驗證碼
            if (!captcha_check($request->input('code'))) {
                return response()->json([
                    'message' => '驗證碼輸入錯誤或是過期',
                ], 400);
            }

            // 贊助方式
            if ($request->input('product_id') == -1) {
                $amount = $request->input('amount');
                if ($amount <= 0) {
                    return response()->json([
                        'message' => '金額輸入異常',
                    ], 400);
                }
            } else {
                $product = ProductModel::find($request->input('product_id'));
                if (empty($product)) {
                    return response()->json([
                        'message' => '查無贊助方式',
                    ], 400);
                } else {
                    $amount = $product->amount;
                }
            }

            // 付款方式
            if (!in_array($request->input('pay_method'), [2, 3, 4, 5])) {
                return response()->json([
                    'message' => '查無付款方式',
                ], 400);
            } else {
                // 付款方式金額檢查
                switch ($request->input('pay_method')) {
                    case '1':
                        // 信用卡拿掉不使用
                        break;

                    case '2':
                        if ($amount < 30 || $amount > 20000) {
                            return response()->json([
                                'message' => '超商代碼付款金額只能在30~20000元之間',
                            ], 400);
                        }
                        break;

                    case '3':
                        if ($amount < 20 || $amount > 40000) {
                            return response()->json([
                                'message' => '超商條碼付款金額只能在20~40000元之間',
                            ], 400);
                        }
                        break;

                    case '4':
                        if ($amount > 50000) {
                            return response()->json([
                                'message' => 'ATM轉帳金額只能在50000元內',
                            ], 400);
                        }
                        break;

                    case '5':
                        if ($amount > 50000) {
                            return response()->json([
                                'message' => 'WebATM金額只能在50000元內',
                            ], 400);
                        }
                        break;

                    default:
                        return response()->json([
                            'message' => '查無付款功能',
                        ], 400);
                        break;
                }
            }

            // 檢查指定伺服器是否有帳號存在
            Config::set('database.connections.mysql_other.host', $server->sql_ip);
            Config::set('database.connections.mysql_other.port', $server->sql_port);
            Config::set('database.connections.mysql_other.database', $server->sql_database);
            Config::set('database.connections.mysql_other.username', $server->sql_username);
            Config::set('database.connections.mysql_other.password', $server->sql_password);

            $account = DB::connection('mysql_other')
                ->table('accounts')
                ->where('login', $request->input('account'))
                ->first();
            if (empty($account)) {
                return response()->json([
                    'message' => '該伺服器查無此帳號',
                ], 400);
            }

            // 代號區分
            $code = $server->blue_online == 1 ? 'D' : 'T';

            // 紀錄(本地)
            $result = DonateRecordModel::create([
                'server_id' => $server->id,
                'product_id' => $request->input('product_id'),
                'account' => $request->input('account'),
                'number' => $code . date('YmdHis') . rand(0, 9),
                'pay_method' => $request->input('pay_method'),
                'amount' => $amount,
            ]);

            // 呼叫藍新
            $bluenewHtml = (new Bluenew(
                $server->blue_online,
                $server->blue_number,
                $server->blue_hash_key,
                $server->blue_hash_iv
                ))->sendPay([
                    'TimeStamp' => time(),
                    'MerchantOrderNo' => $result->number,
                    'Amt' => $result->amount,
                    'ItemDesc' => '贊助',
                    'CREDIT' => $request->input('pay_method') == 1 ? 1 : 0,
                    'CVS' => $request->input('pay_method') == 2 ? 1 : 0,
                    'BARCODE' => $request->input('pay_method') == 3 ? 1 : 0,
                    'VACC' => $request->input('pay_method') == 4 ? 1 : 0,
                    'WEBATM' => $request->input('pay_method') == 5 ? 1 : 0,
                ], [
                    'notifyURL' => route('receive'),
                    'clientBackURL' => route('index'),
                ]);

            return response()->json([
                'html' => $bluenewHtml
            ]);
        } catch (\Throwable $th) {
            \Log::error('DonateController checkForm()異常', [
                'message' => $th->getMessage(),
                'line' => $th->getLine(),
                'file' => $th->getFile(),
            ]);

            return response()->json([
                'message' => '系統異常,請聯絡管理人員',
            ], 500);
        }
    }

    // [POST] 藍新付款成功回傳
    public function blueCallBack(Request $request)
    {
        try {
            // 判斷參數是否存在
            if ($request->has(['TradeInfo', 'MerchantID', 'Status'])) {
                $server = ServerModel::where('blue_number', $request->input('MerchantID'))->first();
                if (!empty($server)) {
                    $result =(new Bluenew(
                        $server->blue_online,
                        $server->blue_number,
                        $server->blue_hash_key,
                        $server->blue_hash_iv,
                        ))->receiveData($request->input('TradeInfo'));

                    $record = DonateRecordModel::where('number', $result['Result']['MerchantOrderNo'])->first();
                    if (!empty($record)) {
                        $record->status = $result['Status'] == 'SUCCESS' ? 1 : 2;
                        $record->blue_callback = json_encode($result);
                        $record->save();

                        // 由於會有重複藍新帳號,導致伺服器找不到正確的,這邊再重新拉一次
                        $server = ServerModel::find($record->server_id);

                        // 如果成功在寫入遊戲資料庫
                        if ($record->status == 1) {
                            Config::set('database.connections.mysql_other.host', $server->sql_ip);
                            Config::set('database.connections.mysql_other.port', $server->sql_port);
                            Config::set('database.connections.mysql_other.database', $server->sql_database);
                            Config::set('database.connections.mysql_other.username', $server->sql_username);
                            Config::set('database.connections.mysql_other.password', $server->sql_password);

                            // 依照不同資料庫寫法
                            if ($server->sql_payment_table == 'shop_user') {
                                $insertData = [
                                    'account' => $record->account,
                                    'count' => $record->amount,
                                ];
                            } else {
                                $insertData = [
                                    'amount' => $record->amount,
                                    'payname' => $record->account,
                                    'state' => 1,
                                ];
                            }

                            DB::connection('mysql_other')->table($server->sql_payment_table)->insert($insertData);
                        }
                    }
                }
            }
        } catch (\Throwable $th) {
            \Log::error('DonateController blueCallBack()異常', [
                'message' => $th->getMessage(),
                'line' => $th->getLine(),
                'file' => $th->getFile(),
            ]);
        }
    }
}
