<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Tournament;
use App\Hotel;

class HotelsController extends Controller
{

    public function removeFromTournament(Request $request) {
        $tournament = Tournament::find($request->input('tournament_id'));

        $removedHotel = Hotel::find($request->input('hotel_id'));

        $tournament->hotels()->detach($removedHotel->id);

        /*$hotel = array('id' => $removedHotel->id, 'name' => $removedHotel->name);

        $hotel = json_encode($hotel);
        //$hotel = Hotel::find($request->hotel_id);

        $data = [
            'success' => true,
            'message'=> 'Hotel Removed from Tournament',
            'removed_hotel' => $hotel
          ];
        //$hotels
        return response()->json($data);*/
        return back()->with(['message' => "Hotel Removed from Tournament", 'alert-type' => 'success']);
        //return back()->with('alertSuccess', 'Hotel Removed from Tournament');
    }

    public function assignToTournament(Request $request)
    {
        $tournament = Tournament::find($request->input('tournament_id'));

        $hotels = array();
        //$tournament->hotels()->sync($request->hotel_ids);
        foreach ($request->input('hotel_ids') as $hotel_id) {
            $hotel = Hotel::find($hotel_id);

            array_push($hotels, ['id'=>$hotel->id, 'name'=>$hotel->name]);
        

            
            $tournament->hotels()->attach($hotel_id);
            
        }
        $tournament->save();

        /*$hotels = json_encode($hotels);
        //$hotel = Hotel::find($request->hotel_id);

        $data = [
            'success' => true,
            'message'=> 'Hotel Added to Tournament',
            'added_hotels' => $hotels
          ];
        //$hotels
            return response()->json($data);*/
            return back()->with(['message' => "Hotel Added to Tournament", 'alert-type' => 'success']);
            //return back()->with('alertSuccess', 'Hotel Added to Tournament');
    }
}
