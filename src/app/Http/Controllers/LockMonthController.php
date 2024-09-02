<?php

namespace App\Http\Controllers;

use App\Models\LockMonth;
use Illuminate\Support\Facades\Response;
use Illuminate\Http\Request;

class LockMonthController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    //管理側からロックされた月を取得後チェックボックスに反映するindex()
    public function index()
    {
        // DBからデータを取得
        $lockMonths = LockMonth::select('year', 'month')->get();

        // 現在のyearSelectの値を取得
        $currentYear = request('year');

        // フィルタリングして該当する月にチェックをつける
        $filteredLockMonths = $lockMonths->filter(function ($lockMonth) use ($currentYear) {
            return $lockMonth->year == $currentYear;
        })->pluck('month');

        return Response::json([
            'status' => 'success',
            'message' => 'Lock months retrieved successfully',
            'data' => $filteredLockMonths
        ], 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    //　管理側からロック月を追加するstore()
    public function store(Request $request)
    {
        // バリデーション
        $request->validate([
            'year' => 'required|string',
            'month' => 'required|string',
        ]);

        // lockMonthカラムに挿入
        $lockMonth = new LockMonth();
        $lockMonth->year = $request->input('year');
        $lockMonth->month = $request->input('month');
        $lockMonth->save();

        // レスポンスの作成
        return Response::json([
            'status' => 'success',
            'message' => 'Lock month saved successfully',
            'data' => $lockMonth
        ], 200);
    }

    /**
     * Display the specified resource.
     */
    // ユーザー側からのtitle(2024年⚪︎月)で照合後booleanを返すshow()
    public function show(Request $request, LockMonth $lockMonths)
    {
        // バリデーション
        $request->validate([
            'title' => 'required|string',
        ]);

        // リクエストからタイトルを取得
        $title = $request->title;

        // Model内のgetLockMonths()関数でtitleに合わせた形でロック月を取得
        $lockMonths = LockMonth::getLockMonths();

        // titleがロック月に含まれているか確認
        $titleExists = in_array($title, $lockMonths);

        return Response::json([
            'status' => 'success',
            'message' => 'Lock months retrieved successfully',
            // 'data' => $lockMonths,
            'titleExists' => $titleExists
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(LockMonth $lockMonth)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, LockMonth $lockMonth)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    // 管理側からロック月を削除するdelete()
    public function delete(Request $request)
    {
        // バリデーション
        $validated = $request->validate([
            'year' => 'required|string',
            'month' => 'required|string',
        ]);

        // データの削除処理
        $lockMonth = LockMonth::where('year', $validated['year'])
            ->where('month', $validated['month'])
            ->first();

        if ($lockMonth) {
            $lockMonth->delete();
            return Response::json([
                'status' => 'success',
                'message' => 'Lock month deleted successfully'
            ], 200);
        } else {
            return Response::json([
                'status' => 'error',
                'message' => 'Lock month not found'
            ], 404);
        }
    }
}
