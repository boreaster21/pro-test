<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ReservationRequest;
use App\Models\Reservation;
use App\Models\Store;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

class ReservationController extends Controller
{

    public function store(ReservationRequest $request)
    {
        $validated = $request->validated();

        $reservationDateTime = Carbon::parse($validated['reservation_date'] . ' ' . $validated['reservation_time']);

        Reservation::create([
            'user_id' => Auth::id(),
            'store_id' => $validated['store_id'],
            'reservation_datetime' => $reservationDateTime,
            'number_of_people' => $validated['number_of_people'],
        ]);

        return redirect()->route('stores.show', $validated['store_id'])
                         ->with('success', '予約が完了しました。');
    }
}