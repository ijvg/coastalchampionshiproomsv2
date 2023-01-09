<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class CustomerRoom extends Model
{

    protected $fillable = [
        'customer_id',
        'tournament_hotel_room_type_id',
        'quantity',
        'check_in',
        'check_out',
        'number_of_nights',
        'team_request_room_id',
        'tournament_hotel_room_id'
    ];
    
    /*public function hotelTournament () {
        return $this->hasMany(\App\HotelTournament::class);
    }

    public function tournaments () {
        return $this->belongsToMany(Tournament::class, 'hotel_tournament', 'hotel_id', 'tournament_id');
    }*/

    /*public function getHotel () {
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
    }*/
}
