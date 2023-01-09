<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class TournamentHotelRoom extends Model
{
    
    protected $fillable = [
        'hotel_id',
        'tournament_id',
        'hotel_room_type_id',
        'gross_price_per_night',
        'net_price_per_night',
        'quantity',
        'rooms_available',
        'held',
        'allocated'
    ];

    public function hotelTournament () {
        //return $this->hasMany(\App\HotelTournament::class);
        return $this->belongsToMany(\App\HotelTournament::class);
    }


    public function hotel () {
        return $this->belongsToMany(Hotel::class);
    }

    public function getHotel () {
        return Hotel::find($this->hotel_id);
    }

    public function tournament () {
        return $this->belongsToMany(Tournament::class);
    }

    public function hotelRoomType() {
        return $this->belongsTo(HotelRoomType::class);
    }

    public function availableRoomTypes () {
        $ids = DB::table('hotel_tournament')->where('tournament_id', '=', $this->id)->pluck('hotel_id');
        return \App\Hotel::whereNotIn('id', $ids)->get();
    }

    public function roomType () {
        return DB::table('hotel_room_types')->where('id', '=', $this->tournament_hotel_room_id)->get();
    }

    public function teamRequestRooms () {
        return $this->hasMany(TeamRequestRoom::class);
    }

    /*
    public function tournaments () {
        return $this->belongsToMany(Tournament::class, 'hotel_tournament', 'hotel_id', 'tournament_id');
    }*/
}
