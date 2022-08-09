<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Admin\ServerResquest;
use App\Models\ServerModel;
use DB, Config;

class ServerController extends Controller
{
    /**
     * 畫面
     *
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    public function index()
    {
        return view('admin.server');
    }

    /**
     * 取得全部
     *
     * @return \Illuminate\Http\Response|\Illuminate\Contracts\Routing\ResponseFactory
     */
    public function all()
    {
        return response()->json([
            'items' => ServerModel::orderByDesc('created_at')->paginate(15)
        ]);
    }

    /**
     * 新增
     *
     * @param \App\Http\Requests\Admin\ServerResquest $request
     * @return \Illuminate\Http\Response|\Illuminate\Contracts\Routing\ResponseFactory
     */
    public function create(ServerResquest $request)
    {
        ServerModel::create([
            'name' => $request->input('name'),
            'url_suffix' => $request->input('url_suffix'),
            'sql_ip' => $request->input('sql_ip'),
            'sql_port' => $request->input('sql_port'),
            'sql_database' => $request->input('sql_database'),
            'sql_username' => $request->input('sql_username'),
            'sql_password' => $request->input('sql_password'),
            'sql_payment_table' => $request->input('sql_payment_table'),
            'blue_online' => $request->input('blue_online'),
            'blue_number' => $request->input('blue_number'),
            'blue_hash_key' => $request->input('blue_hash_key'),
            'blue_hash_iv' => $request->input('blue_hash_iv')
        ]);

        return response()->json([
            'message' => '新增成功'
        ]);
    }

    /**
     * 更新
     *
     * @param \App\Http\Requests\Admin\ServerResquest $request
     * @param int $id
     * @return \Illuminate\Http\Response|\Illuminate\Contracts\Routing\ResponseFactory
     */
    public function update(ServerResquest $request, $id)
    {
        $item = ServerModel::find($id);
        if (empty($item)) {
            return response([
                'message' => '查無資料'
            ], 400);
        } else {
            $item->name = $request->input('name');
            $item->url_suffix = $request->input('url_suffix');
            $item->sql_ip = $request->input('sql_ip');
            $item->sql_port = $request->input('sql_port');
            $item->sql_database = $request->input('sql_database');
            $item->sql_username = $request->input('sql_username');
            $item->sql_password = $request->input('sql_password');
            $item->sql_payment_table = $request->input('sql_payment_table');
            $item->blue_online = $request->input('blue_online');
            $item->blue_number = $request->input('blue_number');
            $item->blue_hash_key = $request->input('blue_hash_key');
            $item->blue_hash_iv = $request->input('blue_hash_iv');
            $item->save();

            return response()->json([
                'message' => '更新成功'
            ]);
        }
    }

    /**
     * 刪除
     *
     * @param int $id
     * @return \Illuminate\Http\Response|\Illuminate\Contracts\Routing\ResponseFactory
     */
    public function delete($id)
    {
        $item = ServerModel::find($id);
        if (empty($item)) {
            return response([
                'message' => '查無資料'
            ], 400);
        } else {
            $item->delete();

            return response()->json([
                'message' => '刪除成功'
            ]);
        }
    }

    /**
     * 取得單一
     *
     * @param int $id
     * @return \Illuminate\Http\Response|\Illuminate\Contracts\Routing\ResponseFactory
     */
    public function find($id)
    {
        $item = ServerModel::find($id);
        if (empty($item)) {
            return response()->json([
                'message' => '查無資料'
            ], 400);
        } else {
            return response()->json([
                'item' => $item
            ]);
        }
    }

    /**
     * 排序
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response|\Illuminate\Contracts\Routing\ResponseFactory Response
     */
    public function sort(Request $request)
    {
        if (!$request->has(['items'])) {
            return response([
                'message' => '排序更新失敗。'
            ], 400);
        } else {
            DB::update(update_when_case_string('server', 'sort', $request->items));

            return response([
                'message' => '排序更新成功。'
            ]);
        }
    }

    /**
     * DB連線測試
     *
     * @param int $id
     * @return \Illuminate\Http\Response|\Illuminate\Contracts\Routing\ResponseFactory Response
     */
    public function dbConnectTest($id)
    {
        $item = ServerModel::find($id);
        if (empty($item)) {
            return response()->json([
                'message' => '查無資料'
            ], 400);
        } else {
            try {
                Config::set('database.connections.mysql_other.host', $item->sql_ip);
                Config::set('database.connections.mysql_other.port', $item->sql_port);
                Config::set('database.connections.mysql_other.database', $item->sql_database);
                Config::set('database.connections.mysql_other.username', $item->sql_username);
                Config::set('database.connections.mysql_other.password', $item->sql_password);

                $result = DB::connection('mysql_other')->getDatabaseName();
                if (empty($result)) {
                    return response()->json([
                        'message' => '資料庫連線失敗',
                    ], 400);
                } else {
                    return response()->json([
                        'message' => '資料庫連線成功'
                    ]);
                }
            } catch (\Throwable $th) {
                return response()->json([
                    'message' => '資料庫連線異常,錯誤訊息：' . $th->getMessage(),
                ], 500);
            }
        }
    }
}
