<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Support\Facades\DB;

class Sport extends Model
{
    use Sluggable;

    public function sluggable()
    {
        return [
            'slug' => [
                'source' => 'name'
            ]
        ];
    }

    public function tournaments() {
        //return $this->hasMany(Hotel::class, 'hotel_tournament', 'tournament_id', 'hotel_id');
        //return $this->hasMany(Hotel::class)->using('\App\HotelTournament');
        return $this->hasMany('\App\Tournament');
    }

    public function hasOngoingTournaments() {
        return DB::table('tournaments')->where('sport_id', '=', $this->id)->where('status', '=', 1)->count();
    }

    public function activeTournamentCount() {
        return DB::table('tournaments')->where('sport_id', '=', $this->id)->where('end_date', '>=', Carbon::now())->where('status', '=', 1)->count();
    }
}
