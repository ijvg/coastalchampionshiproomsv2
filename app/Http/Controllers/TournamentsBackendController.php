<?php

namespace App\Http\Controllers;

use App\HotelTournament;
use App\Tournament;
use App\Hotel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Log;


class TournamentsBackendController extends \TCG\Voyager\Http\Controllers\VoyagerBaseController
{
    
    public function tournamentManagerSelect() {
        $tournaments = \App\Tournament::all();

        return view('/vendor/voyager/tournament-manager-select', compact('tournaments'));
    }

    public function tournamentManager($slug) {

        $tournament = \App\Tournament::where('slug', '=', $slug)->firstOrFail();

        $hotels = \App\Hotel::all();

        $tournamentHotelRooms = \App\TournamentHotelRoom::where('tournament_id', '=', $tournament->id)->get();

        $hotelsNotIn = $tournament->availableHotels();
        //$hotelsNotIn = "";

        //$tournamentHotelRooms = 

        //, compact('tournaments')
        return view('/vendor/voyager/tournament-manager', compact('tournament', 'hotels', 'tournamentHotelRooms', 'hotelsNotIn'));
    }

    public function setTeamExpireLinkDefaultDate(Request $request) {
    
        $tournament = Tournament::findOrFail($request->get('tID'));
    
        $tournament->default_team_link_expiration = $request->get('lateDateRequestStart');
    
        $tournament->save();
    
        $response = array(
          "message" => "Successfully updated Default Team Link Expire Date"
       );
    
       echo json_encode($response);
       exit;
    
    }
    
    public function saveHotelTournamentData(Request $request) {
	    
	    $tournament = Tournament::findOrFail($request->input('tournament_id'));
	    
	    Log::info($request->input('hotel_id'));
	    
	    $hotelId = $request->input('hotel_id');
	    
	    $hotel = \App\Hotel::findOrFail($request->input('hotel_id'));
	    
	    
	    //$hotelTournamnet = DB::table('hotel_tournament')->where('tournament_id', '=', $request->input('tournament_id'))->where('hotel_id', '=', $request->input('hotel_id'))->get();
	    
	    /*$hotelTournament->amenities = "" . $request->input('amenities');
	    
	    $hotelTournament->cancellation_policy = "" . $request->input('cancellationPolicy');
	    
	    $hotelTournament->min_nights_stay = 0 + $request->input('minNightsStay');
	    
	    $hotelTournament->save();*/

        Log::info($request->input('customNights') . ' yo');
	    
	    $tournament->hotels()->updateExistingPivot($hotelId, ['custom_nights_stay' => $request->input('customNights'), 'min_nights_stay' => $request->input('minNightsStay'), 'cancellation_policy' => $request->input('cancellationPolicy'), 'amenities' => $request->input('amenities')]);

		return back()->with(['message' => "Tournament Hotel Information Saved", 'alert-type' => 'success']);
    }

}