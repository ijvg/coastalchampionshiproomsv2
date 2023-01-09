<?php

namespace App\Http\Controllers;

use App\CancelOrder;
use App\ChangeOrder;
use App\CustomerOrder;
use Illuminate\Http\Request;
use App\Tournament;
use App\TournamentHotelRoom;
use App\Customer;
use App\CustomerRoom;
use App\Hotel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;

use Carbon\Carbon;

use App\Mail\SendMail;
use Mail;

define("APPROVED", 1);
define("DECLINED", 2);
define("ERROR", 3);

class TournamentsController extends Controller
{

    public function singleApplicationPage($slug, Request $request) {
        $tournament = \App\Tournament::where('slug', '=', $slug)->firstOrFail();

        $hotel = \App\Hotel::where('id', '=',  $request->query('hotel'))->get();

        //Log::info($hotel[0]);

        $hotel = $hotel[0];

        $hotelTournament = DB::table('hotel_tournament')->where('tournament_id', '=', $tournament->id)->where('hotel_id', '=', $hotel->id)->get();

        $hotelTournamentRooms = \App\TournamentHotelRoom::where('tournament_id', '=', $tournament->id)->where('hotel_id', '=', $hotel->id)->get();

        $states = $this->getStates();

        $siteTransactionFee = \App\SiteTransactionFee::find(1);

        $siteFee = $siteTransactionFee->amount;

        $sitePercentFee = $siteTransactionFee->percent;

        $roomAvailableCount = 0;

        foreach( $hotelTournamentRooms as $requestRoom) {
            if ( $requestRoom->rooms_available >= 1) {

              $roomAvailableCount++;

            }
        }

        Log::info($states);

        return view('/tournament-single-form', compact('tournament', 'hotel', 'hotelTournament', 'hotelTournamentRooms', 'states', 'siteFee', 'sitePercentFee', 'roomAvailableCount'));
    }

    public function singleRequestSubmission(Request $request) {
        $gw = new gwapi;
        $gw->setLogin("CvH82Yys2vGcvrX2974c9zF8852Cqad8");
        $gw->setBilling($request->get('firstName'),$request->get('lastName'),"",$request->get('address'),"", $request->get('city'),
            $request->get('state'),$request->get('zip'),"US",$request->get('phone'),"",$request->get('email'),"");


        $gw->doStoreInVault($request->get('tok'));


        $cvID = $gw->responses['customer_vault_id'];

        $customerRoomsArray = array();


        if ($gw->responses['response'] == 1) {

            $roomsRequestedData = json_encode($request->get('roomsRequested'));

            $roomsRequestedData = json_decode($roomsRequestedData);

            /* NEED DEPOSIT AMOUNT & REMAINING AMOUNT (TOTAL - DEPOSIT) */

            $customerData = array(
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

                    //$trRoom = TeamRequestRoom::find($roomRequestID);

                    $thRoom = TournamentHotelRoom::find($roomRequestID);

                    //$trRoom->available = $trRoom->available - $numberOfRooms;
                    //$trRoom->allocated = $trRoom->allocated + $numberOfRooms;

                    //$thRoom->held = $thRoom->held - $numberOfRooms;
                    $thRoom->allocated = $thRoom->allocated + $numberOfRooms;
                    $thRoom->rooms_available = $thRoom->rooms_available - $numberOfRooms;

                    //$trRoom->save();
                    $thRoom->save();

                    $customerRoomData = array(
                        "customer_id" => $customer->id,
                        "quantity" => $numberOfRooms,
                        "check_in" => $request->get('checkInDate'),
                        "check_out" => $request->get('checkOutDate'),
                        "number_of_nights" => $request->get('numberOfNights'),
                        "tournament_hotel_room_id" => $roomRequestID
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

            $hotel = Hotel::find($request->get('hotelID'));

            //$teamRequest = TeamRequest::find($request->get('teamRequestID'));

            $hotelName = $hotel->name;

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
            
            /* SEND EMAIL WITH CONFIRMATION NUMBER */
            $response = array(
                "message" => "Successfully Submited. You will be emailed with your confirmation number"
            );
            

            

        } else {
            $response = array(
                "message" => "Something went wrong. If this persist please contact us for help."
            );
        }

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

    public function changeRequestForm() {

      return view('/change-order');
    }

    public function submitChangeRequestForm(Request $request) {

      $request->validate([
        'confirmationNumber' => 'required|exists:customer_orders,confirmation_number',
        'changeDescription' => 'required',
      ]);

      $input = $request->all();

      $customersOrder = CustomerOrder::where('confirmation_number', '=', $input['confirmationNumber'])->get();
      
      if ($customersOrder->isEmpty()) {
        return redirect()->back()->with(['errorText' => 'Sorry, we could not find an order associated with that confirmation number.']);
      }

    foreach ($customersOrder as $co) {
      $changeRequestData = array(
        "confirmation_number" => $input['confirmationNumber'],
        "changes" => $input['changeDescription'],
        "tournament_id" => $co->tournament_id,
        "hotel_id" => $co->hotel_id
      );

      $change_order = ChangeOrder::create($changeRequestData);
    }

      return redirect()->back()->with(['success' => 'Change Request Form Submit Successfully. You should recieve an email about your change request soon.']);
    }

    public function getChangeRequestData(Request $request) {

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

      $totalRecords = \App\Customer::select('count(*) as allcount')->count();
      $totalRecordswithFilter = \App\Customer::select('count(*) as allcount')->where('first_name', 'like', '%' .$searchValue . '%')->count();

        $records = DB::table('customers')
        ->join('customer_orders', 'customers.id', '=', 'customer_orders.customer_id')
        ->join('team_requests', 'customers.team_request_id', '=', 'team_requests.id')
        ->join('tournaments', 'team_requests.tournament_id', '=', 'tournaments.id')
        ->select('customers.id', 'customers.first_name', 'customers.last_name', 'team_requests.team_name', 'tournaments.name', 'customer_orders.confirmation_number', 'customer_orders.total', 'customer_orders.remaining', 'customer_orders.total_paid', 'customer_orders.deposit_amount', 'customer_orders.order_date')
        ->orderBy($columnName,$columnSortOrder)
        ->where(DB::raw('concat(customers.first_name," ",customers.last_name)'), 'like', '%' .$searchValue . '%')
        ->orWhere('customer_orders.confirmation_number', '=', $searchValue)
        ->orWhere('team_requests.team_name', '=', 'like', '%' .$searchValue . '%')
        ->where('customer_orders.refunded', '=', 0)
        ->skip($start)
        ->take($rowperpage)
        ->get();

      $data_arr = array();
      
      foreach($records as $record){
        $id = $record->id;
        $name = $record->name;
        $start_date = date('m-d-Y', strtotime($record->start_date));
        $end_date = date('m-d-Y', strtotime($record->end_date));
        $slug = $record->slug;

        $data_arr[] = array(
          "id" => $id,
          "name" => $name,
          "start_date" => $start_date,
          "slug" => $slug
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

    public function cancelRequestForm() {

      return view('/cancel-order');
    }

    public function submitCancelRequestForm(Request $request) {

      $request->validate([
        'confirmationNumber' => 'required|exists:customer_orders,confirmation_number',
        'cancellationDescription' => 'required',
      ]);

      $input = $request->all();

      $customersOrder = CustomerOrder::where('confirmation_number', '=', $input['confirmationNumber'])->get();
      
      Log::info($customersOrder);

      if ($customersOrder->isEmpty()) {
        return redirect()->back()->with(['errorText' => 'Sorry, we could not find an order associated with that confirmation number.']);
      }

    foreach ($customersOrder as $co) {
      $cancelRequestData = array(
        "confirmation_number" => $input['confirmationNumber'],
        "cancellation_description" => $input['cancellationDescription'],
        "tournament_id" => $co->tournament_id,
        "hotel_id" => $co->hotel_id
      );

      $change_order = CancelOrder::create($cancelRequestData);
    }

      return redirect()->back()->with(['success' => 'Cancellation Request Form Submit Successfully. You should recieve an email about your cancel request soon.']);
    }

    public function getCancelRequestData(Request $request) {

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

      $totalRecords = \App\Customer::select('count(*) as allcount')->count();
      $totalRecordswithFilter = \App\Customer::select('count(*) as allcount')->where('first_name', 'like', '%' .$searchValue . '%')->count();

        $records = DB::table('customers')
        ->join('customer_orders', 'customers.id', '=', 'customer_orders.customer_id')
        ->join('team_requests', 'customers.team_request_id', '=', 'team_requests.id')
        ->join('tournaments', 'team_requests.tournament_id', '=', 'tournaments.id')
        ->select('customers.id', 'customers.first_name', 'customers.last_name', 'team_requests.team_name', 'tournaments.name', 'customer_orders.confirmation_number', 'customer_orders.total', 'customer_orders.remaining', 'customer_orders.total_paid', 'customer_orders.deposit_amount', 'customer_orders.order_date')
        ->orderBy($columnName,$columnSortOrder)
        ->where(DB::raw('concat(customers.first_name," ",customers.last_name)'), 'like', '%' .$searchValue . '%')
        ->orWhere('customer_orders.confirmation_number', '=', $searchValue)
        ->orWhere('team_requests.team_name', '=', 'like', '%' .$searchValue . '%')
        ->where('customer_orders.refunded', '=', 0)
        ->skip($start)
        ->take($rowperpage)
        ->get();

      $data_arr = array();
      
      foreach($records as $record){
        $id = $record->id;
        $name = $record->name;
        $start_date = date('m-d-Y', strtotime($record->start_date));
        $end_date = date('m-d-Y', strtotime($record->end_date));
        $slug = $record->slug;

        $data_arr[] = array(
          "id" => $id,
          "name" => $name,
          "start_date" => $start_date,
          "slug" => $slug
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
    
    public function getTournaments(Request $request) {

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
     $totalRecords = \App\Tournament::select('count(*) as allcount')->count();
     $totalRecordswithFilter = \App\Tournament::select('count(*) as allcount')->where('name', 'like', '%' .$searchValue . '%')->count();

     // Fetch records
     $records = \App\Tournament::orderBy($columnName,$columnSortOrder)
       ->where('tournaments.name', 'like', '%' .$searchValue . '%')
       ->select('tournaments.*')
       ->skip($start)
       ->take($rowperpage)
       ->get();

     $data_arr = array();
     
     foreach($records as $record){
        $id = $record->id;
        $name = $record->name;
        $start_date = date('m-d-Y', strtotime($record->start_date));
        $end_date = date('m-d-Y', strtotime($record->end_date));
        $slug = $record->slug;

        $data_arr[] = array(
          "id" => $id,
          "name" => $name,
          "start_date" => $start_date,
          "slug" => $slug
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

   public function frontendTournamentPage($slug) {
    $tournament = \App\Tournament::where('slug', '=', $slug)->firstOrFail();

    $hotels = \App\Hotel::all();

    $tournamentHotelRooms = \App\TournamentHotelRoom::where('tournament_id', '=', $tournament->id)->get();

    $hotelsNotIn = $tournament->availableHotels();
    //$hotelsNotIn = "";

    //$tournamentHotelRooms = 

    //, compact('tournaments')
    Log::info($tournament->tournament_type_id);
    
    if ($tournament->tournament_type_id == 1) {
	    return view('/frontend-tournament', compact('tournament', 'hotels', 'tournamentHotelRooms'));
	    Log::info('type 1');
    } elseif ($tournament->tournament_type_id == 2) {
	    return view('/frontend-tournament-singles', compact('tournament', 'hotels', 'tournamentHotelRooms'));
    }
    
   }

   public function frontendTournamentApplicationPage($slug) {

    $tournament = \App\Tournament::where('slug', '=', $slug)->firstOrFail();

    $hotels = \App\Hotel::all();

    $tournamentHotelRooms = \App\TournamentHotelRoom::where('tournament_id', '=', $tournament->id)->get();

    
    return view('/tournament-team-application-form', compact('tournament', 'hotels', 'tournamentHotelRooms'));
   }

   public function teamMembersSelectRoomPage($slug, Request $request) {

    $teamUUID = $request->query('tuuid');

    $teamRequest = \App\TeamRequest::where('uuid', '=', $teamUUID)->firstOrFail();

    $tournament = \App\Tournament::where('slug', '=', $slug)->firstOrFail();

    $hotels = \App\Hotel::all();

    $tournamentHotelRooms = \App\TournamentHotelRoom::where('tournament_id', '=', $tournament->id)->get();

    $siteTransactionFee = \App\SiteTransactionFee::find(1);

    $siteFee = $siteTransactionFee->amount;

    $sitePercentFee = $siteTransactionFee->percent;

    $teamRoomAvailableCount = 0;

    foreach( $teamRequest->teamRequestRooms as $teamRequestRoom) {
            if ( $teamRequestRoom->available >= 1) {

              $teamRoomAvailableCount++;

            }
    }



                            

    //$testString = uniqid();

    $states = $this->getStates();

    //$status = "not";
    
    return view('/tournament-team-members-form', compact('tournament', 'hotels', 'tournamentHotelRooms', 'teamRequest', 'states', 'siteFee', 'sitePercentFee', 'teamRoomAvailableCount'));
   }

   public function termsAndConditions() {
     return (view('/terms-and-conditions'));
   }

   public function getStates() {
    
    $states = array(
        'AL'=>'Alabama',
        'AK'=>'Alaska',
        'AZ'=>'Arizona',
        'AR'=>'Arkansas',
        'CA'=>'California',
        'CO'=>'Colorado',
        'CT'=>'Connecticut',
        'DE'=>'Delaware',
        'DC'=>'District of Columbia',
        'FL'=>'Florida',
        'GA'=>'Georgia',
        'HI'=>'Hawaii',
        'ID'=>'Idaho',
        'IL'=>'Illinois',
        'IN'=>'Indiana',
        'IA'=>'Iowa',
        'KS'=>'Kansas',
        'KY'=>'Kentucky',
        'LA'=>'Louisiana',
        'ME'=>'Maine',
        'MD'=>'Maryland',
        'MA'=>'Massachusetts',
        'MI'=>'Michigan',
        'MN'=>'Minnesota',
        'MS'=>'Mississippi',
        'MO'=>'Missouri',
        'MT'=>'Montana',
        'NE'=>'Nebraska',
        'NV'=>'Nevada',
        'NH'=>'New Hampshire',
        'NJ'=>'New Jersey',
        'NM'=>'New Mexico',
        'NY'=>'New York',
        'NC'=>'North Carolina',
        'ND'=>'North Dakota',
        'OH'=>'Ohio',
        'OK'=>'Oklahoma',
        'OR'=>'Oregon',
        'PA'=>'Pennsylvania',
        'RI'=>'Rhode Island',
        'SC'=>'South Carolina',
        'SD'=>'South Dakota',
        'TN'=>'Tennessee',
        'TX'=>'Texas',
        'UT'=>'Utah',
        'VT'=>'Vermont',
        'VA'=>'Virginia',
        'WA'=>'Washington',
        'WV'=>'West Virginia',
        'WI'=>'Wisconsin',
        'WY'=>'Wyoming',
    );

    return $states;
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
