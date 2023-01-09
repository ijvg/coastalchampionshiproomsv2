<?php

use App\Http\Controllers\RoomsController;
use Illuminate\Support\Facades\Route;
use TCG\Voyager\Facades\Voyager;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


	
//Route::middleware('maintenance')->group(function () {

Route::get('/', function () {
    //return view('welcome');
    return view('home');
});

Route::get('/page/{slug}', 'PagesController@show');

Route::get('/tournament/{slug}', 'TournamentsController@frontendTournamentPage');

Route::get('/contact-us', 'PagesController@contactUsPage');

Route::post('/contact-us', 'PagesController@sendEmailContactUs')->name('contact-form.send');

//Route::get('/test','TournamentsFrontendController@tournamentManagerSelect');

Route::get('/tournament/{slug}/tournament-application-form', 'TournamentsController@frontendTournamentApplicationPage');

Route::get('/tournament/{slug}/team-members-select-rooms-form', 'TournamentsController@teamMembersSelectRoomPage');

Route::get('/tournament/{slug}/tournament-single-application-form', 'TournamentsController@singleApplicationPage');

Route::get('/change-request-form', 'TournamentsController@changeRequestForm');

Route::post('/submit-change-request-form', 'TournamentsController@submitChangeRequestForm');

Route::get('/cancel-request-form', 'TournamentsController@cancelRequestForm');

Route::post('/submit-cancel-request-form', 'TournamentsController@submitCancelRequestForm');

Route::post('/team-requests/tr-submit-request', 'TeamRequestController@teamRequestSubmission');

Route::post('/team-requests/tmr-submit-request', 'TeamRequestController@teamMemberRequestSubmission');

Route::post('/requests/single-submit-request', 'TournamentsController@singleRequestSubmission');

Route::get('/terms-and-conditions', 'TournamentsController@termsAndConditions');


//});

Route::get('maintenance', function(){
    return view('maintenance');
});

/*
Route::get('/tournament/{slug}/team-member-form-step-one', 'TeamRequestController@createStepOneTeamMemberRequest')->name('team.member.request.create.step.one');
Route::post('/tournament/{slug}/team-member-form-step-one', 'TeamRequestController@postCreateStepOneTeamMemberRequest')->name('team.member.request.create.step.one.post');

Route::get('/tournament/{slug}/team-member-form-step-two', 'TeamRequestController@createStepTwoTeamMemberRequest')->name('team.member.request.create.step.two');
Route::post('/tournament/{slug}/team-member-form-step-two', 'TeamRequestController@postCreateStepTwoTeamMemberRequest')->name('team.member.request.create.step.two.post');*/


Route::group(['prefix' => 'admin'], function () {
    Voyager::routes();

    Route::get('/tournament-manager/get-tournaments', 'TournamentsController@getTournaments')->middleware('admin.user');

    Route::get('/tournament-manager', 'TournamentsBackendController@tournamentManagerSelect')->middleware('admin.user');

    Route::get('/tournament-manager/tournament/{slug}','TournamentsBackendController@tournamentManager')->middleware('admin.user');

    Route::post('/tournament-manager/set-default-team-request-expire-link', 'TournamentsBackendController@setTeamExpireLinkDefaultDate')->middleware('admin.user');

    Route::get('/tournament-manager/tournament/{slug}/team-request-manager', 'TeamRequestController@teamRequestMananger')->middleware('admin.user');

    Route::get('/transactions-manager', 'TransactionsController@transactionsManager')->middleware('admin.user');

    Route::get('/orders-manager', 'TransactionsController@ordersManager')->middleware('admin.user');
    
    
    Route::post('/orders-manager/export-tournament-orders', 'TransactionsController@exportTournamentOrders')->middleware('admin.user');
    
    Route::post('/orders-manager/export-accounting-data', 'TransactionsController@exportAccountingData')->middleware('admin.user');

    Route::post('/team-request/approve', 'TeamRequestController@approveTeamRequest')->middleware('admin.user');
    Route::post('/team-request/deny', 'TeamRequestController@denyTeamRequest')->middleware('admin.user');

    Route::get('/team-requests/get-approved-team-requests','TeamRequestController@getApprovedTeamRequests')->middleware('admin.user');
    Route::get('/team-requests/get-denied-team-requests','TeamRequestController@getDeniedTeamRequests')->middleware('admin.user');
    Route::get('/team-requests/get-team-request','TeamRequestController@getTeamRequest')->middleware('admin.user');
    Route::post('/team-requests/trm-update-approved-request', 'TeamRequestController@trmUpdateApprovedRequest')->middleware('admin.user');
    Route::post('/team-requests/trm-update-request', 'TeamRequestController@trmUpdateRequest')->middleware('admin.user');
    
    Route::post('/hotel-tournament/save-data', 'TournamentsBackendController@saveHotelTournamentData')->middleware('admin.user');


    Route::get('/orders/get-all-customers-data','TransactionsController@getCustomersAccountData')->middleware('admin.user');
    
    Route::get('/orders/get-cancel-requests', 'TransactionsController@getCancelRequests')->middleware('admin.user');
    
    Route::get('/orders/get-change-requests', 'TransactionsController@getChangeOrders')->middleware('admin.user');

    Route::get('/transactions/get-all-approved-transactions-data', 'TransactionsController@getApprovedTransactionsData')->middleware('admin.user');

    Route::get('/transactions/get-all-declined-transactions-data', 'TransactionsController@getDeclinedTransactionsData')->middleware('admin.user');

    Route::get('/transactions/get-all-transactions-error-data', 'TransactionsController@getTransactionsErrorData')->middleware('admin.user');

    Route::post('/hotel/add-to-tournament', 'HotelsController@assignToTournament')->middleware('admin.user');
    Route::post('/hotel/remove-from-tournament', 'HotelsController@removeFromTournament')->middleware('admin.user');


    Route::post('/room/assign-to-tournament', 'RoomsController@assignToTournament')->middleware('admin.user');
    Route::post('/room/delete-tournament-hotel-room', 'RoomsController@deleteTournamentHotelRoom')->middleware('admin.user');
    Route::post('/room/update-tournament-hotel-room', 'RoomsController@updateTournamentHotelRoom')->middleware('admin.user');

    Route::post('/transactions/charge-remaining', 'TransactionsController@createTransaction')->middleware('admin.user');

	Route::post('/tournament-manager/set-hotel-room-status', 'TeamRequestController@disableEnableHotelTournamentRoomType')->middleware('admin.user');
    
    
});