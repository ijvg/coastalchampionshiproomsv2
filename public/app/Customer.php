<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class Customer extends Model
{
    protected $fillable = [
        'team_request_id',
        'first_name',
        'last_name',
        'phone_number',
        'email',
        'ccv_id'
    ];
    /* Now in Customer Order
        'deposit_amount',
        'remaining_amount',
        'refunded'
        */

    public function getHotel() {
        $customerRoom = CustomerRoom::where("customer_id", "=", $this->id)->firstOrFail();
        
        $customerOrder = CustomerOrder::where("customer_id", "=", $this->id)->firstOrFail();
        
        $tournament = Tournament::where("id", "=", $customerOrder->tournament_id)->firstOrFail();
        
        $hotel;
        
        if ($tournament->tournament_type_id == 2) {
	        $hotel = Hotel::find($customerOrder->hotel_id);
	        
	        //return $hotel;
        } else {
	        $teamRequestRoom = TeamRequestRoom::find($customerRoom->team_request_room_id);

	        $teamRequest = TeamRequest::find($teamRequestRoom->team_request_id);
	
	        $hotel = $teamRequest->getHotel();
        }

        return $hotel;
    }

    public function getTournament() {
        $customerRoom = CustomerRoom::where("customer_id", "=", $this->id)->firstOrFail();
        
        $customerOrder = CustomerOrder::where("customer_id", "=", $this->id)->firstOrFail();

        //$teamRequestRoom = TeamRequestRoom::find($customerRoom->team_request_room_id);

        //$teamRequest = TeamRequest::find($teamRequestRoom->team_request_id);
        
        $tournament = Tournament::find($customerOrder->tournament_id);

        return $tournament;
    }

}
