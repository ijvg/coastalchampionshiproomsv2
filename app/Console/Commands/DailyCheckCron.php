<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

use App\CustomerOrder;
use App\Customer;
use App\CustomerRoom;
use App\CustomerTransaction;
use App\CustomerTransactionError;
use App\TeamRequest;
use App\TeamRequestRoom;
use App\TournamentHotelRoom;
use Carbon\Carbon;

use App\Mail\SendMail;
use Mail;

define("APPROVED", 1);
define("DECLINED", 2);
define("ERROR", 3);

class DailyCheckCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dailyCheck:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check on the status of Customer Orders, if 30 days before tournament date and has not paid deposit, pay deposit. If 7 days before tournament and has not paid full amount. Pay full amount.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        /* Handle Team Request Link Expires (on expire put available back in hotel rooms inventory) */

        /* Handle Condition if less than 30 days before tournament and deposit not paid, charge */

        /* Handle Condition if less than 7 days before tournament and full amount not paid, charge */

		Log::info('cron1');

        /*$allTournaments = \App\Tournament::all();

        foreach ($allTournaments as $tournament) {
            if ($tournament->status != 0){
                if ($tournament->end_date->isPast()) {
                    $tournament->status = 0;
                    $tournament->save();
                }
            }
        };*/


        $activeTournaments = \App\Tournament::where('status', '=', '1')->get();
        
        $now = Carbon::now();

        foreach ($activeTournaments as $activeTournament) {

            $checkInDate = $activeTournament->default_check_in;
            
            Log::info($checkInDate);

            $depositDueDate = Carbon::createFromFormat('Y-m-d', $checkInDate)->subDays(30);
            
            //Log::info('' . $depositDueDate);

            $remainingAmountDate = Carbon::createFromFormat('Y-m-d', $checkInDate)->subDays(7);
            
            Log::info($remainingAmountDate);

            if ($now->gt($depositDueDate)) {

                $ordersWithoutDeposit = \App\CustomerOrder::where('tournament_id', '=', $activeTournament->id)->where('refunded', '=', 0)->where('deposit_amount', '=', '0.00')->where('remaining', '!=', '0.00')->get();

                foreach ($ordersWithoutDeposit as $orderWithoutDeposit) {
					//Log::info($orderWithoutDeposit->total);
					//if ($orderWithoutDeposit->customer_id == 49) {
						//Log::info($orderWithoutDeposit->total);
                    //Log::info(json_encode($orderWithoutDeposit));
                    $depostChargeResponse = $this->createTransaction($orderWithoutDeposit->customer_id, $orderWithoutDeposit->total, 2);
					//}
                    Log::info('done');

                }


            }
            
            Log::info($remainingAmountDate);
            
            //Log::info($now->gt($remainingAmountDate));

            //Log::info($remainingAmountDate);

            if ($now->gt($remainingAmountDate)) {

                Log::info('in');
                $ordersNotPaid = \App\CustomerOrder::where('tournament_id', '=', $activeTournament->id)->where('refunded', '=', 0)->where('remaining', '!=', '0.00')->get();

                foreach ($ordersNotPaid as $orderNotPaid) {
                    $this->createTransaction($orderNotPaid->customer_id, $orderNotPaid->remaining, 4);

                }
            }


            /*$teamRequestsLinksExpired = \App\TeamRequest::whereDate('link_expire_date', '<=', Carbon::now()->toDateString())->where('approved', '=', 1)->where('tournament_id', '=', $activeTournament->id)
                ->get();*/

            

            /*foreach($teamRequestsLinksExpired as $teamRequest) {
                $teamRequestRooms = \App\TeamRequestRoom::where('team_request_id', '=', $teamRequest->id)->get();

                foreach ($teamRequestRooms as $teamRequestRoom) {

                    if ($teamRequestRoom->available != 0) {

                        Log::info(json_encode($teamRequestRoom));

                        $teamRequestRoomsAvailable = $teamRequestRoom->available;

                        $teamRequestRoom->quantity = $teamRequestRoom->quantity - $teamRequestRoomsAvailable;

                        $tournamentHotelRoom = \App\TournamentHotelRoom::find($teamRequestRoom->tournament_hotel_room_id);

                        $tournamentHotelRoomsHeld = $tournamentHotelRoom->held;

                        $tournamentHotelRoom->held = $tournamentHotelRoomsHeld - $teamRequestRoomsAvailable;

                        $tournamentHotelRoomsAvailable = $tournamentHotelRoom->rooms_available;

                        $tournamentHotelRoom->rooms_available = $tournamentHotelRoomsAvailable + $teamRequestRoomsAvailable;

                        $teamRequestRoom->available = 0;

                        $teamRequestRoom->save();

                        $tournamentHotelRoom->save();
                    }
                }
            }*/

        }

        
        

    }

    function chargeRequest($customerID, $amount, $transactionType) {

        $query = "";

        $query .= "customerID=" . urlencode($customerID) . "&";
        $query .= "amount=" . urlencode($amount) . "&";
        $query .= "type=" . urlencode($transactionType) . "&";
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "localhost:8000/admin/transactions/charge-remaining");
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
        unset($ch);
            //print "\n$data\n";
            $data = explode("&",$data);
            for($i=0;$i<count($data);$i++) {
            $rdata = explode("=",$data[$i]);
            $this->responses[$rdata[0]] = $rdata[1];
        }
        return APPROVED;
    }

    public function createTransaction($customerID, $amount, $transactionType) {

        //$transactionType = $request->get('type');

        Log::info($transactionType);

        //$amount = null;

        if ($transactionType != 3) {
            //$amount = $request->get('amount');
        }
        
        $customerVaultResponse = null;

        $response = null;

        $customer = Customer::find($customerID);
        $customerRoom = CustomerRoom::where('customer_id', "=", $customerID)->firstOrFail();
        $customerOrder = CustomerOrder::where('customer_id', '=', $customerID)->firstOrFail();

        $hotel = $customer->getHotel();

        $tournament = $customer->getTournament();

        $gw = new gwapi;
        

       
        if ($transactionType == 2) {
            /* DEPOSIT */

            $depositAmount = $amount / $customerRoom->number_of_nights;


            $depositAmount = number_format((float)round( $depositAmount ,2, PHP_ROUND_HALF_DOWN),2,'.','');



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

                $customerOrder->save();
                
                
                $customerName = $customer->first_name . ' ' . $customer->last_name;
                $customerEmail = $customer->email;
                
                \Mail::send('orderTransactionMail', array(
                'orderRemaining' => $customerOrder->remaining,
                'amountCharged' => $depositAmount,
	            ), function($message) use ($customerName, $customerEmail){
	                //$message->to('caleb@vgnet.com', 'Admin')->subject($request->get('subject'));
	                $message->to('' . $customerEmail, '' . $customerName )->subject('Championship City Rooms Order Charge for Deposit');
	            });

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

            Log::info($status);

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
                
                
                $customerName = $customer->first_name . ' ' . $customer->last_name;
                $customerEmail = $customer->email;
                
                \Mail::send('orderTransactionMail', array(
                'orderRemaining' => $customerOrder->remaining,
                'amountCharged' => $amount,
	            ), function($message) use ($customerName, $customerEmail){
	                //$message->to('caleb@vgnet.com', 'Admin')->subject($request->get('subject'));
	                $message->to('' . $customerEmail, '' . $customerName )->subject('Championship City Rooms Order Charge');
	            });

                $response = array(
                    "message" => $typeName . ' Amount Charge Complete',
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

        //echo json_encode($response);
        //exit;
        return;
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
        //return $this->responses['response'];
      }
}