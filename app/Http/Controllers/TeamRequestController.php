<?php

namespace App\Http\Controllers;

use App\Customer;
use App\CustomerOrder;
use App\CustomerRoom;
use App\TeamRequest;
use App\TournamentHotelRoom;
use App\TeamRequestRoom;
use App\Tournament;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

use App\Mail\SendMail;
use Mail;

define("APPROVED", 1);
define("DECLINED", 2);
define("ERROR", 3);

class TeamRequestController extends Controller
{
	
	public function disableEnableHotelTournamentRoomType(Request $request) {
		
		//Log::info(json_decode($request));
		$hotelTournamentRoomID = $request->get('tournamentHotelRoomId');
		$status = $request->get('status');
		
		
		$hotelTournamentRoom = TournamentHotelRoom::find($hotelTournamentRoomID);
		
		
		$$hotelTournamentRoom->disabled = $status;
		
		
		$response = array(
            "message" => 'Updated Hotel Room Type Status'
        );

        echo json_encode($response);
		return; 
	}
	
    public function teamRequestMananger($slug) {    
        $tournament = \App\Tournament::where('slug', '=', $slug)->firstOrFail();

        $hotels = \App\Hotel::all();

        $tournamentHotelRooms = \App\TournamentHotelRoom::where('tournament_id', '=', $tournament->id)->get();

        //$hotelsNotIn = $tournament->availableHotels();
        //$hotelsNotIn = "";

        //$tournamentHotelRooms = 

        //, compact('tournaments')
        return view('/vendor/voyager/team-request-manager', compact('tournament', 'hotels', 'tournamentHotelRooms'));
    }

    public function teamMemberRequestSubmission(Request $request) {

        //$requestSub = $request;

    
        //return back()->with(['message' => "Room Added to Tournament Hotel", 'alert-type' => 'success']);

        //$teamUUID = $request->query('tuuid');

        /*$teamRequest = \App\TeamRequest::where('id', '=', $request->input('trid'))->firstOrFail();

        $tournament = \App\Tournament::where('id', '=', $request->input('tid'))->firstOrFail();

        $hotels = \App\Hotel::all();

        $tournamentHotelRooms = \App\TournamentHotelRoom::where('tournament_id', '=', $tournament->id)->get();

        //$states = $this->getStates();
        $formData = $request;

        $status = "yes";
        
        return view('/tournament-team-members-form', compact('tournament', 'hotels', 'tournamentHotelRooms', 'teamRequest', 'states', 'status', 'formData'));*/

        $gw = new gwapi;
        $gw->setLogin("CvH82Yys2vGcvrX2974c9zF8852Cqad8");
        $gw->setBilling($request->get('firstName'),$request->get('lastName'),"",$request->get('address'),"", $request->get('city'),
            $request->get('state'),$request->get('zip'),"US",$request->get('phone'),"",$request->get('email'),"");

            /*$gw->setBilling("John","Smith","Acme, Inc.","123 Main St","Suite 200", "Beverly Hills",
        "CA","90210","US","555-555-5555","555-555-5556","support@example.com",
        "www.example.com");*/
                
        //$gw->setVaultInfo($request->input('ccnumber'), $request->input('ccexp'));
       // $gw->setShipping("Mary","Smith","na","124 Shipping Main St","Suite Ship", "Beverly Hills",
                //"CA","90210","US","support@example.com");
        //$gw->setOrder("1234","Big Order",1, 2, "PO1234","65.192.14.10");
    
        //$r = $gw->doSale("50.00","4111111111111111","1010");
    
        //$gw->doStoreInVault($request->input('ccnumber'), $request->input('ccexp'));

		//$tournament = Tournament::find($request->get('tournamentID'));
        //$teamRequest = TeamRequest::find($request->get('teamRequestID'));

        $gw->doStoreInVault($request->get('tok'));

        //Log::info($gw->responses['response']);

        $cvID = $gw->responses['customer_vault_id'];

        $customerRoomsArray = array();

        //$ccvResponse = $gw->responses['responsetext'];

        if ($gw->responses['response'] == 1) {

            $roomsRequestedData = json_encode($request->get('roomsRequested'));

            $roomsRequestedData = json_decode($roomsRequestedData);

            /* NEED DEPOSIT AMOUNT & REMAINING AMOUNT (TOTAL - DEPOSIT) */

            $customerData = array(
                "team_request_id" => $request->get('teamRequestID'),
                "first_name" => $request->get('firstName'),
                "last_name" => $request->get('lastName'),
                "phone_number" => $request->get('phone'),
                "email" => $request->get('email'),
                "ccv_id" => $cvID
            );

            $customer = Customer::create($customerData);

            $confirmationNumberTester = false;
            $confirmation_number = 0;

            while($confirmationNumberTester == false) {
                $confirmation_number = uniqid();

                $confirmationNumberTester = $this->testConfirmationNumber($confirmation_number);
            }

            $totalTax = number_format((float)round( $request->get("totalTax"),2, PHP_ROUND_HALF_DOWN),2,'.','');
            $totalCost = number_format((float)round( $request->get("totalCost"),2, PHP_ROUND_HALF_DOWN),2,'.','');

            $siteTransactionPercent = number_format((float)round($request->get('siteTransactionPercent'),2, PHP_ROUND_HALF_DOWN),2,'.','');

           
            $customerOrderData = array(
                "customer_id" => $customer->id,
                "tournament_id" => $request->get('tournamentID'),
                "hotel_id" => $request->get('hotelID'),
                "special_request" => $request->get('specialRequest'),
                "refunded" => '0',
                "confirmation_number" => $confirmation_number,
                "team_request_id" => $request->get('teamRequestID'),
                "total" => $totalCost,
                "total_paid" => '0.00',
                "deposit_amount" => '0.00',
                "refunded_amount" => '0.00',
                "remaining" => $totalCost,
                "hotel_taxes" => $totalTax,
                "hotel_flat_fee" => $request->get("hotelFee"),
                "booking_fee" => '0.00',
                "transaction_flat_fee" => '5.00',
                "transaction_percent_fee" => $siteTransactionPercent,
                "order_date" => Carbon::now()
            );

            $customerOrder = CustomerOrder::create($customerOrderData);

            

            foreach ($roomsRequestedData as $customerRequestRoom) {

                $numberOfRooms = $customerRequestRoom->roomsRequested;

                if ($numberOfRooms >= 1) {
                    

                    $roomRequestID = $customerRequestRoom->trRoomID;

                    $trRoom = TeamRequestRoom::find($roomRequestID);

                    $thRoom = TournamentHotelRoom::find($trRoom->tournament_hotel_room_id);

                    $trRoom->available = $trRoom->available - $numberOfRooms;
                    $trRoom->allocated = $trRoom->allocated + $numberOfRooms;

                    $thRoom->held = $thRoom->held - $numberOfRooms;
                    $thRoom->allocated = $thRoom->allocated + $numberOfRooms;

                    $trRoom->save();
                    $thRoom->save();

                    $customerRoomData = array(
                        "customer_id" => $customer->id,
                        "quantity" => $numberOfRooms,
                        "check_in" => $request->get('checkInDate'),
                        "check_out" => $request->get('checkOutDate'),
                        "number_of_nights" => $request->get('numberOfNights'),
                        "team_request_room_id" => $roomRequestID
                    );

                    $customerRoom = CustomerRoom::create($customerRoomData);

                    //Log::info($thRoom->hotelRoomtype->type);

                    $customerRooms = (object) [];

                    $customerRooms->quantity = $numberOfRooms;

                    $customerRooms->number_of_nights = $request->get('numberOfNights');

                    $customerRooms->type = $thRoom->hotelRoomtype->type;

                    /*$customerRooms = array(
                        'quantity' => $numberOfRooms,
                        'number_of_nights' => $request->get('numberOfNights'),
                        'type' => $thRoom->hotelRoomtype->type
                    );*/

                    array_push($customerRoomsArray, $customerRooms);

                }
               
                
            }
            
            //$contactName = $customer->first_name . ' ' . $customer->last_name;
            
            $contactName = $request->get('firstName') . ' ' . $request->get('lastName');

            $tournament = Tournament::find($request->get('tournamentID'));

            $teamRequest = TeamRequest::find($request->get('teamRequestID'));

            $hotelName = $teamRequest->getHotel()->name;

            $siteTransactionFeeTotal = number_format((float)round( ($customerOrder->transaction_flat_fee + $siteTransactionPercent),2, PHP_ROUND_HALF_DOWN),2,'.','');
            
            $siteTransactionFeeTotalString = "" . $siteTransactionFeeTotal;
            
            $checkInDate = "" . $request->get('checkInDate');
            $checkOutDate = "" . $request->get('checkOutDate');
            
            
            //Log::info($siteTransactionFeeTotal);

           \Mail::send('orderResponseMail', array(
                'confirmationNumber' => $confirmation_number,
                'orderRooms' => $customerRoomsArray,
                'roomsTotal' => $request->get('pricePerNightTotal'),
                'stateAndLocalTax' => $totalTax,
                'hotelName' => $hotelName,
                'total' => $totalCost,
                'occupancyTax' => $request->get("hotelFee"),
                'transactionFee' => $siteTransactionFeeTotalString,
                'checkInDate' => $checkInDate,
                'checkOutDate' => $checkOutDate,
            ), function($message) use ($request, $contactName){
                $message->to('' . $request->get('email'), '' . $contactName )->subject('Championship City Rooms Order Confirmation');
            });
            
            /*\Mail::send('orderResponseMail', array(
                'tournamentName' => $tournament->name,
                'contactName' => $contactName,
                'teamName' => $teamRequest->team_name,
                'confirmationNumber' => $confirmation_number,
                'orderRooms' => $customerRoomsArray,
                'roomsTotal' => $request->get('pricePerNightTotal'),
                'stateAndLocalTax' => $totalTax,
                'occupancyTax' => $request->get("hotelFee"),
                'transactionFee' => $siteTransactionFeeTotal,
                'total' => $totalCost,
                'hotelName' => $hotelName,
            ), function($message) use ($request, $contactName){
                $message->to('' . $request->get('email'), '' . $contactName )->subject('Championship City Rooms Order Confirmation');
            });*/


            /* SEND EMAIL WITH CONFIRMATION NUMBER */
            $response = array(
                "message" => "Successfully Submited. You will be emailed with your confirmation number"
            );
            

            

        } else {
            $response = array(
                "message" => "Something went wrong. If this persist please contact us for help."
            );
        }
        

        //Log::info(json_encode($gw->responses));

        //$parsedCcvResponse = json_decode($ccvResponse);

        //$ccvReponseType = gettype($ccvResponse);
        /* ITS A STRING !! *///REFID:1148235201

        

        echo json_encode($response);
        exit;

    }

    function testConfirmationNumber($uuid) {
        $uuidExist = CustomerOrder::where('confirmation_number', '=', $uuid)->first();
        if ($uuidExist === null) {
            return true;
        } else {
            return false;
        }
    }

    /*public function storeInVault(Request $request) {

        $gw = new gwapi;
        $gw->setLogin("CvH82Yys2vGcvrX2974c9zF8852Cqad8");
        $gw->setBilling($request->input('firstName'),$request->input('lastName'),"",$request->input('address'),"", $request->input('city'),
            $request->input('state'),$request->input('zip'),"US",$request->input('phone'),"",$request->input('email'),"");
                
        //$gw->setVaultInfo($request->input('ccnumber'), $request->input('ccexp'));
       // $gw->setShipping("Mary","Smith","na","124 Shipping Main St","Suite Ship", "Beverly Hills",
                //"CA","90210","US","support@example.com");
        //$gw->setOrder("1234","Big Order",1, 2, "PO1234","65.192.14.10");
    
        //$r = $gw->doSale("50.00","4111111111111111","1010");
    
        //$gw->doStoreInVault($request->input('ccnumber'), $request->input('ccexp'));
    
        $gw->doStoreInVault($request->input('payment_token'));
    
        print $gw->responses['responsetext'];
    
    }*/

    public function teamRequestSubmission(Request $request) {
        

        $tournament = Tournament::findOrFail($request->get('tournamentID'));

        $start = strtotime($tournament->default_check_in);
        $your_date = strtotime($tournament->default_check_out);
        $datediff =  $your_date - $start;

        $defaultNumberOfDays = round($datediff / (60 * 60 * 24));

        $defaultNumberOfDays = $defaultNumberOfDays; 

        $defaultTeamLinkExp = null;
        if(null !== $tournament->default_team_link_expiration) {
            $defaultTeamLinkExp = $tournament->default_team_link_expiration;
        } 

        if( null !== $request->get('teamMemberLinkExpiration') ) {
            if ($request->get('teamMemberLinkExpiration') != "") {
                $defaultTeamLinkExp  = $request->get('teamMemberLinkExpiration');
            }
        }

        $allow_individual_check_in_out_dates = 0;

        if ( null !== $request->get('allowIndividualCheckInOut')) {
            $allow_individual_check_in_out_dates = $request->get('allowIndividualCheckInOut');
        }
        
        $request_are_late = 0;
        if (null !== $request->get('requestAreLate')) {
            $request_are_late = $request->get('requestAreLate');
        }

        //Log::info($defaultNumberOfDays);

        $teamRequestData = array(
            "team_name" => $request->get('teamName'),
            "contact_first_name" => $request->get('contactFirstName'),
            "contact_last_name" => $request->get('contactLastName'),
            "phone_number" => $request->get('phone'),
            'email' => $request->get('email'),
            'description' => $request->get('specialRequest'),
            "hotel_id" => $request->get('hotelID'),
            "tournament_id" => $request->get('tournamentID'),
            "link_expire_date" => $defaultTeamLinkExp,
            "allow_individual_check_in_out_dates" => $allow_individual_check_in_out_dates,
            "late_request" => $request_are_late,
            "check_in" => $tournament->default_check_in,
            "check_out" => $tournament->default_check_out,
            "number_of_nights" => $defaultNumberOfDays,
            "uuid" => Str::uuid()->toString()
        );



        $trHotelRoomData = json_decode($request->get('trHotelRoomData'));

        $teamRequest = TeamRequest::create($teamRequestData);

        //$teamRequest->save();

        foreach ($trHotelRoomData as $teamRequestRoom) {


            if ($teamRequestRoom->roomsRequested != 0) {
                $teamRequestRoomData = array(
                    "team_request_id" => $teamRequest->id,
                    "tournament_hotel_room_id" => $teamRequestRoom->hotelRoomID,
                    "rooms_requested" => $teamRequestRoom->roomsRequested,
                    "default_nights" => $defaultNumberOfDays
                );


                $teamRequestRoom = TeamRequestRoom::create($teamRequestRoomData);
            }
            
        }


        $response = array(
            "message" => "Successfully Submited Request. You will be emailed about your request upon approval.",
            "trID" => $teamRequest->id
        );


        $contactName = $teamRequest->contact_first_name . ' ' . $teamRequest->contact_last_name;

         //  Send mail to admin
         \Mail::send('teamSignupMail', array(
            'tournamentName' => $tournament->name,
            'contactName' => $contactName,
            'teamName' => $teamRequest->team_name,
        ), function($message) use ($teamRequest, $contactName){
            //$message->to('caleb@vgnet.com', 'Admin')->subject($request->get('subject'));
            $message->to('' . $teamRequest->email, '' . $contactName )->subject('Team Request Recieved');
        });
    

        echo json_encode($response);
        exit;
    }

    public function getTeamRequest(Request $request) {
        $trID = $request->get('trID');

        $teamRequest = TeamRequest::findOrFail($trID);

        $teamRequestRoomsData = array();

        foreach ($teamRequest->teamRequestRooms as $teamRequestRoom) {
            $teamRequestRoomData = array(
                "tr_room_id" => $teamRequestRoom->id,
                "tournament_hotel_room_id" => $teamRequestRoom->tournament_hotel_room_id,
                "rooms_requested" => $teamRequestRoom->rooms_requested
            );

            array_push($teamRequestRoomsData, $teamRequestRoomData);
        }

        $request_are_late = 0;
        $team_member_link_expiration = null;
        $allow_individual_check_out = 0;

        if (null !== $teamRequest->late_request) {
            $request_are_late = $teamRequest->late_request;
        }

        if (null !== $teamRequest->allow_individual_check_in_out_dates) {
            $allow_individual_check_out = $teamRequest->allow_individual_check_in_out_dates;
        }

        if (null !== $teamRequest->link_expire_date) {
            $team_member_link_expiration = $teamRequest->link_expire_date;
        }

        $teamRequestData = array(
            "team_name" => $teamRequest->team_name,
            "contact_first_name" => $teamRequest->contact_first_name,
            "contact_last_name" => $teamRequest->contact_last_name,
            "phone_number" => $teamRequest->phone_number,
            'email' => $teamRequest->email,
            "hotel_id" => $teamRequest->hotel_id,
            "rooms_data" => json_encode($teamRequestRoomsData),
            "request_are_late" => $request_are_late,
            "allow_individual_check_out" => $allow_individual_check_out,
            "team_member_link_expiration" => $team_member_link_expiration
        );

        echo json_encode($teamRequestData);
        exit;
    }

    public function trmUpdateRequest(Request $request) {

        //$roomsDataUpdate = array();

        $trHotelRoomData = json_decode($request->get('trHotelRoomData'));

        $teamRequest = TeamRequest::findOrFail($request->get('teamRequestID'));

        $teamRequest->team_name = $request->get('teamName');
        $teamRequest->contact_first_name = $request->get('contactFirstName');
        $teamRequest->contact_last_name = $request->get('contactLastName');
        $teamRequest->phone_number = $request->get('phone');
        $teamRequest->email = $request->get('email');

        if( $request->get('teamMemberLinkExpiration') != "") {
            $teamRequest->link_expire_date = $request->get('teamMemberLinkExpiration');
        }
        
        
        
        
        $teamRequest->allow_individual_check_in_out_dates = $request->get('allowIndividualCheckInOut');
        
        $teamRequest->late_request = $request->get('requestAreLate');

        $tournament = Tournament::findOrFail($teamRequest->tournament_id);

        $start = strtotime($tournament->default_check_in);
        $your_date = strtotime($tournament->default_check_out);
        $datediff =  $your_date - $start;

        $defaultNumberOfDays = round($datediff / (60 * 60 * 24));

        //$defaultNumberOfDays = $defaultNumberOfDays - 1;

        $teamRequest->check_in = $tournament->default_check_in;
        $teamRequest->check_out = $tournament->default_check_out;
        $teamRequest->number_of_nights = $defaultNumberOfDays;

        $teamRequest->save();
        

        foreach ($trHotelRoomData as $teamRequestRoom) {
            //Log::Info($teamRequestRoom->teamRequestRoomID);
            //$trrd = json_decode($trRoomData);

            if($teamRequestRoom->teamRequestRoomID != 0) {

                $teamRequestRoomOld = TeamRequestRoom::findOrFail($teamRequestRoom->teamRequestRoomID);


                //$newTeamRequestRoomAmountDifference = $teamRequestRoom->roomsRequested - $teamRequestRoomOld->quantity;

                //$teamRequestRoomOld->quantity = $teamRequestRoom->roomsRequested;

                $teamRequestRoomOld->rooms_requested = $teamRequestRoom->roomsRequested;

                $teamRequestRoomOld->save();

                //$teamRequestRoomOld->available += $newTeamRequestRoomAmountDifference;

                //array_push($roomsDataUpdate, ['tournamentHotelRoom_id'=>$teamRequestRoom->hotelRoomID, 'trRoomID' => $teamRequestRoom->teamRequestRoomID, 'amount'=>$teamRequestRoom->roomsRequested]);
            } elseif($teamRequestRoom->roomsRequested != 0) {

                $teamRequestRoomData = array(
                    "team_request_id" => $teamRequest->id,
                    "tournament_hotel_room_id" => $teamRequestRoom->hotelRoomID,
                    "rooms_requested" => $teamRequestRoom->roomsRequested,
                    "default_nights" => $defaultNumberOfDays
                );
    
    
                $newTeamRequestRoom = TeamRequestRoom::create($teamRequestRoomData);
            }

            
        }

        

        $response = array(
            "message" => 'Update To Team Request Success',
            //"trID" => $request->get('teamRequestID'),
            //"rdata" => json_encode($roomsDataUpdate)
        );

        echo json_encode($response);
        exit;
    }

    public function trmUpdateApprovedRequest(Request $request) {

        $roomsDataUpdate = array();

        $trHotelRoomData = json_decode($request->get('trHotelRoomData'));

        $teamRequest = TeamRequest::findOrFail($request->get('teamRequestID'));

        $tournament = Tournament::findOrFail($teamRequest->tournament_id);

        $start = strtotime($tournament->default_check_in);
        $your_date = strtotime($tournament->default_check_out);
        $datediff =  $your_date - $start;

        $defaultNumberOfDays = round($datediff / (60 * 60 * 24));

        //$defaultNumberOfDays = $defaultNumberOfDays;
        
        //Log::info($defaultNumberOfDays);

		//Log::info($request->get('teamMemberLinkExpiration'));

        if( $request->get('teamMemberLinkExpiration') != "") {
            $teamRequest->link_expire_date = $request->get('teamMemberLinkExpiration');
        }
        
        //Log::info('yo ' . $request->get('allowIndividualCheckInOut'));
        $teamRequest->allow_individual_check_in_out_dates = $request->get('allowIndividualCheckInOut');
        
        $teamRequest->late_request = $request->get('request_are_late');

        $teamRequest->team_name = $request->get('teamName');
        $teamRequest->contact_first_name = $request->get('contactFirstName');
        $teamRequest->contact_last_name = $request->get('contactLastName');
        $teamRequest->phone_number = $request->get('phone');
        $teamRequest->email = $request->get('email');
        $teamRequest->check_in = $tournament->default_check_in;
        $teamRequest->check_out = $tournament->default_check_out;
        $teamRequest->number_of_nights = $defaultNumberOfDays;

        

        $teamRequest->save();

        foreach ($trHotelRoomData as $teamRequestRoom) {
            //$trrd = json_decode($trRoomData);

            

            $teamRequestRoomOld = TeamRequestRoom::findOrFail($teamRequestRoom->teamRequestRoomID);

            $newTeamRequestRoomAmountDifference = $teamRequestRoom->roomsRequested - $teamRequestRoomOld->quantity;

            $teamRequestRoomOld->quantity = $teamRequestRoom->roomsRequested;

            $teamRequestRoomOld->rooms_requested = $teamRequestRoom->roomsRequested;

            $teamRequestRoomOld->available += $newTeamRequestRoomAmountDifference;

            if ($teamRequestRoomOld->available < 0) {

            } else {

                

                $tournamentHotelRoom = TournamentHotelRoom::findOrFail($teamRequestRoom->hotelRoomID);
            
                if ($tournamentHotelRoom->rooms_available >= $newTeamRequestRoomAmountDifference) {
                    $teamRequestRoomOld->save();

                    $tournamentHotelRoom->rooms_available -= $newTeamRequestRoomAmountDifference;
                    $tournamentHotelRoom->held += $newTeamRequestRoomAmountDifference;

                    //$tournamentHotelRoom->save();

                    /*$teamRequestRoom->quantity = $newTeamRequestRoomAmountDifference;
                    $teamRequestRoom->available = $newTeamRequestRoomAmountDifference;

                    $teamRequestRoom->save();*/

                    //array_push($roomsDataUpdate, ['tournamentHotelRoom_id'=>$teamRequestRoom->tournament_hotel_room_id, 'amount'=>$teamRequestRoom->rooms_requested]);
                    
                } else {
                    //do error message
                }

                array_push($roomsDataUpdate, ['tournamentHotelRoom_id'=>$teamRequestRoom->hotelRoomID, 'amount'=>$newTeamRequestRoomAmountDifference]);

            }

            
        }

        

        $response = array(
            "message" => 'Update To Approved Team Request Success',
            "rdata" => json_encode($roomsDataUpdate)
        );

        echo json_encode($response);
        exit;
    }

    public function approveTeamRequest(Request $request) {

        $trID = $request->get('trID');

        $roomsDataUpdate = array();

        $teamRequest = TeamRequest::findOrFail($trID);

        $tournament = Tournament::find($teamRequest->tournament_id);

        $teamRequestLinkExpire = "";

        //$teamRequestLinkExpire = Carbon::createFromFormat('Y-m-d', $tournament->start_date)->subDays(20)->toDateString();

        if (!is_null($tournament->default_team_link_expiration)) {
            $teamRequestLinkExpire = $tournament->default_team_link_expiration;
        } else {
            $teamRequestLinkExpire = Carbon::createFromFormat('Y-m-d', $tournament->start_date)->subDays(20)->toDateString();
        }
        

        if($teamRequest) {
            $teamRequest->denied = 0;
            $teamRequest->approved = 1;
            $teamRequest->link_expire_date = $teamRequestLinkExpire;
            $teamRequest->save();

            $contactName = $teamRequest->contact_first_name . ' ' . $teamRequest->contact_last_name;

            $teamRequestLink = "https://championshipcityrooms.com/tournament/" . $tournament->slug . "/team-members-select-rooms-form?tuuid=" . $teamRequest->uuid;

             //  Send mail to admin
            \Mail::send('teamSignupApprovedMail', array(
                'teamLink' => $teamRequestLink,
                'teamLinkExpires' => $teamRequestLinkExpire,
                'tournamentName' => $tournament->name,
                'teamName' => $teamRequest->team_name,
                'contactName' => $contactName,
            ), function($message) use ($teamRequest, $contactName){
                //$message->to('caleb@vgnet.com', 'Admin')->subject($request->get('subject'));
                $message->to('' . $teamRequest->email, '' . $contactName )->subject('Team Request Approval');
            });
        }

        



        //Log::info($teamRequestLinkExpire);

        foreach ($teamRequest->teamRequestRooms as $teamRequestRoom) {
            $tournamentHotelRoom = TournamentHotelRoom::findOrFail($teamRequestRoom->tournament_hotel_room_id);
            
            if ($tournamentHotelRoom->rooms_available >= $teamRequestRoom->rooms_requested) {
                $tournamentHotelRoom->rooms_available -= $teamRequestRoom->rooms_requested;
                $tournamentHotelRoom->held += $teamRequestRoom->rooms_requested;

                $tournamentHotelRoom->save();

                $teamRequestRoom->quantity = $teamRequestRoom->rooms_requested;
                $teamRequestRoom->available = $teamRequestRoom->rooms_requested;

                $teamRequestRoom->save();

                array_push($roomsDataUpdate, ['tournamentHotelRoom_id'=>$teamRequestRoom->tournament_hotel_room_id, 'amount'=>$teamRequestRoom->rooms_requested]);
                
            } else {
                //do error message
            }
        }

        $response = array(
            "message" => "Team Request Approved",
            "trID" => $trID,
            "updateData" => json_encode($roomsDataUpdate)
         );
    
         echo json_encode($response);
         exit;
    }

    public function denyTeamRequest(Request $request) {

        $trID = $request->get('trID');
        $reasonDenied = $request->get('reasonDenied');

        $teamRequest = TeamRequest::findOrFail($trID);

        if($teamRequest) {
            $teamRequest->denied = 1;
            $teamRequest->reason_denied = $reasonDenied;
            $teamRequest->save();
        }

        $response = array(
            "message" => "Team Request Denied",
            "trID" => $trID
         );
    
         echo json_encode($response);
         exit;
    }


    public function getApprovedTeamRequests(Request $request) {

        ## Read value
        $draw = $request->get('draw');
        $start = $request->get("start");
        $rowperpage = $request->get("length"); // Rows display per page

        $columnIndex_arr = $request->get('order');
        $columnName_arr = $request->get('columns');
        $order_arr = $request->get('order');
        $search_arr = $request->get('search');

        $columnIndex = $columnIndex_arr[0]['column']; // Column index
        $columnName = $columnName_arr[$columnIndex]['data']; // Column name
        $columnSortOrder = $order_arr[0]['dir']; // asc or desc
        $searchValue = $search_arr['value']; // Search value

        // Total records
        /*$totalRecords = Tournament::select('count(*) as allcount')->count();
        $totalRecordswithFilter = Tournament::select('count(*) as allcount')->where('name', 'like', '%' .$searchValue . '%')->count();

        // Fetch records
        $records = Tournament::orderBy($columnName,$columnSortOrder)*/


        $totalRecords = \App\TeamRequest::select('count(*) as allcount')->WHERE('tournament_id', '=', $request->get('tournament_id'))->WHERE('approved', '=', 1)->count();
        $totalRecordswithFilter = \App\TeamRequest::select('count(*) as allcount')->WHERE('tournament_id', '=', $request->get('tournament_id'))->WHERE('approved', '=', 1)->where('team_name', 'like', '%' .$searchValue . '%')->count();

        // Fetch records
        $records = \App\TeamRequest::orderBy($columnName,$columnSortOrder)->WHERE('tournament_id', '=', $request->get('tournament_id'))->WHERE('approved', '=', 1)
        ->where('team_requests.team_name', 'like', '%' .$searchValue . '%')
        ->select('team_requests.*')
        ->skip($start)
        ->take($rowperpage)
        ->get();

        $data_arr = array();
        
        foreach($records as $record){
        $id = $record->id;
        $name = $record->team_name;
        $contact = $record->contact_first_name . ' ' . $record->contact_last_name;
        $phone = $record->phone_number;
        $email = $record->email;
        $tuuid = $record->uuid;

        $data_arr[] = array(
            "id" => $id,
            "team_name" => $name,
            "contact" => $contact,
            "phone" => $phone,
            "email" => $email,
            'uuid' => $tuuid,
        );
        }

        $response = array(
        "draw" => intval($draw),
        "iTotalRecords" => $totalRecords,
        "iTotalDisplayRecords" => $totalRecordswithFilter,
        "aaData" => $data_arr
        );

        echo json_encode($response);
        exit;
    }

    public function getDeniedTeamRequests(Request $request) {

        ## Read value
        $draw = $request->get('draw');
        $start = $request->get("start");
        $rowperpage = $request->get("length"); // Rows display per page

        $columnIndex_arr = $request->get('order');
        $columnName_arr = $request->get('columns');
        $order_arr = $request->get('order');
        $search_arr = $request->get('search');

        $columnIndex = $columnIndex_arr[0]['column']; // Column index
        $columnName = $columnName_arr[$columnIndex]['data']; // Column name
        $columnSortOrder = $order_arr[0]['dir']; // asc or desc
        $searchValue = $search_arr['value']; // Search value

        // Total records
        /*$totalRecords = Tournament::select('count(*) as allcount')->count();
        $totalRecordswithFilter = Tournament::select('count(*) as allcount')->where('name', 'like', '%' .$searchValue . '%')->count();

        // Fetch records
        $records = Tournament::orderBy($columnName,$columnSortOrder)*/


        $totalRecords = \App\TeamRequest::select('count(*) as allcount')->WHERE('tournament_id', '=', $request->get('tournament_id'))->WHERE('denied', '=', 1)->count();
        $totalRecordswithFilter = \App\TeamRequest::select('count(*) as allcount')->WHERE('tournament_id', '=', $request->get('tournament_id'))->WHERE('denied', '=', 1)->where('team_name', 'like', '%' .$searchValue . '%')->count();

        // Fetch records
        $records = \App\TeamRequest::orderBy($columnName,$columnSortOrder)->WHERE('tournament_id', '=', $request->get('tournament_id'))->WHERE('denied', '=', 1)
        ->where('team_requests.team_name', 'like', '%' .$searchValue . '%')
        ->select('team_requests.*')
        ->skip($start)
        ->take($rowperpage)
        ->get();

        $data_arr = array();
        
        foreach($records as $record){
        $id = $record->id;
        $name = $record->team_name;
        $contact = $record->contact_first_name . ' ' . $record->contact_last_name;
        $phone = $record->phone_number;
        $email = $record->email;

        $data_arr[] = array(
            "id" => $id,
            "team_name" => $name,
            "contact" => $contact,
            "phone" => $phone,
            "email" => $email
        );
        }

        $response = array(
        "draw" => intval($draw),
        "iTotalRecords" => $totalRecords,
        "iTotalDisplayRecords" => $totalRecordswithFilter,
        "aaData" => $data_arr
        );

        echo json_encode($response);
        exit;
    }

}



class gwapi {

// Initial Setting Functions

function setLogin($security_key) {
$this->login['security_key'] = $security_key;
}

function setOrder($orderid,
    $orderdescription,
    $tax,
    $shipping,
    $ponumber,
    $ipaddress) {
$this->order['orderid']          = $orderid;
$this->order['orderdescription'] = $orderdescription;
$this->order['tax']              = $tax;
$this->order['shipping']         = $shipping;
$this->order['ponumber']         = $ponumber;
$this->order['ipaddress']        = $ipaddress;
}

function setBilling($firstname,
    $lastname,
    $company,
    $address1,
    $address2,
    $city,
    $state,
    $zip,
    $country,
    $phone,
    $fax,
    $email,
    $website) {
$this->billing['firstname'] = $firstname;
$this->billing['lastname']  = $lastname;
$this->billing['company']   = $company;
$this->billing['address1']  = $address1;
$this->billing['address2']  = $address2;
$this->billing['city']      = $city;
$this->billing['state']     = $state;
$this->billing['zip']       = $zip;
$this->billing['country']   = $country;
$this->billing['phone']     = $phone;
$this->billing['fax']       = $fax;
$this->billing['email']     = $email;
$this->billing['website']   = $website;
}

function setShipping($firstname,
    $lastname,
    $company,
    $address1,
    $address2,
    $city,
    $state,
    $zip,
    $country,
    $email) {
$this->shipping['firstname'] = $firstname;
$this->shipping['lastname']  = $lastname;
$this->shipping['company']   = $company;
$this->shipping['address1']  = $address1;
$this->shipping['address2']  = $address2;
$this->shipping['city']      = $city;
$this->shipping['state']     = $state;
$this->shipping['zip']       = $zip;
$this->shipping['country']   = $country;
$this->shipping['email']     = $email;
}

/*function setVaultInfo($ccnumber,
      $ccexp) {
$this->vaultInfo['customer_vault'] = 'add_customer';
$this->vaultInfo['ccnumber'] = $ccnumber;
$this->vaultInfo['ccexp'] = $ccexp;
}*/

function doStoreInVault($payment_token, $cvv="") {
  // $ccnumber, $ccexp,
$query  = "";
// Login Information
$query .= "security_key=" . urlencode($this->login['security_key']) . "&";
// Transaction Information
//Log::info($payment_token);
$query .= "customer_vault=" . urlencode('add_customer') . "&";
$query .= "payment_token=" . urlencode($payment_token) . "&";
/*$query .= "ccnumber=" . urlencode($ccnumber) . "&";
$query .= "ccexp=" . urlencode($ccexp) . "&";
$query .= "cvv=" . urlencode($cvv) . "&";*/

// Billing Information
$query .= "firstname=" . urlencode($this->billing['firstname']) . "&";
$query .= "lastname=" . urlencode($this->billing['lastname']) . "&";
$query .= "company=" . urlencode($this->billing['company']) . "&";
$query .= "address1=" . urlencode($this->billing['address1']) . "&";
$query .= "address2=" . urlencode($this->billing['address2']) . "&";
$query .= "city=" . urlencode($this->billing['city']) . "&";
$query .= "state=" . urlencode($this->billing['state']) . "&";
$query .= "zip=" . urlencode($this->billing['zip']) . "&";
$query .= "country=" . urlencode($this->billing['country']) . "&";
$query .= "phone=" . urlencode($this->billing['phone']) . "&";
$query .= "fax=" . urlencode($this->billing['fax']) . "&";
$query .= "email=" . urlencode($this->billing['email']) . "&";
$query .= "website=" . urlencode($this->billing['website']);

//Log::info($query);

return $this->_doPost($query);
}

// Transaction Functions

function doSale($amount, $ccnumber, $ccexp, $cvv="") {

$query  = "";
// Login Information
$query .= "security_key=" . urlencode($this->login['security_key']) . "&";
// Sales Information
$query .= "ccnumber=" . urlencode($ccnumber) . "&";
$query .= "ccexp=" . urlencode($ccexp) . "&";
$query .= "amount=" . urlencode(number_format($amount,2,".","")) . "&";
$query .= "cvv=" . urlencode($cvv) . "&";
// Order Information
$query .= "ipaddress=" . urlencode($this->order['ipaddress']) . "&";
$query .= "orderid=" . urlencode($this->order['orderid']) . "&";
$query .= "orderdescription=" . urlencode($this->order['orderdescription']) . "&";
$query .= "tax=" . urlencode(number_format($this->order['tax'],2,".","")) . "&";
$query .= "shipping=" . urlencode(number_format($this->order['shipping'],2,".","")) . "&";
$query .= "ponumber=" . urlencode($this->order['ponumber']) . "&";
// Billing Information
$query .= "firstname=" . urlencode($this->billing['firstname']) . "&";
$query .= "lastname=" . urlencode($this->billing['lastname']) . "&";
$query .= "company=" . urlencode($this->billing['company']) . "&";
$query .= "address1=" . urlencode($this->billing['address1']) . "&";
$query .= "address2=" . urlencode($this->billing['address2']) . "&";
$query .= "city=" . urlencode($this->billing['city']) . "&";
$query .= "state=" . urlencode($this->billing['state']) . "&";
$query .= "zip=" . urlencode($this->billing['zip']) . "&";
$query .= "country=" . urlencode($this->billing['country']) . "&";
$query .= "phone=" . urlencode($this->billing['phone']) . "&";
$query .= "fax=" . urlencode($this->billing['fax']) . "&";
$query .= "email=" . urlencode($this->billing['email']) . "&";
$query .= "website=" . urlencode($this->billing['website']) . "&";
// Shipping Information
$query .= "shipping_firstname=" . urlencode($this->shipping['firstname']) . "&";
$query .= "shipping_lastname=" . urlencode($this->shipping['lastname']) . "&";
$query .= "shipping_company=" . urlencode($this->shipping['company']) . "&";
$query .= "shipping_address1=" . urlencode($this->shipping['address1']) . "&";
$query .= "shipping_address2=" . urlencode($this->shipping['address2']) . "&";
$query .= "shipping_city=" . urlencode($this->shipping['city']) . "&";
$query .= "shipping_state=" . urlencode($this->shipping['state']) . "&";
$query .= "shipping_zip=" . urlencode($this->shipping['zip']) . "&";
$query .= "shipping_country=" . urlencode($this->shipping['country']) . "&";
$query .= "shipping_email=" . urlencode($this->shipping['email']) . "&";
$query .= "type=sale";
return $this->_doPost($query);
}

function doAuth($amount, $ccnumber, $ccexp, $cvv="") {

$query  = "";
// Login Information
$query .= "security_key=" . urlencode($this->login['security_key']) . "&";
// Sales Information
$query .= "ccnumber=" . urlencode($ccnumber) . "&";
$query .= "ccexp=" . urlencode($ccexp) . "&";
$query .= "amount=" . urlencode(number_format($amount,2,".","")) . "&";
$query .= "cvv=" . urlencode($cvv) . "&";
// Order Information
$query .= "ipaddress=" . urlencode($this->order['ipaddress']) . "&";
$query .= "orderid=" . urlencode($this->order['orderid']) . "&";
$query .= "orderdescription=" . urlencode($this->order['orderdescription']) . "&";
$query .= "tax=" . urlencode(number_format($this->order['tax'],2,".","")) . "&";
$query .= "shipping=" . urlencode(number_format($this->order['shipping'],2,".","")) . "&";
$query .= "ponumber=" . urlencode($this->order['ponumber']) . "&";
// Billing Information
$query .= "firstname=" . urlencode($this->billing['firstname']) . "&";
$query .= "lastname=" . urlencode($this->billing['lastname']) . "&";
$query .= "company=" . urlencode($this->billing['company']) . "&";
$query .= "address1=" . urlencode($this->billing['address1']) . "&";
$query .= "address2=" . urlencode($this->billing['address2']) . "&";
$query .= "city=" . urlencode($this->billing['city']) . "&";
$query .= "state=" . urlencode($this->billing['state']) . "&";
$query .= "zip=" . urlencode($this->billing['zip']) . "&";
$query .= "country=" . urlencode($this->billing['country']) . "&";
$query .= "phone=" . urlencode($this->billing['phone']) . "&";
$query .= "fax=" . urlencode($this->billing['fax']) . "&";
$query .= "email=" . urlencode($this->billing['email']) . "&";
$query .= "website=" . urlencode($this->billing['website']) . "&";
// Shipping Information
$query .= "shipping_firstname=" . urlencode($this->shipping['firstname']) . "&";
$query .= "shipping_lastname=" . urlencode($this->shipping['lastname']) . "&";
$query .= "shipping_company=" . urlencode($this->shipping['company']) . "&";
$query .= "shipping_address1=" . urlencode($this->shipping['address1']) . "&";
$query .= "shipping_address2=" . urlencode($this->shipping['address2']) . "&";
$query .= "shipping_city=" . urlencode($this->shipping['city']) . "&";
$query .= "shipping_state=" . urlencode($this->shipping['state']) . "&";
$query .= "shipping_zip=" . urlencode($this->shipping['zip']) . "&";
$query .= "shipping_country=" . urlencode($this->shipping['country']) . "&";
$query .= "shipping_email=" . urlencode($this->shipping['email']) . "&";
$query .= "type=auth";
return $this->_doPost($query);
}

function doCredit($amount, $ccnumber, $ccexp) {

$query  = "";
// Login Information
$query .= "security_key=" . urlencode($this->login['security_key']) . "&";
// Sales Information
$query .= "ccnumber=" . urlencode($ccnumber) . "&";
$query .= "ccexp=" . urlencode($ccexp) . "&";
$query .= "amount=" . urlencode(number_format($amount,2,".","")) . "&";
// Order Information
$query .= "ipaddress=" . urlencode($this->order['ipaddress']) . "&";
$query .= "orderid=" . urlencode($this->order['orderid']) . "&";
$query .= "orderdescription=" . urlencode($this->order['orderdescription']) . "&";
$query .= "tax=" . urlencode(number_format($this->order['tax'],2,".","")) . "&";
$query .= "shipping=" . urlencode(number_format($this->order['shipping'],2,".","")) . "&";
$query .= "ponumber=" . urlencode($this->order['ponumber']) . "&";
// Billing Information
$query .= "firstname=" . urlencode($this->billing['firstname']) . "&";
$query .= "lastname=" . urlencode($this->billing['lastname']) . "&";
$query .= "company=" . urlencode($this->billing['company']) . "&";
$query .= "address1=" . urlencode($this->billing['address1']) . "&";
$query .= "address2=" . urlencode($this->billing['address2']) . "&";
$query .= "city=" . urlencode($this->billing['city']) . "&";
$query .= "state=" . urlencode($this->billing['state']) . "&";
$query .= "zip=" . urlencode($this->billing['zip']) . "&";
$query .= "country=" . urlencode($this->billing['country']) . "&";
$query .= "phone=" . urlencode($this->billing['phone']) . "&";
$query .= "fax=" . urlencode($this->billing['fax']) . "&";
$query .= "email=" . urlencode($this->billing['email']) . "&";
$query .= "website=" . urlencode($this->billing['website']) . "&";
$query .= "type=credit";
return $this->_doPost($query);
}

function doOffline($authorizationcode, $amount, $ccnumber, $ccexp) {

$query  = "";
// Login Information
$query .= "security_key=" . urlencode($this->login['security_key']) . "&";
// Sales Information
$query .= "ccnumber=" . urlencode($ccnumber) . "&";
$query .= "ccexp=" . urlencode($ccexp) . "&";
$query .= "amount=" . urlencode(number_format($amount,2,".","")) . "&";
$query .= "authorizationcode=" . urlencode($authorizationcode) . "&";
// Order Information
$query .= "ipaddress=" . urlencode($this->order['ipaddress']) . "&";
$query .= "orderid=" . urlencode($this->order['orderid']) . "&";
$query .= "orderdescription=" . urlencode($this->order['orderdescription']) . "&";
$query .= "tax=" . urlencode(number_format($this->order['tax'],2,".","")) . "&";
$query .= "shipping=" . urlencode(number_format($this->order['shipping'],2,".","")) . "&";
$query .= "ponumber=" . urlencode($this->order['ponumber']) . "&";
// Billing Information
$query .= "firstname=" . urlencode($this->billing['firstname']) . "&";
$query .= "lastname=" . urlencode($this->billing['lastname']) . "&";
$query .= "company=" . urlencode($this->billing['company']) . "&";
$query .= "address1=" . urlencode($this->billing['address1']) . "&";
$query .= "address2=" . urlencode($this->billing['address2']) . "&";
$query .= "city=" . urlencode($this->billing['city']) . "&";
$query .= "state=" . urlencode($this->billing['state']) . "&";
$query .= "zip=" . urlencode($this->billing['zip']) . "&";
$query .= "country=" . urlencode($this->billing['country']) . "&";
$query .= "phone=" . urlencode($this->billing['phone']) . "&";
$query .= "fax=" . urlencode($this->billing['fax']) . "&";
$query .= "email=" . urlencode($this->billing['email']) . "&";
$query .= "website=" . urlencode($this->billing['website']) . "&";
// Shipping Information
$query .= "shipping_firstname=" . urlencode($this->shipping['firstname']) . "&";
$query .= "shipping_lastname=" . urlencode($this->shipping['lastname']) . "&";
$query .= "shipping_company=" . urlencode($this->shipping['company']) . "&";
$query .= "shipping_address1=" . urlencode($this->shipping['address1']) . "&";
$query .= "shipping_address2=" . urlencode($this->shipping['address2']) . "&";
$query .= "shipping_city=" . urlencode($this->shipping['city']) . "&";
$query .= "shipping_state=" . urlencode($this->shipping['state']) . "&";
$query .= "shipping_zip=" . urlencode($this->shipping['zip']) . "&";
$query .= "shipping_country=" . urlencode($this->shipping['country']) . "&";
$query .= "shipping_email=" . urlencode($this->shipping['email']) . "&";
$query .= "type=offline";
return $this->_doPost($query);
}

function doCapture($transactionid, $amount =0) {

$query  = "";
// Login Information
$query .= "security_key=" . urlencode($this->login['security_key']) . "&";
// Transaction Information
$query .= "transactionid=" . urlencode($transactionid) . "&";
if ($amount>0) {
    $query .= "amount=" . urlencode(number_format($amount,2,".","")) . "&";
}
$query .= "type=capture";
return $this->_doPost($query);
}

function doVoid($transactionid) {

$query  = "";
// Login Information
$query .= "security_key=" . urlencode($this->login['security_key']) . "&";
// Transaction Information
$query .= "transactionid=" . urlencode($transactionid) . "&";
$query .= "type=void";
return $this->_doPost($query);
}

function doRefund($transactionid, $amount = 0) {

$query  = "";
// Login Information
$query .= "security_key=" . urlencode($this->login['security_key']) . "&";
// Transaction Information
$query .= "transactionid=" . urlencode($transactionid) . "&";
if ($amount>0) {
    $query .= "amount=" . urlencode(number_format($amount,2,".","")) . "&";
}
$query .= "type=refund";
return $this->_doPost($query);
}

function _doPost($query) {
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://integratepayments.transactiongateway.com/api/transact.php");
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

curl_setopt($ch, CURLOPT_POSTFIELDS, $query);
curl_setopt($ch, CURLOPT_POST, 1);

if (!($data = curl_exec($ch))) {
    return ERROR;
}
curl_close($ch);
unset($ch);
//print "\n$data\n";
$data = explode("&",$data);
for($i=0;$i<count($data);$i++) {
    $rdata = explode("=",$data[$i]);
    $this->responses[$rdata[0]] = $rdata[1];
}
return $this->responses['response'];
}

}
