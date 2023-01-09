<?php

namespace App\Exports;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Illuminate\Contracts\View\View;
use App\Tournament;

class AccountingDataExport implements FromView, ShouldAutoSize
{
	//FromQuery, WithHeadings, 
	use Exportable;
	
	/*public function forContest(int $contestId)
    {
        $this->contest_id = $contestId;
    }*/
	public function __construct(int $tournament_id)
    {
        $this->tournament_id = $tournament_id;
    }
    
    public function view(): View
    {
	    //$tournament = Tournament::where('id', '=', $this->tournament_id)->firstOrFail();
	    
	    $tournament = Tournament::find($this->tournament_id);
	    
        return view('exports.accounting-data', [
            'tournament' => $tournament
        ]);
    }
	
	 /*public function view(): View
    {
        return view('exports.tournament-orders', [
            //'tournament' => \App\Contest::where('id', '=', $this->contest_id)->firstOrFail()
        ]);
    }*/
    
    /*public function query()
    {
        $tournament = Tournament::where('id', '=', $this->tournament_id)->firstOrFail();

        if ($tournament->tournament_type_id == 2) {

            return DB::table('customer_orders')
            ->join('customers', 'customer_orders.customer_id', '=', 'customers.id')
            ->join('customer_rooms', 'customers.id', '=', 'customer_rooms.customer_id')
            ->join('tournaments', 'customer_orders.tournament_id', '=', 'tournaments.id')
            ->join('hotels', 'customer_orders.hotel_id', '=', 'hotels.id')
            ->join('tournament_hotel_rooms', 'customer_rooms.tournament_hotel_room_id', '=', 'tournament_hotel_rooms.id')
            ->join('hotel_room_types', 'tournament_hotel_rooms.hotel_room_type_id', '=', 'hotel_room_types.id')
            ->select('customers.first_name', 'customers.last_name', 'customers.phone_number', 'customers.email', 'customer_rooms.check_in', 'customer_rooms.check_out', 'hotel_room_types.type', 'tournament_hotel_rooms.gross_price_per_night', 'customer_rooms.quantity', 'customer_rooms.number_of_nights', 'customer_orders.special_request', 'tournaments.name as tournament', 'hotels.name as hotel')
            ->orderBy('customers.first_name', 'ASC')
            ->where('tournaments.id', '=', $this->tournament_id);

        } else {
	    
            return DB::table('customer_orders')
            ->join('customers', 'customer_orders.customer_id', '=', 'customers.id')
            ->join('customer_rooms', 'customers.id', '=', 'customer_rooms.customer_id')
            ->join('team_request_rooms', 'customer_rooms.team_request_room_id', '=', 'team_request_rooms.id')
            ->join('team_requests', 'customers.team_request_id', '=', 'team_requests.id')
            ->join('tournaments', 'customer_orders.tournament_id', '=', 'tournaments.id')
            ->join('hotels', 'customer_orders.hotel_id', '=', 'hotels.id')
            ->join('tournament_hotel_rooms', 'team_request_rooms.tournament_hotel_room_id', '=', 'tournament_hotel_rooms.id')
            ->join('hotel_room_types', 'tournament_hotel_rooms.hotel_room_type_id', '=', 'hotel_room_types.id')
            ->select('customers.first_name', 'customers.last_name', 'customers.phone_number', 'customers.email', 'customer_rooms.check_in', 'customer_rooms.check_out', 'hotel_room_types.type', 'tournament_hotel_rooms.gross_price_per_night', 'customer_rooms.quantity', 'customer_rooms.number_of_nights', 'customer_orders.special_request', 'tournaments.name as tournament', 'hotels.name as hotel', 'team_requests.team_name', 'team_requests.contact_first_name', 'team_requests.contact_last_name', 'team_requests.phone_number as manager_phone_number', 'team_requests.email as manager_email')
            ->orderBy('customers.first_name', 'ASC')
            ->where('tournaments.id', '=', $this->tournament_id);

        }
	    
    }
    
    public function headings(): array 
    {
        $tournament = Tournament::where('id', '=', $this->tournament_id)->firstOrFail();

        if ($tournament->tournament_type_id == 2) {

            return ["First Name", "Last Name", "Phone", "Email", "Arrival Date", "Departure Date", "Room Type", "Room Rate", "Number of Rooms", "Number of Nights", "Special Request", "Tournament", "Hotel"];
        
        } else {
        
            return ["First Name", "Last Name", "Phone", "Email", "Arrival Date", "Departure Date", "Room Type", "Room Rate", "Number of Rooms", "Number of Nights", "Special Request", "Tournament", "Hotel", "Team", "Team Manager First Name", "Team Manager Last Name", "Team Manager Phone Number", "Team Manager Email"];
        
        }  
    }*/
    
}