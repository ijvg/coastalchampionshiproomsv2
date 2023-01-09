<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class HotelRoomType extends Model
{
    
    public function tournamentHotelRoom() {
        return $this->belongsTo(TournamentHotelRoom::class);
    }

    public function TeamRequestRoom() {
        return $this->belongsTo(TeamRequestRoom::class);
    }
}
