<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class TeamRequest extends Model
{

    protected $fillable = [
        'team_name',
        'contact_first_name',
        'contact_last_name',
        'phone_number',
        'email',
        'hotel_id',
        'tournament_id',
        'description',
        'check_in',
        'check_out',
        'number_of_nights',
        'uuid'
    ];
    
    /*public function hotelTournament () {
        return $this->hasMany(\App\HotelTournament::class);
    }

    public function tournaments () {
        return $this->belongsToMany(Tournament::class, 'hotel_tournament', 'hotel_id', 'tournament_id');
    }*/
    
    public function getHotelTournament() {
	    $hotelTournament = DB::table('hotel_tournament')->where('tournament_id', '=', $this->tournament_id)->where('hotel_id', '=', $this->hotel_id)->get();
	    return $hotelTournament;
    }

    public function getHotel () {
        return Hotel::find($this->hotel_id);
    }

    public function teamRequestRooms () {
        return $this->hasMany(TeamRequestRoom::class);
    }

    public function hotelTournament () {
        return $this->belongsTo(Hotel::class);
    }

    public function tournament () {
        return $this->belognsTo(Tournament::class);
    }

    public function totalTeamRoomsAvailable() {
        $teamRequestRooms = DB::table('team_request_rooms')->where('team_request_id', '=', $this->id)->get();
        $totalRoomsAvailable = 0;

        foreach ($teamRequestRooms as $teamRequestRoom) {
            $totalRoomsAvailable = $totalRoomsAvailable + $teamRequestRoom->available;
        }

        return $totalRoomsAvailable;

    }
}
