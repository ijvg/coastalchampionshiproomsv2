<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use App\Tournament;


class Hotel extends Model
{
    
    /*public function hotelTournament () {
        return $this->hasMany(\App\HotelTournament::class);
    }*/

    public function tournament () {
        //return $this->belongsToMany(Tournament::class, 'hotel_tournament', 'hotel_id', 'tournament_id');

        return $this->belongsToMany(Tournament::class);
    }

    public function hotelRooms() {
        return $this->hasMany(TournamentHotelRoom::class);
        //return $this->hasManyThrough(TournamentHotelRoom::class, Tournament::class, )
        
    }
    
    public function tournaments() {
        return $this->belongsToMany(Tournament::class)->using(TournamentHotel::class);
    }

    public function hotelTournament() {
        return $this->hasMany(HotelTournament::class);
    }

    public function teamRequests() {
        return $this->hasMany(TeamRequest::class);
    }
}
