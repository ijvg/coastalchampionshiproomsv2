<?php

namespace App\Http\Controllers;

use App\Customer;
use App\CustomerOrder;
use App\CustomerRoom;
use App\CustomerTransaction;
use App\CustomerTransactionError;
use App\Tournament;
use App\TeamRequestRoom;
use App\TournamentHotelRoom;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

use App\Mail\SendMail;
use Mail;

use App\Exports\TournamentOrdersExport;
use App\Exports\AccountingDataExport;
use Excel;

define("APPROVED", 1);
define("DECLINED", 2);
define("ERROR", 3);

class TransactionsController extends Controller
{
	
	public function exportAccountingData(Request $request) {
		Log::info($request->tournamentID);
		return (new AccountingDataExport($request->tournamentID))->download('accountingData.xlsx');
	}

	public function exportTournamentOrders(Request $request) {	
		Log::info($request->tournamentID);
		return (new TournamentOrdersExport($request->tournamentID))->download('tournamentOrders.xlsx');
	}
    
    public function transactionsManager () {

        $tournaments = Tournament::all();

        return view('/vendor/voyager/transactions-manager', compact('tournaments'));
    }

    public function ordersManager () {
        $tournaments = Tournament::all();

		$exportData = DB::table('customer_orders')
		->join('customers', 'customer_orders.customer_id', '=', 'customers.id')
		->join('customer_rooms', 'customers.id', '=', 'customer_rooms.customer_id')
		->join('team_request_rooms', 'customer_rooms.team_request_room_id', '=', 'team_request_rooms.id')
		->join('team_requests', 'customers.team_request_id', '=', 'team_requests.id')
		->join('tournaments', 'customer_orders.tournament_id', '=', 'tournaments.id')
		->join('hotels', 'customer_orders.hotel_id', '=', 'hotels.id')
		->join('tournament_hotel_rooms', 'team_request_rooms.tournament_hotel_room_id', '=', 'tournament_hotel_rooms.id')
		->join('hotel_room_types', 'tournament_hotel_rooms.hotel_room_type_id', '=', 'hotel_room_types.id')
		->select('customers.first_name', 'customers.last_name', 'customers.phone_number', 'customers.email', 'customer_rooms.check_in', 'customer_rooms.check_out', 'hotel_room_types.type', 'tournament_hotel_rooms.gross_price_per_night', 'customer_rooms.quantity', 'customer_rooms.number_of_nights', 'customer_orders.special_request', 'tournaments.name as tournament', 'hotels.name as hotel', 'team_requests.team_name', 'team_requests.contact_first_name', 'team_requests.contact_last_name', 'team_requests.phone_number as manager_phone_number', 'team_requests.email as manager_email')
		//->groupby('customers.id')
		->where('tournaments.id', '=', '3')->get();
		
		/*->join('customer_orders', 'customers.id', '=', 'customer_orders.customer_id')
        ->join('team_requests', 'customers.team_request_id', '=', 'team_requests.id')
        ->join('tournaments', 'team_requests.tournament_id', '=', 'tournaments.id')
        ->select('customers.id', 'customers.first_name', 'customers.last_name', 'team_requests.team_name', 'tournaments.name', 'customer_orders.confirmation_number', 'customer_orders.total', 'customer_orders.remaining', 'customer_orders.total_paid', 'customer_orders.deposit_amount', 'customer_orders.order_date')*/

        return view('/vendor/voyager/orders-manager', compact('tournaments', 'exportData'));
    }


    public function createTransaction(Request $request) {

        $transactionType = $request->get('type');

        

        $amount = null;

        if ($transactionType != 3) {
            $amount = $request->get('amount');
        }
        
        $customerVaultResponse = null;

        $response = null;
        
         //Log::info($transactionType);
         
        

        $customer = Customer::find($request->get('customerID'));
        
        $customerRoom = CustomerRoom::where('customer_id', "=", $customer->id)->firstOrFail();
        
      
        $customerOrder = CustomerOrder::where('customer_id', '=', $request->get('customerID'))->firstOrFail();



        $hotel = $customer->getHotel();
        
        Log::info($request->get('customerID'));

        $tournament = $customer->getTournament();

        $gw = new gwapi;
        

        /*(if ($transactionType == 1) {
            //Old Full Amount Charge

            $orderDescription = "Full amount charged for rooms purchased at Championship City Rooms LLC for rooms at the " . $hotel->name . " for the " . $tournament->name . " Tournament";

            $query  = "";

            $query .= "security_key=" . urlencode('CvH82Yys2vGcvrX2974c9zF8852Cqad8') . "&";
            // Transaction Information

            $query .= "customer_vault_id=" . urlencode($customer->ccv_id) . "&";

            $query .= "amount=" . urlencode($amount) . "&";

            $query .= "currency=" . urlencode('USD') . "&";

            $query .= "order_description=" . urlencode($orderDescription) . "&";

            $query .= "stored_credential_indicator=used&";

            $status = $gw->doVaultCharge($query);

            if ($gw->responses['response'] == 1) {

                $transactionID = $gw->responses['transactionid'];

                $approved = 1;

                $customerTransactionData = array(
                    "customer_id" => $customer->id,
                    "tournament_id" => $tournament->id,
                    "hotel_id" => $hotel->id,
                    "transaction_type_id" => $transactionType,
                    "amount" => $amount,
                    "transaction_id" => $transactionID,
                    "approved" => $approved
                );

                //Log::info(json_encode($customerTransactionData));

                $customerTransaction = CustomerTransaction::create($customerTransactionData);

                $customerOrder->total_paid = $amount;
                $customerOrder->deposit_amount = 0;
                $customerOrder->remaining = 0;

                $customerOrder->save();

                $response = array(
                    "message" => 'Full Amount Charge Complete',
                    "responseType" => 'success'
                );

            } elseif ($gw->responses['response'] == 2) {

                $transactionID = 0;

                if ($gw->responses['transactionid'] != null) {
                    $transactionID = $gw->responses['transactionid'];
                }

                $customerTransactionData = array(
                    "customer_id" => $customer->id,
                    "tournament_id" => $tournament->id,
                    "hotel_id" => $hotel->id,
                    "transaction_type_id" => $transactionType,
                    "approved" => "0",
                    "amount" => $amount,
                    "transaction_id" => $transactionID
                );

                $customerTransaction = CustomerTransaction::create($customerTransactionData);

                $response = array(
                    "message" => 'Full Amount Charge Declined',
                    "responseType" => 'declined'
                );


            } elseif ($gw->responses['response'] == 3) {

                $transactionErrorData = array(
                    "customer_id" => $customer->id,
                    "error_given" => $gw->responses['responsetext']
                );

                $transactionError = CustomerTransactionError::create($transactionErrorData);

                $response = array(
                    "message" => 'Error During Full Amount Charge',
                    "responseType" => 'error'
                );

            }

        } else*/
        

        if ($transactionType == 2) {
            /* DEPOSIT */

            $depositAmount = $amount / $customerRoom->number_of_nights;

            $orderDescription = "Deposit charged for rooms purchased at Championship City Rooms LLC for rooms at the " . $hotel->name . " for the " . $tournament->name . " Tournament";

            $query  = "";

            $query .= "security_key=" . urlencode('CvH82Yys2vGcvrX2974c9zF8852Cqad8') . "&";
            // Transaction Information

            $query .= "customer_vault_id=" . urlencode($customer->ccv_id) . "&";

            $query .= "amount=" . urlencode($depositAmount) . "&";

            $query .= "currency=" . urlencode('USD') . "&";

            $query .= "order_description=" . urlencode($orderDescription) . "&";

            $query .= "stored_credential_indicator=used&";

            $status = $gw->doVaultCharge($query);

            //Log::info($query);
            Log::info(json_encode($gw->responses));

            if ($gw->responses['response'] == 1) {

                $transactionID = $gw->responses['transactionid'];

                $approved = 1;

                $customerTransactionData = array(
                    "customer_id" => $customer->id,
                    "tournament_id" => $tournament->id,
                    "hotel_id" => $hotel->id,
                    "transaction_type_id" => $transactionType,
                    "amount" => $depositAmount,
                    "transaction_id" => $transactionID,
                    "approved" => $approved
                );

                //Log::info(json_encode($customerTransactionData));

                $customerTransaction = CustomerTransaction::create($customerTransactionData);

                $customerOrder->total_paid = $depositAmount;
                $customerOrder->deposit_amount = $depositAmount;
                $customerOrder->remaining = $customerOrder->remaining - $depositAmount;
                
                
                $customerName = $customer->first_name . ' ' . $customer->last_name;
                $customerEmail = $customer->email;
                
                \Mail::send('orderTransactionMail', array(
                'orderRemaining' => $customerOrder->remaining,
                'amountCharged' => $depositAmount,
	            ), function($message) use ($request, $customerName, $customerEmail){
	                //$message->to('caleb@vgnet.com', 'Admin')->subject($request->get('subject'));
	                $message->to('' . $customerEmail, '' . $customerName )->subject('Championship City Rooms Order Charge for Deposit');
	            });
                

                $customerOrder->save();

                $response = array(
                    "message" => 'Deposit Charge Complete',
                    "responseType" => 'success'
                );

            } elseif ($gw->responses['response'] == 2) {
                /* DENIED */

                $transactionID = 0;

                if ($gw->responses['transactionid'] != null) {
                    $transactionID = $gw->responses['transactionid'];
                }

                $customerTransactionData = array(
                    "customer_id" => $customer->id,
                    "tournament_id" => $tournament->id,
                    "hotel_id" => $hotel->id,
                    "transaction_type_id" => $transactionType,
                    "approved" => "0",
                    "amount" => $depositAmount,
                    "transaction_id" => $transactionID
                );

                $customerTransaction = CustomerTransaction::create($customerTransactionData);

                $response = array(
                    "message" => 'Deposit Charge Declined',
                    "responseType" => 'declined'
                );


            } elseif ($gw->responses['response'] == 3) {

                $transactionErrorData = array(
                    "customer_id" => $customer->id,
                    "error_given" => $gw->responses['responsetext']
                );

                $transactionError = CustomerTransactionError::create($transactionErrorData);

                $response = array(
                    "message" => 'Error During Deposit Charge',
                    "responseType" => 'error'
                );

            }

            

        } elseif ($transactionType == 3) {
            /* Refund / Cancel */

            $customerTransactionsToRefund = CustomerTransaction::where('customer_id', '=', $customer->id)->get();

            $gw->setLogin("CvH82Yys2vGcvrX2974c9zF8852Cqad8");

            $refundResponses = array();

            $refundedAmount = 0;

            foreach($customerTransactionsToRefund as $ctf) {

                    $gw->doRefund($ctf->transaction_id, $ctf->amount);

                    if ($gw->responses['response'] == 1) {

                        $transactionID = $gw->responses['transactionid'];
        
                        $approved = 1;

                        $refundedAmount = $refundedAmount + $ctf->amount;
        
                        $customerTransactionData = array(
                            "customer_id" => $customer->id,
                            "tournament_id" => $tournament->id,
                            "hotel_id" => $hotel->id,
                            "transaction_type_id" => $transactionType,
                            "amount" => $ctf->amount,
                            "transaction_id" => $transactionID,
                            "approved" => $approved
                        );
        
                        //Log::info(json_encode($customerTransactionData));
        
                        $customerTransaction = CustomerTransaction::create($customerTransactionData);
        
                        array_push($refundResponses, $gw->responses['response']);
        
                    } elseif ($gw->responses['response'] == 2) {
        
                        $transactionID = 0;
        
                        if ($gw->responses['transactionid'] != null) {
                            $transactionID = $gw->responses['transactionid'];
                        }
        
                        $customerTransactionData = array(
                            "customer_id" => $customer->id,
                            "tournament_id" => $tournament->id,
                            "hotel_id" => $hotel->id,
                            "transaction_type_id" => $transactionType,
                            "approved" => "0",
                            "amount" => $ctf->amount,
                            "transaction_id" => $transactionID
                        );
        
                        $customerTransaction = CustomerTransaction::create($customerTransactionData);
        
                        
                        array_push($refundResponses, $gw->responses['response']);
        
                    } elseif ($gw->responses['response'] == 3) {
        
                        $transactionErrorData = array(
                            "customer_id" => $customer->id,
                            "error_given" => $gw->responses['responsetext']
                        );
        
                        $transactionError = CustomerTransactionError::create($transactionErrorData);

                        array_push($refundResponses, $gw->responses['response']);
        
                    }

            }


            if (in_array(2, $refundResponses)) {

                $response = array(
                    "message" => '1 or more Refund(s) Declined',
                    "responseType" => 'declined'
                );

            } elseif (in_array(3, $refundResponses)) {
                $response = array(
                    "message" => '1 or more Errors During Refund(s)',
                    "responseType" => 'error'
                );
            } else {
				
				
				$orderID = $customerOrder->id;
				
				$team_request_room_id = 0;
				
				$tournament_hotel_room_id = 0;
				
				if ($customerRoom->team_request_room_id != null) {
					$team_request_room_id = $customerRoom->team_request_room_id;
					
					$team_request_room = TeamRequestRoom::where('id', "=", $team_request_room_id)->firstOrFail();
					
					$team_request_room->allocated = $team_request_room->allocated - $customerRoom->quantity;
					
					$team_request_room->available = $team_request_room->available + $customerRoom->quantity;
					
					$tournament_hotel_room = TournamentHotelRoom::where('id', "=", $team_request_room->tournament_hotel_room_id)->firstOrFail();
					
					$tournament_hotel_room->allocated = $tournament_hotel_room->allocated - $customerRoom->quantity;
					
					$tournament_hotel_room->held = $tournament_hotel_room->held + $customerRoom->quantity;
					
					$team_request_room->save();
					
					$tournament_hotel_room->save();
					
				} else {
					$tournament_hotel_room_id = $customerRoom->tournament_hotel_room_id;
					
					$tournament_hotel_room = TournamentHotelRoom::where('id', "=", $tournament_hotel_room_id)->firstOrFail();
					
					$tournament_hotel_room->allocated = $tournament_hotel_room->allocated - $customerRoom->quantity;
					
					$tournament_hotel_room->held = $tournament_hotel_room->held + $customerRoom->quantity;
					
					$tournament_hotel_room->save();
					
				}
				
				
				

                $customerOrder->refunded = 1;
                $customerOrder->canceled = 1;
                $customerOrder->refund_date = Carbon::now();
                $customerOrder->refunded_amount = $refundedAmount;

                $customerOrder->save();
                
                
                $customerName = $customer->first_name . ' ' . $customer->last_name;
                $customerEmail = $customer->email;
                
                \Mail::send('orderRefundMail', array(
                'refundAmount' => $refundedAmount,
	            ), function($message) use ($request, $customerName, $customerEmail){
	                //$message->to('caleb@vgnet.com', 'Admin')->subject($request->get('subject'));
	                $message->to('' . $customerEmail, '' . $customerName )->subject('Championship City Rooms Order Refund');
	            });

                $response = array(
                    "message" => 'Refund(s) Complete',
                    "responseType" => 'success'
                );
            }
            

        } elseif ($transactionType == 4 || $transactionType == 1) {
             /* Remaining OR FULL */

            
            $typeName = "";

            if ($transactionType == 1) {
                $typeName = "Full";
            } else {
                $typeName = "Remaining";
            }

            $orderDescription = $typeName . " amount charged for rooms purchased at Championship City Rooms LLC for rooms at the " . $hotel->name . " for the " . $tournament->name . " Tournament";

            $query  = "";

            $query .= "security_key=" . urlencode('CvH82Yys2vGcvrX2974c9zF8852Cqad8') . "&";
            // Transaction Information

            $query .= "customer_vault_id=" . urlencode($customer->ccv_id) . "&";

            $query .= "amount=" . urlencode($amount) . "&";

            $query .= "currency=" . urlencode('USD') . "&";

            $query .= "order_description=" . urlencode($orderDescription) . "&";

            $query .= "stored_credential_indicator=used&";

            $status = $gw->doVaultCharge($query);

            if ($gw->responses['response'] == 1) {

                $transactionID = $gw->responses['transactionid'];

                $approved = 1;

                $customerTransactionData = array(
                    "customer_id" => $customer->id,
                    "tournament_id" => $tournament->id,
                    "hotel_id" => $hotel->id,
                    "transaction_type_id" => $transactionType,
                    "amount" => $amount,
                    "transaction_id" => $transactionID,
                    "approved" => $approved
                );

                //Log::info(json_encode($customerTransactionData));

                $customerTransaction = CustomerTransaction::create($customerTransactionData);


                if ($transactionType == 1) {
                    $customerOrder->total_paid = $amount;
                    $customerOrder->deposit_amount = 0;
                    $customerOrder->remaining = 0;
                } else {
                    $customerOrder->total_paid = $amount;
                    //$customerOrder->deposit_amount = 0;
                    $customerOrder->remaining = 0;
                }
                

                $customerOrder->save();

                $response = array(
                    "message" => $typeName . ' Amount Charge Complete',
                    "responseType" => 'success'
                );
                
                
                $customerName = $customer->first_name . ' ' . $customer->last_name;
                $customerEmail = $customer->email;
                
                \Mail::send('orderTransactionMail', array(
                'orderRemaining' => $customerOrder->remaining,
                'amountCharged' => $amount,
	            ), function($message) use ($request, $customerName, $customerEmail){
	                //$message->to('caleb@vgnet.com', 'Admin')->subject($request->get('subject'));
	                $message->to('' . $customerEmail, '' . $customerName )->subject('Championship City Rooms Order Charge');
	            });

                
                

            } elseif ($gw->responses['response'] == 2) {

                $transactionID = 0;

                if ($gw->responses['transactionid'] != null) {
                    $transactionID = $gw->responses['transactionid'];
                }

                $customerTransactionData = array(
                    "customer_id" => $customer->id,
                    "tournament_id" => $tournament->id,
                    "hotel_id" => $hotel->id,
                    "transaction_type_id" => $transactionType,
                    "approved" => "0",
                    "amount" => $amount,
                    "transaction_id" => $transactionID
                );

                $customerTransaction = CustomerTransaction::create($customerTransactionData);

                $response = array(
                    "message" => $typeName . ' Amount Charge Declined',
                    "responseType" => 'declined'
                );


            } elseif ($gw->responses['response'] == 3) {

                $transactionErrorData = array(
                    "customer_id" => $customer->id,
                    "error_given" => $gw->responses['responsetext']
                );

                $transactionError = CustomerTransactionError::create($transactionErrorData);

                $response = array(
                    "message" => 'Error During ' . $typeName . ' Amount Charge',
                    "responseType" => 'error'
                );

            }
        }

        //Log::info(json_encode($response));

        echo json_encode($response);
        exit;
    }


    public function getCustomersAccountData(Request $request) {

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


        $totalRecords = \App\Customer::select('count(*) as allcount')->count();
        $totalRecordswithFilter = \App\Customer::select('count(*) as allcount')->where('first_name', 'like', '%' .$searchValue . '%')->count();

        $records = DB::table('customers')
        ->join('customer_orders', 'customers.id', '=', 'customer_orders.customer_id')
        //->leftJoin('team_requests', 'customers.team_request_id', '=', 'team_requests.id')
        ->join('tournaments', 'customer_orders.tournament_id', '=', 'tournaments.id')
        //->select('customers.id', 'customers.first_name', 'customers.last_name', 'team_requests.team_name', 'tournaments.name', 'customer_orders.confirmation_number', 'customer_orders.total', 'customer_orders.remaining', 'customer_orders.total_paid', 'customer_orders.deposit_amount', 'customer_orders.order_date')
        ->select('customers.id', 'customers.first_name', 'customers.last_name', 'tournaments.name', 'customer_orders.confirmation_number', 'customer_orders.total', 'customer_orders.remaining', 'customer_orders.total_paid', 'customer_orders.deposit_amount', 'customer_orders.order_date')
        ->orderBy($columnName,$columnSortOrder)
        //->where('customers.first_name', 'like', '%' .$searchValue . '%')
        //->where(DB::raw("CONCAT('customers.first_name', ' ', 'customers.last_name')"), 'like', '%' .$searchValue . '%')
        ->where(DB::raw('concat(customers.first_name," ",customers.last_name)'), 'like', '%' .$searchValue . '%')
        ->orWhere('customer_orders.confirmation_number', '=', $searchValue)
        //->orWhere('team_requests.team_name', '=', 'like', '%' .$searchValue . '%')
        ->where('customer_orders.refunded', '=', 0)
        ->where('customer_orders.canceled', '=', 0)
        ->skip($start)
        ->take($rowperpage)
        ->get();
        // Fetch records
        /*$records = \App\Customer::orderBy($columnName,$columnSortOrder)
        ->where('customers.first_name', 'like', '%' .$searchValue . '%')
        ->select('customers.*')
        ->skip($start)
        ->take($rowperpage)
        ->get();*/

        $data_arr = array();
        
        foreach($records as $record){
        $id = $record->id;
        //$customer = $record->first_name . ' ' . $record->last_name;
        $first_name = $record->first_name;
        $last_name = $record->last_name;
        //$teamName = $record->team_name;
        $tournament = $record->name;
        $confirmationNumber = $record->confirmation_number;
        $total = $record->total;
        $totalPaid = $record->total_paid;
        $remaining = $record->remaining;
        $orderDate = $record->order_date;
        $depositAmount = $record->deposit_amount;

        //"customer" => $customer,
        $data_arr[] = array(
            "first_name" => $first_name,
            "last_name" => $last_name,
            //"team_name" => $teamName,
            "name" => $tournament,
            "confirmation_number" => $confirmationNumber,
            "total" => $total,
            "remaining" => $remaining,
            "order_date" => $orderDate,
            "id" => $id,
            "total_paid" => $totalPaid,
            "deposit_amount" => $depositAmount
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

    public function getApprovedTransactionsData(Request $request) {

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


        $totalRecords = \App\Customer::select('count(*) as allcount')->count();
        $totalRecordswithFilter = \App\Customer::select('count(*) as allcount')->where('first_name', 'like', '%' .$searchValue . '%')->count();

        //->join('customer_orders', 'customers.id', '=', 'customer_orders.customer_id')
        $records = DB::table('customers')
        ->join('customer_transactions', 'customers.id', '=', 'customer_transactions.customer_id')
        //->join('team_requests', 'customers.team_request_id', '=', 'team_requests.id')
        ->join('tournaments', 'customer_transactions.tournament_id', '=', 'tournaments.id')
        
        /*->select('customers.id', 'customers.first_name', 'customers.last_name', 'team_requests.team_name', 'tournaments.name', 'customer_transactions.transaction_type_id', 
        'customer_transactions.transaction_id', 'customer_transactions.amount', 'customer_transactions.created_at')*/
        ->select('customers.id', 'customers.first_name', 'customers.last_name', 'tournaments.name', 'customer_transactions.transaction_type_id', 
        'customer_transactions.transaction_id', 'customer_transactions.amount', 'customer_transactions.created_at')
        ->orderBy($columnName,$columnSortOrder)
        //->where('customers.first_name', 'like', '%' .$searchValue . '%')
        ->where(DB::raw('concat(customers.first_name," ",customers.last_name)'), 'like', '%' .$searchValue . '%')
        //->orWhere('team_requests.team_name', '=', 'like', '%' .$searchValue . '%')
        ->where('customer_transactions.approved', '=', 1)
        ->skip($start)
        ->take($rowperpage)
        ->get();

        //'customer_orders.confirmation_number', 'customer_orders.total', 'customer_orders.remaining', 'customer_orders.total_paid', 'customer_orders.deposit_amount', 'customer_orders.order_date'

        // Fetch records
        /*$records = \App\Customer::orderBy($columnName,$columnSortOrder)
        ->where('customers.first_name', 'like', '%' .$searchValue . '%')
        ->select('customers.*')
        ->skip($start)
        ->take($rowperpage)
        ->get();*/

        $data_arr = array();
        
        //$customer = $record->first_name . ' ' . $record->last_name;
        foreach($records as $record){
        $id = $record->id;
        $first_name = $record->first_name;
        $last_name = $record->last_name;
        //$teamName = $record->team_name;
        $tournament = $record->name;
        $transactionID = $record->transaction_id;
        $transactionTypeID = $record->transaction_type_id;
        $amount = $record->amount;
        $createdAt = $record->created_at;

        //"customer" => $customer,
        $data_arr[] = array(
            "first_name" => $first_name,
            "last_name" => $last_name,
            //"team_name" => $teamName,
            "name" => $tournament,
            "transaction_id" => $transactionID,
            "transaction_type_id" => $transactionTypeID,
            "amount" => $amount,
            "created_at" => $createdAt,
            "id" => $id
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
    
    public function getCancelRequests(Request $request) {
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
        ->join('cancel_orders', 'customer_orders.confirmation_number', '=', 'cancel_orders.confirmation_number')
        ->join('tournaments', 'customer_orders.tournament_id', '=', 'tournaments.id')
        ->select('customers.id', 'customers.first_name', 'customers.last_name', 'tournaments.name', 'cancel_orders.confirmation_number', 
        'cancel_orders.cancellation_description', 'cancel_orders.created_at')
        
        ->orderBy($columnName,$columnSortOrder)
        ->where(DB::raw('concat(customers.first_name," ",customers.last_name)'), 'like', '%' .$searchValue . '%')
        ->skip($start)
        ->take($rowperpage)
        ->get();


        $data_arr = array();
        
        //$customer = $record->first_name . ' ' . $record->last_name;
        foreach($records as $record){
        $id = $record->id;
        $first_name = $record->first_name;
        $last_name = $record->last_name;
        //$teamName = $record->team_name;
        $tournament = $record->name;
        $confirmation_number = $record->confirmation_number;
        $reason = $record->cancellation_description;
        $createdAt = $record->created_at;

        //"customer" => $customer,
        $data_arr[] = array(
            "first_name" => $first_name,
            "last_name" => $last_name,
            //"team_name" => $teamName,
            "name" => $tournament,
            "confirmation_number" => $confirmation_number,
            "reason" => $reason,
            "created_at" => $createdAt,
            "id" => $id
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
	
	public function getChangeOrders(Request $request) {
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
        ->join('change_orders', 'customer_orders.confirmation_number', '=', 'change_orders.confirmation_number')
        ->join('tournaments', 'customer_orders.tournament_id', '=', 'tournaments.id')
        ->select('customers.id', 'customers.first_name', 'customers.last_name', 'tournaments.name', 'change_orders.confirmation_number', 
        'change_orders.changes', 'change_orders.created_at')
        
        ->orderBy($columnName,$columnSortOrder)
        ->where(DB::raw('concat(customers.first_name," ",customers.last_name)'), 'like', '%' .$searchValue . '%')
        ->skip($start)
        ->take($rowperpage)
        ->get();


        $data_arr = array();
        
        //$customer = $record->first_name . ' ' . $record->last_name;
        foreach($records as $record){
        $id = $record->id;
        $first_name = $record->first_name;
        $last_name = $record->last_name;
        //$teamName = $record->team_name;
        $tournament = $record->name;
        $confirmation_number = $record->confirmation_number;
        $changes = $record->changes;
        $createdAt = $record->created_at;

        //"customer" => $customer,
        $data_arr[] = array(
            "first_name" => $first_name,
            "last_name" => $last_name,
            //"team_name" => $teamName,
            "name" => $tournament,
            "confirmation_number" => $confirmation_number,
            "changes" => $changes,
            "created_at" => $createdAt,
            "id" => $id
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

    public function getDeclinedTransactionsData(Request $request) {

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


        $totalRecords = \App\Customer::select('count(*) as allcount')->count();
        $totalRecordswithFilter = \App\Customer::select('count(*) as allcount')->where('first_name', 'like', '%' .$searchValue . '%')->count();

        //->join('customer_orders', 'customers.id', '=', 'customer_orders.customer_id')
        $records = DB::table('customers')
        ->join('customer_transactions', 'customers.id', '=', 'customer_transactions.customer_id')
        //->join('team_requests', 'customers.team_request_id', '=', 'team_requests.id')
        ->join('tournaments', 'customer_transactions.tournament_id', '=', 'tournaments.id')
        
        /*->select('customers.id', 'customers.first_name', 'customers.last_name', 'team_requests.team_name', 'tournaments.name', 'customer_transactions.transaction_type_id', 
        'customer_transactions.transaction_id', 'customer_transactions.declined_response', 'customer_transactions.amount', 'customer_transactions.created_at')*/
        ->select('customers.id', 'customers.first_name', 'customers.last_name', 'tournaments.name', 'customer_transactions.transaction_type_id', 
        'customer_transactions.transaction_id', 'customer_transactions.declined_response', 'customer_transactions.amount', 'customer_transactions.created_at')
        ->orderBy($columnName,$columnSortOrder)
        //->where('customers.first_name', 'like', '%' .$searchValue . '%')
        ->where(DB::raw('concat(customers.first_name," ",customers.last_name)'), 'like', '%' .$searchValue . '%')
        //->orWhere('team_requests.team_name', '=', 'like', '%' .$searchValue . '%')
        ->where('customer_transactions.approved', '=', 0)
        ->skip($start)
        ->take($rowperpage)
        ->get();

        //'customer_orders.confirmation_number', 'customer_orders.total', 'customer_orders.remaining', 'customer_orders.total_paid', 'customer_orders.deposit_amount', 'customer_orders.order_date'

        // Fetch records
        /*$records = \App\Customer::orderBy($columnName,$columnSortOrder)
        ->where('customers.first_name', 'like', '%' .$searchValue . '%')
        ->select('customers.*')
        ->skip($start)
        ->take($rowperpage)
        ->get();*/

        $data_arr = array();
        
        //$customer = $record->first_name . ' ' . $record->last_name;
        foreach($records as $record){
        $id = $record->id;
        $first_name = $record->first_name;
        $last_name = $record->last_name;
        //$teamName = $record->team_name;
        $tournament = $record->name;
        $transactionID = $record->transaction_id;
        $transactionTypeID = $record->transaction_type_id;
        $declinedResponse = $record->declined_response;
        $amount = $record->amount;
        $createdAt = $record->created_at;

        //"customer" => $customer,
        $data_arr[] = array(
            "first_name" => $first_name,
            "last_name" => $last_name,
            //"team_name" => $teamName,
            "name" => $tournament,
            "transaction_id" => $transactionID,
            "transaction_type_id" => $transactionTypeID,
            "amount" => $amount,
            "declined_response" => $declinedResponse,
            "created_at" => $createdAt,
            "id" => $id
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

    public function getTransactionsErrorData(Request $request) {

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


        $totalRecords = \App\Customer::select('count(*) as allcount')->count();
        $totalRecordswithFilter = \App\Customer::select('count(*) as allcount')->where('first_name', 'like', '%' .$searchValue . '%')->count();

        /*$records = DB::table('customers')
        ->join('customer_orders', 'customers.id', '=', 'customer_orders.customer_id')
        ->join('team_requests', 'customers.team_request_id', '=', 'team_requests.id')
        ->join('tournaments', 'team_requests.tournament_id', '=', 'tournaments.id')
        ->select('customers.id', 'customers.first_name', 'customers.last_name', 'team_requests.team_name', 'tournaments.name', 'customer_orders.confirmation_number', 'customer_orders.total', 'customer_orders.remaining', 'customer_orders.total_paid', 'customer_orders.deposit_amount', 'customer_orders.order_date')
        ->orderBy($columnName,$columnSortOrder)
        ->where('customers.first_name', 'like', '%' .$searchValue . '%')
        ->where('customer_orders.refunded', '=', 0)
        ->skip($start)
        ->take($rowperpage)
        ->get();*/

        $records = DB::table('customers')
        //->join('team_requests', 'customers.team_request_id', '=', 'team_requests.id')
        ->join('customer_orders', 'customers.id', '=', 'customer_orders.customer_id')
        ->join('tournaments', 'customer_orders.tournament_id', '=', 'tournaments.id')
        ->join('customer_transaction_errors', 'customers.id', '=', 'customer_transaction_errors.customer_id')
        //->select('customers.id', 'customers.first_name', 'customers.last_name', 'team_requests.team_name', 'tournaments.name', 'customer_transaction_errors.error_given', 'customer_transaction_errors.created_at')
        ->select('customers.id', 'customers.first_name', 'customers.last_name', 'tournaments.name', 'customer_transaction_errors.error_given', 'customer_transaction_errors.created_at')
        ->orderBy($columnName,$columnSortOrder)
        //->where('customers.first_name', 'like', '%' .$searchValue . '%')
        ->where(DB::raw('concat(customers.first_name," ",customers.last_name)'), 'like', '%' .$searchValue . '%')
        //->orWhere('team_requests.team_name', '=', 'like', '%' .$searchValue . '%')
        ->orWhere('tournaments.name', '=', 'like', '%' .$searchValue . '%')
        ->skip($start)
        ->take($rowperpage)
        ->get();

        // Fetch records
        /*$records = \App\Customer::orderBy($columnName,$columnSortOrder)
        ->where('customers.first_name', 'like', '%' .$searchValue . '%')
        ->select('customers.*')
        ->skip($start)
        ->take($rowperpage)
        ->get();*/

        $data_arr = array();
        
        //$customer = $record->first_name . ' ' . $record->last_name;
        foreach($records as $record){
        $id = $record->id;
        $first_name = $record->first_name;
        $last_name = $record->last_name;
        //$teamName = $record->team_name;
        $tournament = $record->name;
        $errorGiven = $record->error_given;
        $createdAt = $record->created_at;

        //"customer" => $customer,
        $data_arr[] = array(
            "first_name" => $first_name,
            "last_name" => $last_name,
            //"team_name" => $teamName,
            "name" => $tournament,
            "error_given" => $errorGiven,
            "created_at" => $createdAt,
            "id" => $id,
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

    public function storeInVault(Request $request) {

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

    }

}

class gwapi {

// Initial Setting Functions

  function setLogin($security_key) {
    $this->login['security_key'] = $security_key;
  }

  function doVaultCharge($query) {
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

    Log::info($query);
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
    $query .= "website=" . urlencode($this->billing['website']) . "&";
    
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

  

  
  
}


