<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Tournament;
use App\Hotel;
use App\TournamentHotelRoom;

class RoomsController extends Controller
{
    public function assignToTournament(Request $request) {


        foreach ($request->input('room_type_ids') as $room_type_id) {

            $hoteRoom = array();

            $hotelRoom['tournament_id'] = $request->input('tournament_id');
            $hotelRoom['hotel_id'] = $request->input('hotel_id');
            $hotelRoom['hotel_room_type_id'] = $room_type_id;

            $assignedRoom = TournamentHotelRoom::create($hotelRoom);

        }

        return back()->with(['message' => "Room Added to Tournament Hotel", 'alert-type' => 'success']);

    }

    public function deleteTournamentHotelRoom(Request $request) {
        $deleteRoom = TournamentHotelRoom::where('id', '=', $request->input('tournament_hotel_room_id'));
        $deleteRoom->delete();

        return back()->with(['message' => "Room deleted from Tournament Hotel", 'alert-type' => 'success']);
    }

    public function updateTournamentHotelRoom(Request $request) {

        $hotelRoomID = $request->input('tournament_hotel_room_id');

        $updatedRoom = TournamentHotelRoom::find($hotelRoomID);

        $hotel = Hotel::find($updatedRoom->hotel_id);

        $updatedRoomPreQuantity = $updatedRoom->quantity;
        
        $updatedRoomNewQuantity = $request->input('quantity');

        $updatedRoomNewAvailable = $updatedRoomNewQuantity - $updatedRoomPreQuantity;

        $updatedRoomNewAvailable = $updatedRoom->rooms_available + $updatedRoomNewAvailable;

        $grossPricePerNight = $request->input('pricePerNight');

        $hotelRebate = $hotel->flat_rebate;

        $commission = ($grossPricePerNight - $hotelRebate) * $hotel->commission_rate;

        $netPricePerNight = ($grossPricePerNight - $hotelRebate) - $commission;

        $updatedRoomData = array();

        
        $updatedRoomData['quantity'] = $request->input('quantity');
        $updatedRoomData['gross_price_per_night'] = $grossPricePerNight;
        $updatedRoomData['net_price_per_night'] = $netPricePerNight;
        $updatedRoomData['rooms_available'] = $updatedRoomNewAvailable;



        $updatedRoom->update($updatedRoomData);

        return back()->with(['message' => "Tournament Hotel Room updated", 'alert-type' => 'success']);
    }
}
