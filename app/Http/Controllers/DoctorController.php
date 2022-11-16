<?php

namespace App\Http\Controllers;

use App\Models\Slot;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class DoctorController extends Controller
{
    //
    public function setclinicianavailability(Request $request)
    {
        if (Auth::check()) {
            // return gettype($request->all());
            $slotss = [];
            foreach ($request->all() as $key => $value) {
                if (empty($value)) {
                    continue;
                } else {
                    // $slotss[] = $value;
                        $array = collect($value)
                            ->map(function ($item) use ($request) {
                                return $item + [
                                    'Hospital_Id' => auth()->user()->Hospital_Id,
                                    'User_Id' => auth()->user()->id,
                                    'Createdby_Id' => auth()->user()->id,
                                ];
                            })
                            ->toArray();
                        foreach ($array as $keyy => $valuee) {
                            Slot::create($valuee);
                        }
                }
            }
            return response()->json(
                ['message' => 'Time Availability slot is saved successfully'],
                201
            );
        }
        return response()->json(['message' => 'Unauthorized user'], 401);
    }

    public function getclinicianslotavailability($Doctor_Id)
    {
        return response()->json(
            [
                'data' => Slot::where(
                    'Hospital_Id',
                    '=',
                    auth()->user()->Hospital_Id
                )
                    ->where('User_Id', '=', $Doctor_Id)
                    ->with('owner', 'doneby')
                    ->orderBy('start', 'asc')
                    ->get(),
            ],
            200
        );
    }
}
