<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use App\Models\SessionTime;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ScheduleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $session_times = SessionTime::getSessionTimes();
        $users = User::get();

        return view('admin.home', compact('users', 'session_times'));
        // return view('admin.home')->with(
        //     ['session_times' => $session_times]
        // );
    }

    public function getEventsForPeriod(Request $request)
    {
        // バリデーション
        $request->validate([
            'start_date' => 'required|integer',
            'end_date' => 'required|integer'
        ]);

        // カレンダー表示期間
        $start_date = date('Y-m-d', $request->input('start_date') / 1000);
        $end_date = date('Y-m-d', $request->input('end_date') / 1000);

        $schedules = Schedule::select(
            'date',
            'text as title',
        )
            ->whereBetween('date', [$start_date, $end_date])
            ->get();

        return $schedules;
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
    public function store(Request $request)
    {
        // バリデーション
        $request->validate([
            'date' => 'required|string',
            'user' => 'required|string',
            'session_time' => 'required|string',
            'text' => 'required|max:32',
        ]);

        //textカラムに挿入
        $schedule = new Schedule();
        //requestを結合してtextカラムに挿入
        $schedule->uuid = (string)Str::uuid();
        $schedule->date = $request->input('date');
        $text = $request->input('session_time') . $request->input('user') . $request->input('text');
        $schedule->text = $text;
        $schedule->save();

        return redirect()->route('admin.home');
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Schedule $schedule)
    {
        //
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Schedule $schedule)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Schedule $schedule)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Schedule $schedule)
    {
        //
    }
}
