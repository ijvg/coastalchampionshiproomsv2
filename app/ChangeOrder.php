<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class ChangeOrder extends Model
{

    protected $fillable = [
        'confirmation_number',
        'changes',
        'tournament_id',
        'hotel_id'
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
