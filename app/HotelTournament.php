<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Support\Facades\DB;

class HotelTournament extends Pivot


   
/*use Illuminate\Database\Eloquent\Model;


class HotelTournament extends Model*/
{

    protected $fillable = [
        'team_link_expiration',
        'is_late',
        'date_late_request_start',
        'min_nights_stay',
        'amenities',
        'cancellation_policy',
        'custom_nights_stay'
    ];

    /*public function hotel() {
        return $this->belongsTo(\App\Hotel::class);
    }

    public function tournament() {
        return $this->belongsTo(\App\Tournament::class);
    }*/
    /*protected $fillable = [
        'contest_category_id',
        'nomination_id'
    ];*/


	public function getTotalPaid() {
		$totalPaid = DB::table('customer_orders')->where('tournament_id', '=', $this->tournament_id)->where('hotel_id', '=', $this->hotel_id)->where('canceled', '=', 0)->sum('total_paid');
		return $totalPaid;
	}
	
	public function getTotalRoomRatesPaid() {
		$totalTaxes = DB::table('customer_orders')->where('tournament_id', '=', $this->tournament_id)->where('hotel_id', '=', $this->hotel_id)->where('canceled', '=', 0)->sum('hotel_taxes');
		$totalTransactionFees = DB::table('customer_orders')->where('tournament_id', '=', $this->tournament_id)->where('hotel_id', '=', $this->hotel_id)->where('canceled', '=', 0)->sum('transaction_flat_fee');
		$totalTransactionPercentFee = DB::table('customer_orders')->where('tournament_id', '=', $this->tournament_id)->where('hotel_id', '=', $this->hotel_id)->where('canceled', '=', 0)->sum('transaction_percent_fee');
		$totalHotelFlatFee = DB::table('customer_orders')->where('tournament_id', '=', $this->tournament_id)->where('hotel_id', '=', $this->hotel_id)->where('canceled', '=', 0)->sum('hotel_flat_fee');
		
		$totalPaid = DB::table('customer_orders')->where('tournament_id', '=', $this->tournament_id)->where('hotel_id', '=', $this->hotel_id)->where('canceled', '=', 0)->sum('total_paid');
		
		$totalRoomRatesPaid = $totalPaid - $totalTaxes;
		
		$totalRoomRatesPaid = $totalRoomRatesPaid - $totalTransactionFees;
		
		$totalRoomRatesPaid = $totalRoomRatesPaid - $totalTransactionPercentFee;
		
		$totalRoomRatesPaid = $totalRoomRatesPaid - $totalHotelFlatFee;
		
		return $totalRoomRatesPaid;
	}
	
	public function getTotalPercentCreditCardFees() {
		$totalPercentCreditCardFees = DB::table('customer_orders')->where('tournament_id', '=', $this->tournament_id)->where('hotel_id', '=', $this->hotel_id)->where('canceled', '=', 0)->sum('transaction_percent_fee');
		return $totalPercentCreditCardFees;
	}
	
	public function getTotalHotelFlatFee() {
		$totalHotelFlatFee = DB::table('customer_orders')->where('tournament_id', '=', $this->tournament_id)->where('hotel_id', '=', $this->hotel_id)->where('canceled', '=', 0)->sum('hotel_flat_fee');
		return $totalHotelFlatFee;
	}
	
	public function getTotalTransactionFees() {
		$totalHotelFlatFee = DB::table('customer_orders')->where('tournament_id', '=', $this->tournament_id)->where('hotel_id', '=', $this->hotel_id)->where('canceled', '=', 0)->sum('transaction_flat_fee');
		return $totalHotelFlatFee;
	}
	
	public function getTotalHotelTaxes() {
		$totalTaxes = DB::table('customer_orders')->where('tournament_id', '=', $this->tournament_id)->where('hotel_id', '=', $this->hotel_id)->where('canceled', '=', 0)->sum('hotel_taxes');
		return $totalTaxes;
	}

    public function tournamentHotelRooms() {
        //return $this->hasMany(TournamentHotelRoom::class);
        //return $this->hotel_id;
        //$hotelRooms = DB::table('tournament_hotel_rooms')->where('hotel_id', '=', $this->hotel_id)->where('tournament_id', '=', $this->tournament_id)->leftJoin('hotel_room_types', 'hotel_room_types.id', '=', 'tournament_hotel_rooms.hotel_room_type_id')->get();
        $hotelRooms = TournamentHotelRoom::select('tournament_hotel_rooms.*', 'hotel_room_types.type')->where('hotel_id', '=', $this->hotel_id)->where('tournament_id', '=', $this->tournament_id)->join('hotel_room_types', 'hotel_room_types.id', '=', 'tournament_hotel_rooms.hotel_room_type_id')->get();
        return $hotelRooms;
    }

    public function hotel() {
        //return $this->belongsToMany(Hotel::class);
        //return $this->belongsToMany(Hotel::class)->using(TournamentHotel::class);
        return $this->belongsTo(Hotel::class);
    }

    public function tournament() {
        //return $this->belongsToMany(Tournament::class);
        return $this->belongsTo(Tournament::class);
    }

    public function roomsNotUsedTournamentHotel() {
        $ids = DB::table('tournament_hotel_rooms')->where('tournament_id', '=', $this->tournament_id)->where('hotel_id', '=', $this->hotel_id)->pluck('hotel_room_type_id');
        return \App\HotelRoomType::whereNotIn('id', $ids)->get();
    }

    public function teamRequests() {
        return $this->hasMany(TeamRequest::class);
    }
    
    /* public function hotelTournamentRooms() {
        //return $this->hasMany(TournamentHotelRoom::class);

        return DB::table('tournament_hotel_rooms')->where('tournament_id', '=', $this->tournament_id)->where('hotel_id', '=', $this->hotel_id)->get();
    }*/
    
    public function hotelTournamentRooms() {

        return DB::table('tournament_hotel_rooms')
        ->join('hotel_room_types', 'tournament_hotel_rooms.hotel_room_type_id', '=', 'hotel_room_types.id')
        ->select('tournament_hotel_rooms.*', 'hotel_room_types.type')
        ->where('tournament_hotel_rooms.tournament_id', '=', $this->tournament_id)->where('tournament_hotel_rooms.hotel_id', '=', $this->hotel_id)
        ->get();
    }

    
    public function tournamentHotelTeamRequests() {
        return DB::table('team_requests')->where('tournament_id', '=', $this->tournament_id)->where('hotel_id', '=', $this->hotel_id)->get();
    }
}