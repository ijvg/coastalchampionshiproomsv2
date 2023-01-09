<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class TeamRequestRoom extends Model
{

    protected $fillable = [
        'team_request_id',
        'tournament_hotel_room_id',
        'rooms_requested',
        'default_nights'
    ];
    
    /*public function hotelTournament () {
        return $this->hasMany(\App\HotelTournament::class);
    }

    public function tournaments () {
        return $this->belongsToMany(Tournament::class, 'hotel_tournament', 'hotel_id', 'tournament_id');
    }*/

    public function teamRequest() {
        return $this->belongsTo(TeamRequest::class);
    }

    public function tournamentHotelRoom() {
        return $this->belongsTo(TournamentHotelRoom::class);
    }

    /*public function hotelRoomType() {
        return $this->hasOne(HotelRoomType::class, 'id', 'hotel_room_type_id');
    }*/

    public function getTournamentHotelRoom() {
        return DB::table('tournament_hotel_rooms')->where('id', '=', $this->tournament_hotel_room_id)->get();
    }
}
