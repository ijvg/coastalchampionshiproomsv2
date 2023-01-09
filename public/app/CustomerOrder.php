<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class CustomerOrder extends Model
{

    protected $fillable = [
        'customer_id',
        'tournament_id',
        'hotel_id',
        'team_request_id',
        'refunded',
        'confirmation_number',
        'total',
        'remaining',
        'hotel_taxes',
        'hotel_flat_fee',
        'booking_fee',
        'transaction_flat_fee',
        'transaction_percent_fee',
        'order_date'
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
