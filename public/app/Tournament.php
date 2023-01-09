<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Facades\DB;

use App\Hotel;


class Tournament extends Model
{

    protected $fillable = [
        'name','start_date','end_date','description'
     ];

    /*public function hotelTournaments () {
        return $this->hasMany('\App\HotelTournament', 'tournament_id');
    }*/


    public function hotels() {
        //return $this->hasMany(Hotel::class, 'hotel_tournament', 'tournament_id', 'hotel_id');
        //return $this->hasMany(Hotel::class)->using('\App\HotelTournament');
        //return $this->hasManyThrough('\App\Hotel', '\App\HotelTournament', 'id', 'id', 'id', 'hotel_id');

        //return $this->hasMany(Hotel::class, 'hotel_tournament', 'hotel_id', 'tournament_id');


        //return $this->belongsToMany(Hotel::class);
        return $this->belongsToMany(Hotel::class)->using(HotelTournament::class)->withPivot(['min_nights_stay', 'amenities', 'cancellation_policy', 'custom_nights_stay']);
    }
    

    public function sport() {
        return $this->belongsTo('\App\Sport');
    }

    public function availableHotels() {
        $ids = DB::table('hotel_tournament')->where('tournament_id', '=', $this->id)->pluck('hotel_id');
        return \App\Hotel::whereNotIn('id', $ids)->get();
    }

    public function hotelTournament() {
        return $this->hasMany(HotelTournament::class);
    }

    public function teamRequests() {
        return $this->hasMany(TeamRequest::class);
    }

    /*public function hotel () {
        return $this->hasMany('\App\Hotel')
    }*/
}
