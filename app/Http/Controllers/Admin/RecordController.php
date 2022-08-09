<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Admin\ServerResquest;
use App\Models\DonateRecordModel;
use App\Models\ServerModel;

class RecordController extends Controller
{
    /**
     * 畫面
     *
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    public function index()
    {
        return view('admin.record');
    }

    /**
     * 取得全部
     *
     * @param Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response|\Illuminate\Contracts\Routing\ResponseFactory
     */
    public function all(Request $request)
    {
        $is_search = false;
        $query = DonateRecordModel::with('server', 'product')->orderByDesc('created_at');

        if ($request->filled('server_id')) {
            $is_search = true;
            $query = $query->where('server_id', $request->input('server_id'));
        }

        if ($request->filled('account')) {
            $is_search = true;
            $query = $query->where('account', 'LIKE', '%' . $request->input('account') . '%');
        }

        return response()->json([
            'items' => $query->paginate(15),
            'servers' => ServerModel::orderByDesc('created_at')->get(),
            'is_search' => $is_search
        ]);
    }

    /**
     * 取得單一
     *
     * @param int $id
     * @return \Illuminate\Http\Response|\Illuminate\Contracts\Routing\ResponseFactory
     */
    public function find($id)
    {
        $item = DonateRecordModel::with('server', 'product')->find($id);
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
}
