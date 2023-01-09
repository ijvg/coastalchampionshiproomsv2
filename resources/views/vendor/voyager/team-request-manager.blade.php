@extends('voyager::master')

@section('page_header')
	{{--<meta name="csrf-token" content="{{ csrf_token() }}" />--}}
    <div class="container-fluid">
		<h1 class="page-title my-page-title">{{ $tournament->name }} - Team Request Manager</h1>

		<div class="team-request-create-manual-btn-con">
			<button class="btn btn-info btn-icon-text" action="#" id="trCreateBtn">
				<i class="fas fa-plus-circle btn-icon-prepend" style="vertical-align: middle; font-size: 2.4em;"></i>
				<span aria-label="Left Align" style="display: inline-flex; vertical-align: middle; margin-left: 10px;">Create<br>Team Request</span>
			</button>
		</div>
	</div>
@stop

@section('content')

@php $numberOfHotelRooms = 0; @endphp

<div class="flash-message"></div>


<div class="page-content container-fluid tournament-team-request-manager-page">


	
	<div class="row">
		<div class="col-md-12">
			<div class="panel panel-bordered" style="margin-bottom: 50px;">
				
				{{--<div class="panel-header trmSectionHeaderCon">
					<h1 style="text-align: center;">Tournament Hotels</h1>
				</div>--}}

				<div class="panel-body">
					
					<div class="row no-gutters">
						<div class="col-12">
							
				
							<div id="trManagerCon">
								@foreach ($tournament->hotels as $tournamentHotel)
								<div class="panel panel-bordered" style="margin-bottom: 50px;">
									<div class="panel-header trmHotelHeaderCon">
				
										<h2 class='trmHotelHeader'>{{ $tournamentHotel->name }}</h2>
		
									</div>

									<div class="panel-body">
										<div class="row no-gutters padding tournamentHotelRow" id="tournamentHotelRow-{{ $tournamentHotel->id }}" data-hotel-id="{{ $tournamentHotel->id }}">
											
				
											<div class="col-md-12">
				
												@if($tournamentHotel->pivot->tournamentHotelRooms()->count() > 0)
				
													<div class="row">
														<div class="col-md-12">
				
															<!--<h2 style="text-align: center; margin-bottom: 25px;" class="tournamentHotelRoomManagerTitle">Hotel Room overview</h2>-->
															
																<!--<div class="panel panel-bordered" style="padding-top: 15px;">
																	<div class="panel-body">-->
																		<div class="row">
																			<div class="col-md-12">
																				<h3 class="tournamentHotelRoomTitle">Current Rooms Info</h3>
																			</div>
																		</div>
				
																		<div class="row">
																			<div class="col-md-12" style="border-bottom: 1.5px solid rgb(206 206 206);">
																				<div class="table-responsive">
																					<table id="dataTable" class="table table-hover thrTable thrInfoTable" style="width: 100%;">
																						<thead>
																							<tr>
																								<th>Type</th>
																								<th>Gross Price Per Night</th>
																								<th>Total Quantity</th>
																								<th>Rooms Available</th>
																								<th>Rooms on Hold</th>
																								<th>Rooms Allocated</th>
																							</tr>
																						</thead>
																						@foreach ($tournamentHotel->pivot->tournamentHotelRooms() as $tournamentHotelRoom)
																							<tr>
																								<td style="font-weight: bold">
																									{{ $tournamentHotelRoom->type }}
																								</td>
																								<td>
																									$ {{ $tournamentHotelRoom->gross_price_per_night }}
																								</td>
																								<td>
																									{{ $tournamentHotelRoom->quantity }}
																								</td>
																								<td id="thAvailableRooms-{{ $tournamentHotelRoom->id }}" data-available="{{ $tournamentHotelRoom->rooms_available }}">
																									{{ $tournamentHotelRoom->rooms_available }}
																								</td>
																								<td id="thHeld-{{ $tournamentHotelRoom->id }}" data-held="{{ $tournamentHotelRoom->held }}">
																									{{ $tournamentHotelRoom->held }}
																								</td>
																								<td>
																									{{ $tournamentHotelRoom->allocated }}
																								</td>
																							</tr>
																						@endforeach
																					</table>
																				</div>
				
																			</div>
																		</div>

																		<div class="row">
																			<div class="col-md-12">
																				<h3 class="tournamentHotelRoomTitle">Team Requests</h3>
																			</div>
																		</div>

																		@foreach($tournamentHotel->teamRequests as $teamRequest)
																			@if($teamRequest->approved === 0 && !$teamRequest->denied)
																				<!--<div class="row">
																					<div class="col-md-12 trTableOuter">-->
																				<div class="panel panel-bordered" style="padding-top: 20px;" id="trPanel-{{ $teamRequest->id}}">
																					<div class="panel-body">	

																						<div class="row no-gutters">
																							<div class="col-md-6 trmTeamInfoCon">
																								<div>
																									<label>Team:</label> <span class="trTeamName">{{ $teamRequest->team_name }}</span>
																								</div>
																								<div>
																									<label>Name:</label> <span class="trFirstName">{{ $teamRequest->contact_first_name }}</span> <span class="trLastName">{{ $teamRequest->contact_last_name }}</span>
																								</div>
																								<div>
																									<label>Phone Number:</label> <span class="trPhone">{{ $teamRequest->phone_number }}</span>
																								</div>
																								<div>
																									<label>Email:</label> <span class="trEmail">{{ $teamRequest->email }}</span>
																								</div>
																								<div>
																									<label>Special Instructions:</label> {{ $teamRequest->description }}
																								</div>
																							</div>
																							<div class="col-md-6 trTableOuter">

																								<div class="trRoomsRequestedCon">
																									<div class="table-responsive">
																										<table id="dataTable" class="table table-hover trTable trTableInner" style="width: 100%;">

																											<thead>
																												<th>Room Type</th>
																												<th>Number of Rooms Requested</th>
																											</thead>

																											@foreach($teamRequest->teamRequestRooms as $teamRequestRoom)

																												<tr>
																													<td>
																														{{ $teamRequestRoom->tournamentHotelRoom->hotelRoomType->type }}
																													</td>
																													<td id="trRoomRequest-{{ $teamRequestRoom->id }}">
																														{{ $teamRequestRoom->rooms_requested }}
																													</td>
																												</tr>
																											@endforeach
																										</table>
																									</div>
																								</div>

																							</div>
																							<div class="row no-gutters">
																								<div class="col-md-12 trBtnCon">

																									<button class="btn btn-primary btn-icon-text trEdit edit" aria-label="Left Align" data-tr-id="{{$teamRequest->id}}" data-slug="team-request/edit">
																										<i class="fas fa-edit btn-icon-prepend"></i>
																										<span class="hidden-xs hidden-sm">Edit</span>
																									</button>

																									{{--<button class="btn btn-sm btn-primary trEdit pull-right edit" data-slug="team-request/edit" data-tr-id="{{$teamRequest->id}}"><i class="voyager-edit" style="margin-right: 3px;"></i><span class="hidden-xs hidden-sm">Edit</span></a> --}}

																									<button class="btn btn-success btn-icon-text trApprove edit" aria-label="Left Align" data-tr-id="{{$teamRequest->id}}" data-slug="team-request/approve">
																										<i class="fas fa-check-circle btn-icon-prepend"></i>
																										<span class="hidden-xs hidden-sm">Approve</span>
																									</button>

																									<button class="btn btn-danger btn-icon-text trDeny delete" aria-label="Left Align" data-tr-id="{{$teamRequest->id}}" data-slug="team-request/deny">
																										<i class="fas fa-times-circle btn-icon-prepend"></i>
																										<span class="hidden-xs hidden-sm">Deny</span>
																									</button>

																								</div>
																							</div>
																						</div>
																					
						
																					</div>
																				</div>
																			@endif
																		@endforeach
																	
																	<!--</div>
																</div>-->


														</div>
													</div>
				
												@else

													<div class="noRoomInfoCon" style="text-align: center;">
														<h5>No rooms assigned to hotel yet</h5>
													</div>

												@endif
				
				
											</div>
				
										</div>
									</div>
								</div>
								@endforeach
							</div>
						</div>
					</div>
			

				</div>
			</div>

		</div>
	</div>


	<div class="row">
		<div class="col-md-12">
			<div class="panel panel-bordered" style="margin-bottom: 50px;">
				
				{{--<div class="panel-header trmSectionHeaderCon">
					<h1 style="text-align: center;">Tournament Hotels</h1>
				</div>--}}

				<div class="panel-header trmSectionHeaderCon">
				
					<h2 class='trmADHeader'>Approved and Denied Request</h2>

				</div>

				<div class="panel-body">
					
					<div class="row no-gutters">
						<div class="col-12">
							<div class="row">
								<div class="col-md-12">
									<h3 class="tournamentHotelRoomTitle">Approved Rooms</h3>
								</div>
							</div>

							<div class="row">
								<div class="col-md-12" style="border-bottom: 1.5px solid rgb(206 206 206);">
									<div class="table-responsive">
										<table id="approvedDataTable" class="table table-hover thrTable thrInfoTable" style="width: 100%;">
											<thead>
												<tr>
													<th>Team Name</th>
													<th>Contact Name</th>
													<th>Phone</th>
													<th>Email</th>
													<th>Team Link</th>
													<th>Actions</th>
												</tr>
											</thead>
										</table>
									</div>
								</div>
							</div>
						</div>

						<div class="col-12">

							<div style="display: none;" class="teamRequestEditForm">
								<form id="updateApprovedTeamRequestForm" style="max-width: 500px;" method="POST" action="#">
									{{ csrf_field() }}
									
									<!--<input type="hidden" name="tournament_hotel_room_id" value="">-->

									<input type="hidden" name="tournament_hotel_id" value="">

									<input type="hidden" name="teamRequestID" value="">

									<div class="form-group">
										<label class="control-label">Team Name</label>
										<input class="form-control" type="text" name="teamName" value="">
									</div>

									<div class="form-group">
										<label class="control-label">First Name</label>
										<input class="form-control" type="text" name="contactFirstName" value="">
									</div>

									<div class="form-group">
										<label class="control-label">Last Name</label>
										<input class="form-control" type="text" name="contactLastName" value="">
									</div>

									<div class="form-group">
										<label class="control-label">Phone</label>
										<input class="form-control" type="text" name="phoneNumber" value="">
									</div>

									<div class="form-group">
										<label class="control-label">Email</label>
										<input class="form-control" type="text" name="email" value="">
									</div>

									<div class="teamRequestEditRooms">
										@foreach ($tournament->hotels as $tournamentHotel)
											@if ($tournamentHotel->pivot->tournamentHotelRooms()->count() > 0)

												<div style="display: none;" class="teamRequestEditRoomsHotelCon" id="hotel-{{ $tournamentHotel->id }}">

													<label class="teamRequestEditRoomsHotelName">{{ $tournamentHotel->name }}</label>

													@foreach ($tournamentHotel->pivot->tournamentHotelRooms() as $tournamentHotelRoom)

														<div class="teamRequestEditRoomsHotelRoomType">{{ $tournamentHotelRoom->type }}</div>
														<div class="teamRequestEditRoomsHotelRoomTypeAvailablity">Available: <span id="hotelRoom-{{$tournamentHotelRoom->id}}-available">{{ $tournamentHotelRoom->rooms_available }}</span></div>
														<br>

														<div class="form-group requestedRoomsFormGroup hotelRoom">
															<label class="control-label">Rooms Requested</label>
															<input class="form-control trHotelRoomTypeInput" type="number" data-team-request-room-id="0" data-hotel-room-id="{{ $tournamentHotelRoom->id }}" name="rooms_requested_{{ $tournamentHotelRoom->id }}" value="0">
														</div>

													@endforeach

												</div>

											@endif
										@endforeach
									</div>

									<div class="form-group">
										<label class="control-label">Link Expiration Date</label>
										<input type="date" name="teamMemberLinkExpiration">
									</div>
				
									<div class="form-group">
										<div class="form-check">
											<input class="form-check-input" type="checkbox" id="allowIndividualCheckInOut">
											<label class="form-check-label" for="allowIndividualCheckInOut">
											Allow individual team member check in and check out dates
											</label>
										</div>
									</div>
				
									<div class="form-group">
										<div class="form-check">
											<input class="form-check-input" type="checkbox" id="requestsAreLate">
											<label class="form-check-label" for="requestsAreLate">
											Check if further request will be considered late
											</label>
										</div>
									</div>
				
									<div class="form-group">
				
										<button type="submit" id="approvedTeamRequestEditFormSubmit" class="btn btn-success btn-submit">Save</button> 
										<button id="approvedTeamRequestEditFormCancel" class="btn btn-danger cancelEditTeamRequest">Cancel</button>
									</div>
								</form>
							</div>
							<hr>
						</div>
					</div>

					<div class="row no-gutters">
						<div class="col-12">
							<div class="row">
								<div class="col-md-12">
									<h3 class="tournamentHotelRoomTitle">Denied Rooms</h3>
								</div>
							</div>

							<div class="row">
								<div class="col-md-12" style="border-bottom: 1.5px solid rgb(206 206 206);">
									<div class="table-responsive">
										<table id="deniedDataTable" class="table table-hover thrTable thrInfoTable" style="width: 100%;">
											<thead>
												<tr>
													<th>Team Name</th>
													<th>Contact Name</th>
													<th>Phone</th>
													<th>Email</th>
													<th>Actions</th>
												</tr>
											</thead>
										</table>
									</div>
								</div>
							</div>
						</div>
					</div>

				</div>
				
			</div>
		</div>
	</div>

</div>

<div class="modal modal-success fade" tabindex="-1" id="tr_create_modal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" id="tr_create_modal_dismiss_btn" data-dismiss="modal" aria-label="{{ __('voyager::generic.close') }}"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">create team request.</h4>
            </div>

			<div class="modal-body">

<form action="/tournament/application-form" id="tr_application_form" method="POST" style="margin-bottom: 0px;" class="needs-validation" novalidate>

	{{ csrf_field() }}
					
	<!--<input type="hidden" name="tournament_hotel_room_id" value="">-->

	<input type="hidden" name="tournament_id" value="{{ $tournament->id }}">

	<div class="form-group">
		<label class="control-label">Team Name</label>
		<input class="form-control" type="text" name="teamName" value="">
	</div>

	<div class="form-group">
		<label class="control-label">First Name</label>
		<input class="form-control" type="text" name="contactFirstName" value="">
	</div>

	<div class="form-group">
		<label class="control-label">Last Name</label>
		<input class="form-control" type="text" name="contactLastName" value="">
	</div>

	<div class="form-group">
		<label class="control-label">Phone</label>
		<input class="form-control" type="text" name="phoneNumber" value="">
	</div>

	<div class="form-group">
		<label class="control-label">Email</label>
		<input class="form-control" type="text" name="email" value="">
	</div>

	<div class="form-group">
		<label for="specialRequest">Special Request:</label>
		<textarea class="form-control" rows="5" id="specialRequest"></textarea>
	  </div>

	<div class="form-group hotelSelectFormGroup">
		<label for="hotelSelect">Select a hotel</label>
		<select class="form-control" id="hotelSelect">
			<option selected>select...</option>
			@foreach ($tournament->hotels as $tournamentHotel)
				<option value="{{ $tournamentHotel->id }}">{{ $tournamentHotel->name }}</option>
			@endforeach
		</select>
	</div>

	<div class="teamRequestRooms">
		@foreach ($tournament->hotels as $tournamentHotel)
			@if ($tournamentHotel->pivot->tournamentHotelRooms()->count() > 0)

				<div style="display: none;" class="teamRequestRoomsHotelCon" id="hotel-{{ $tournamentHotel->id }}">

					<label class="teamRequestRoomsHotelName">{{ $tournamentHotel->name }}</label>

					@foreach ($tournamentHotel->pivot->tournamentHotelRooms() as $tournamentHotelRoom)

						@php $roomNameIfOneOnly = "rooms-requested-" . $tournamentHotelRoom->id; $numberOfHotelRooms++; @endphp

						{{-- <div class="teamRequestRoomsHotelRoomType">{{ $tournamentHotelRoom->type }} <span class="teamRequestRoomsHotelPrice">${{ $tournamentHotelRoom->price_per_night + $tournamentHotelRoom->booking_fee_per_night }} per night</span></div>--}}
						<div class="teamRequestRoomsHotelRoomType">{{ $tournamentHotelRoom->type }} <span class="teamRequestRoomsHotelPrice">${{ $tournamentHotelRoom->gross_price_per_night }} per night</span></div>
						{{-- <div class="teamRequestRoomsHotelRoomTypeAvailablity">Available: <span id="hotelRoom-{{$tournamentHotelRoom->id}}-available">{{ $tournamentHotelRoom->rooms_available }}</span></div> --}}
						<br>

						<div class="form-group requestedRoomsFormGroup hotelRoom">
							<label class="control-label">Rooms Requesting</label>
							<input class="form-control trHotelRoomTypeInput" type="number" max="{{ $tournamentHotelRoom->rooms_available }}" data-hotel-room-id="{{ $tournamentHotelRoom->id }}" name="rooms-requested-{{ $tournamentHotelRoom->id }}" value="0">
						</div>

					@endforeach

				</div>

			@endif
		@endforeach

		<input type="hidden" class="roomCounter" name="numberOfRooms" value="0">

		<div class="form-group">
			<label class="control-label">Link Expiration Date</label>
			<input type="date" name="teamMemberLinkExpiration">
		</div>

		<div class="form-group">
			<div class="form-check">
				<input class="form-check-input" type="checkbox" id="allowIndividualCheckInOut">
				<label class="form-check-label" for="allowIndividualCheckInOut">
				Allow individual team member check in and check out dates
				</label>
			</div>
		</div>

		<div class="form-group">
			<div class="form-check">
				<input class="form-check-input" type="checkbox" id="requestsAreLate">
				<label class="form-check-label" for="requestsAreLate">
				Check if further request will be considered late
				</label>
			</div>
		</div>

	</div>

	<div class="form-group">

		<button type="submit" id="teamRequestFormSubmit" class="btn btn-success btn-submit">Submit</button> 
		<button type="button" id="teamRequestCreateFormCancel" class="btn btn-default pull-right" data-dismiss="modal">{{ __('voyager::generic.cancel') }}</button>

	</div>
	
</form>

</div>
</div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div class="modal modal-success fade" tabindex="-1" id="tr_edit_modal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" id="tr_edit_modal_dismiss_btn" data-dismiss="modal" aria-label="{{ __('voyager::generic.close') }}"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Change fields below to edit this team request.</h4>
            </div>

			<!--<div class="modal-body">

			</div>-->
            <div class="modal-body">
                <form action="#" id="tr_edit_form" method="POST" style="margin-bottom: 0px;">

					{{ csrf_field() }}
									
					<!--<input type="hidden" name="tournament_hotel_room_id" value="">-->

					<input type="hidden" name="tournament_hotel_id" value="">

					<input type="hidden" name="teamRequestID" value="">

					<div class="form-group">
						<label class="control-label">Team Name</label>
						<input class="form-control" type="text" name="teamName" value="">
					</div>

					<div class="form-group">
						<label class="control-label">First Name</label>
						<input class="form-control" type="text" name="contactFirstName" value="">
					</div>

					<div class="form-group">
						<label class="control-label">Last Name</label>
						<input class="form-control" type="text" name="contactLastName" value="">
					</div>

					<div class="form-group">
						<label class="control-label">Phone</label>
						<input class="form-control" type="text" name="phoneNumber" value="">
					</div>

					<div class="form-group">
						<label class="control-label">Email</label>
						<input class="form-control" type="text" name="email" value="">
					</div>

					<div class="teamRequestEditRooms">
						@foreach ($tournament->hotels as $tournamentHotel)
							@if ($tournamentHotel->pivot->tournamentHotelRooms()->count() > 0)

								<div style="display: none;" class="teamRequestEditRoomsHotelCon" id="hotel-{{ $tournamentHotel->id }}">

									<label class="teamRequestEditRoomsHotelName">{{ $tournamentHotel->name }}</label>

									@foreach ($tournamentHotel->pivot->tournamentHotelRooms() as $tournamentHotelRoom)

										<div class="teamRequestEditRoomsHotelRoomType">{{ $tournamentHotelRoom->type }}</div>
										<div class="teamRequestEditRoomsHotelRoomTypeAvailablity">Available: <span id="hotelRoom-{{$tournamentHotelRoom->id}}-available">{{ $tournamentHotelRoom->rooms_available }}</span></div>
										<br>

										<div class="form-group requestedRoomsFormGroup hotelRoom">
											<label class="control-label">Rooms Requested</label>
											<input class="form-control trHotelRoomTypeInput" type="number" data-team-request-room-id="0" data-hotel-room-id="{{ $tournamentHotelRoom->id }}" name="rooms_requested_{{ $tournamentHotelRoom->id }}" value="0">
										</div>

									@endforeach

								</div>

							@endif
						@endforeach
					</div>

					<div class="form-group">
						<label class="control-label">Link Expiration Date</label>
						<input type="date" name="teamMemberLinkExpiration" id="teamMemberLinkExpiration">
					</div>

					<div class="form-group">
						<div class="form-check">
							<input class="form-check-input" type="checkbox" id="allowIndividualCheckInOut">
							<label class="form-check-label" for="allowIndividualCheckInOut">
							Allow individual team member check in and check out dates
							</label>
						</div>
					</div>

					<div class="form-group">
						<div class="form-check">
							<input class="form-check-input" type="checkbox" id="requestsAreLate">
							<label class="form-check-label" for="requestsAreLate">
							Check if further request will be considered late
							</label>
						</div>
					</div>

					<div class="form-group">

						<button type="submit" id="teamRequestEditFormSubmit" class="btn btn-success btn-submit">Save</button> 
						<button type="button" id="teamRequestEditFormCancel" class="btn btn-default pull-right" data-dismiss="modal">{{ __('voyager::generic.cancel') }}</button>
					</div>
					
                    <!-- <input type="submit" class="btn btn-danger pull-right delete-confirm" id="de_form_submit_btn" value="Deny Request"> -->
                </form>
                
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div class="modal modal-danger fade" tabindex="-1" id="tr_deny_modal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('voyager::generic.close') }}"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Are you sure you want to deny this team request?</h4>
            </div>

			<!--<div class="modal-body">

			</div>-->
            <div class="modal-footer">
                <form action="/admin/team-request/deny" id="tr_deny_form" method="POST" style="margin-bottom: 0px;">
                    {{ csrf_field() }}

					<input type="hidden" id="tr_id" name="tr_id" value="">

					<div class="form-group" id="reasonDenied" style="text-align: left!important;">
						<label class="control-label">Reason for being denied: </label>
						<textarea class="form-control" id="reason_denied" name="reason_denied" type="text-area" rows="10" cols="30"></textarea>
					</div>
					
                    <input type="submit" class="btn btn-danger pull-right delete-confirm" id="de_form_submit_btn" value="Deny Request">
                </form>
                <button type="button" class="btn btn-default pull-right" data-dismiss="modal">{{ __('voyager::generic.cancel') }}</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

@php $trRoomGroup = ""; $trrgCount = 0; @endphp
        @foreach ($tournament->hotels as $tournamentHotel)
            @foreach( $tournamentHotel->pivot->tournamentHotelRooms() as $teamRequestRoom) 
                @php if ($trrgCount == 0) { $trRoomGroup .= "rooms-requested-" . $teamRequestRoom->id; } else { $trRoomGroup .= " rooms-requested-" . $teamRequestRoom->id; } $trrgCount++; @endphp
            @endforeach
        @endforeach

@stop

@section('css')

<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css" integrity="sha384-DyZ88mC6Up2uqS4h/KRgHuoeGwBcD4Ng9SiP4dIRy0EXTlnuz47vAwmeGwVChigm" crossorigin="anonymous">
<!--<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.1/css/bootstrap-select.css" />-->
@stop

@section('javascript')

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/jquery.validate.min.js" defer></script>
<script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.3/dist/additional-methods.min.js" defer></script>

<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.1/js/bootstrap-select.min.js"></script> -->

<script type="text/javascript">
	window.onload = function() {
		var tournament_id = <?php echo $tournament->id; ?>;

		var tournamentSlug = <?php echo '"' . $tournament->slug . '"'; ?>;

		
		$(document).ready(function(){

			


			$('#approvedDataTable').DataTable({
				processing: true,
				serverSide: true,
				responsive: true,
				ajax: { "url": "/admin/team-requests/get-approved-team-requests",
						"data": {"tournament_id": tournament_id} 
					},
				columns: [
					//{ data: 'id' },
					{ data: 'team_name',
					render: function (data, type, row, meta) {
						return '<div>'+data+'</div>';
					} },
					{ data: 'contact'},
					{ data: 'phone' },
					{ data: 'email' },
					{ data: 'uuid',
					render: function(data, type, row, meta) {
						return '<div style="max-width: 150px;">https://championshipcityrooms.com/tournament/' + tournamentSlug + '/team-members-select-rooms-form?tuuid=' + data + '</div>';
						
					}
					},
					{
					"data": {id: 'id'},
					"render": function (data, type, row, meta) {
						var rData = '<button class="btn btn-sm btn-danger removeTR pull-right delete" id="'+data.id+'"><i class="voyager-trash" style="margin-right: 3px;"></i><span class="hidden-xs hidden-sm">Delete</span></a>';
						rData += '<button class="btn btn-sm btn-primary approvedTR pull-right edit" id="'+data.id+'"><i class="voyager-edit" style="margin-right: 3px;"></i><span class="hidden-xs hidden-sm">Edit</span></a>';
						
						return rData;
					},
					"sClass": "bread-actions no-sort no-click"
					}
					//{ data: 'endDate'}
				],
				columnDefs: [
				{'targets': 'dt-not-orderable', 'searchable': false, 'orderable': false},
				]
			});

			$('#deniedDataTable').DataTable({
				processing: true,
				serverSide: true,
				responsive: true,
				ajax: { "url": "/admin/team-requests/get-denied-team-requests",
						"data": {"tournament_id": tournament_id} 
					},
				columns: [
					//{ data: 'id' },
					{ data: 'team_name',
					render: function (data, type, row, meta) {
						return '<div>'+data+'</div>';
					} },
					{ data: 'contact'},
					{ data: 'phone' },
					{ data: 'email' },
					{
					"data": {id: 'id'},
					"render": function (data, type, row, meta) {
						var rData = '<button class="btn btn-sm btn-primary approvedTR pull-right edit" id="'+data.id+'"><i class="voyager-edit" style="margin-right: 3px;"></i><span class="hidden-xs hidden-sm">Undo Deny</span></a>';
							
						return rData;
					},
					"sClass": "bread-actions no-sort no-click"
					}
					//{ data: 'endDate'}
				],
				columnDefs: [
				{'targets': 'dt-not-orderable', 'searchable': false, 'orderable': false},
				]
			});


			var deleteFormAction;

			$('.trApprove').click(function() {

				var trID = $(this).data('tr-id');

				$.ajax({
					headers: {
								'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
					},
					type: "post",
					data: {"trID": trID },
					dataType:'json',
					url: "/admin/team-request/approve",
					success:function(data){
						//console.log(data);

						//var removedHotelData = JSON.parse(data['removed_hotel']);

						var dataToChange = JSON.parse(data['updateData']);


						$.each(dataToChange, function (index,value) {
							console.log(value.tournamentHotelRoom_id);
							
							var currentAvailable = $("#thAvailableRooms-" + value.tournamentHotelRoom_id).data('available');
							var newAvailable = currentAvailable - value.amount;

							$("#thAvailableRooms-" + value.tournamentHotelRoom_id).html(newAvailable);
							$("#thAvailableRooms-" + value.tournamentHotelRoom_id).data('available', newAvailable);


							$('.teamRequestEditRoomsHotelRoomTypeAvailablity #hotelRoom-' + value.tournamentHotelRoom_id + '-available').html(newAvailable);

							var currentHeld = $("#thHeld-" + value.tournamentHotelRoom_id).data('held');
							var newHeld = currentHeld + value.amount;

							$("#thHeld-" + value.tournamentHotelRoom_id).html(newHeld);
							$("#thHeld-" + value.tournamentHotelRoom_id).data('held', newHeld);

							
						});

						$('#approvedDataTable').DataTable().ajax.reload();

						$('#trPanel-' + data['trID'] ).hide('fade');

						toastr.success(data['message']);
					}

				});
			});


			$('div').on('click', '.trDeny', function (e) {

				var trID = $(this).data('tr-id');
				
				$('#tr_deny_modal #tr_deny_form input[name="tr_id"]').val(trID);
				$('#tr_deny_modal').modal('show');
			});

			$('#de_form_submit_btn').click(function(e) {
				e.preventDefault();

				var trID = $('#tr_deny_modal #tr_deny_form input[name="tr_id"]').val();
				var trDeniedReason = $('#tr_deny_modal #tr_deny_form textarea#reason_denied').val();

				$.ajax({
					headers: {
								'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
					},
					type: "post",
					data: {"trID": trID, "reasonDenied": trDeniedReason },
					dataType:'json',
					url: "/admin/team-request/deny",
					success:function(data){
						//console.log(data);

						$('#tr_deny_modal').modal('hide');

						//var removedHotelData = JSON.parse(data['removed_hotel']);

						$('#deniedDataTable').DataTable().ajax.reload();

						$('#trPanel-' + data['trID'] ).hide('fade');

						toastr.success(data['message']);
					}

				});


			});

			$('div.trBtnCon').on('click', '.trEdit', function (e) {
				
				

				var trID = $(this).data('tr-id');

				//$('#tr_edit_modal #tr_edit_form input[name="tr_id"]').val(trID);

				//var trID = $(this).attr('id');

				$.ajax({
					headers: {
								'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
					},
					type: "get",
					data: {"trID": trID},
					dataType:'json',
					url: "/admin/team-requests/get-team-request",
					success:function(data){
						console.log(data);

						//team_request_id teamName contactFirstName contactLastName phoneNumber email

						$('#tr_edit_modal #tr_edit_form input[name="teamRequestID"]').val(trID);

						$('#tr_edit_modal #tr_edit_form input[name="teamName"]').val(data['team_name']);

						$('#tr_edit_modal #tr_edit_form input[name="contactFirstName"]').val(data['contact_first_name']);

						$('#tr_edit_modal #tr_edit_form input[name="contactLastName"]').val(data['contact_last_name']);

						$('#tr_edit_modal #tr_edit_form input[name="phoneNumber"]').val(data['phone_number']);

						$('#tr_edit_modal #tr_edit_form input[name="email"]').val(data['email']);

						$('#tr_edit_modal #tr_edit_form input[name="tournament_hotel_id"]').val(data['hotel_id']);

						if( data['allow_individual_check_out'] > 0) {
							$("#tr_edit_modal #tr_edit_form  #allowIndividualCheckInOut").prop( "checked", true );
						}

						if( data['request_are_late'] > 0) {
							$("#tr_edit_modal #tr_edit_form  #requestAreLate").prop( "checked", true );
						}

						$('#tr_edit_modal #tr_edit_form input[name="teamMemberLinkExpiration"]').val(data['team_member_link_expiration']);

						

						$('#tr_edit_modal #tr_edit_form .teamRequestEditRooms #hotel-' + data['hotel_id']).show();


						var roomData = JSON.parse(data['rooms_data']);

						$.each(roomData, function (index,value) {

							$('#tr_edit_modal #tr_edit_form input[name="rooms_requested_' +  value.tournament_hotel_room_id +'"]').val(value.rooms_requested);  

							$('#tr_edit_modal #tr_edit_form input[name="rooms_requested_' +  value.tournament_hotel_room_id +'"]').data('team-request-room-id', value.tr_room_id);  
							
						});


						$('#tr_edit_modal').modal('show');
					}

			});
		});

		$('#teamRequestEditFormSubmit').click(function(e) {
					e.preventDefault();

					var hotel_id = $('#tr_edit_modal #tr_edit_form input[name="tournament_hotel_id"]').val();

					var trID = $('#tr_edit_modal #tr_edit_form input[name="teamRequestID"]').val();

					var teamName = $('#tr_edit_modal #tr_edit_form input[name="teamName"]').val();

					var contactFirstName = $('#tr_edit_modal #tr_edit_form input[name="contactFirstName"]').val();

					var contactLastName = $('#tr_edit_modal #tr_edit_form input[name="contactLastName"]').val();

					var phone = $('#tr_edit_modal #tr_edit_form input[name="phoneNumber"]').val();

					var email = $('#tr_edit_modal #tr_edit_form input[name="email"]').val();

					var teamMemberLinkExpiration = $('#tr_edit_modal #tr_edit_form input[name="teamMemberLinkExpiration"]').val();

					var allowIndividualCheckInOut = 0;
					if( $('#tr_edit_modal #allowIndividualCheckInOut').is(":checked") ) {
						allowIndividualCheckInOut = 1;
					}

					var requestAreLate = 0;

					if( $('#tr_edit_modal #requestAreLate').is(":checked") ) {
						requestAreLate = 1;
					}

					var trHotelRoomsData = new Array();

					$('#tr_edit_modal #tr_edit_form .teamRequestEditRooms #hotel-' + hotel_id + ' input[type=number]').each(function(){

						trHotelRoomsData.push({ 'hotelRoomID': $(this).data('hotel-room-id'), 'roomsRequested': $(this).val(), 'teamRequestRoomID':  $(this).data('team-request-room-id')});

					});

					var teamRequestData = {
						'hotelID': hotel_id,
						'teamRequestID': trID,
						'teamName': teamName,
						'contactFirstName': contactFirstName,
						'contactLastName': contactLastName,
						'phone': phone,
						'email': email,
						'allowIndividualCheckInOut': allowIndividualCheckInOut,
						'trHotelRoomData': JSON.stringify(trHotelRoomsData),
						'teamMemberLinkExpiration': teamMemberLinkExpiration,
						'requestAreLate': requestAreLate
					};

					$.ajax({
						headers: {
									'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
						},
						type: "post",
						data: teamRequestData,
						dataType:'json',
						url: "/admin/team-requests/trm-update-request",
						success:function(data){
							//console.log(data);
							//var dataToChange = JSON.parse(data['rdata']);

							$("#trPanel-" + teamRequestData.teamRequestID + " .trTeamName").html(teamRequestData.teamName);

							$("#trPanel-" + teamRequestData.teamRequestID + " .trFirstName").html(teamRequestData.contactFirstName);

							$("#trPanel-" + teamRequestData.teamRequestID + " .trLastName").html(teamRequestData.contactLastName);

							$("#trPanel-" + teamRequestData.teamRequestID + " .trPhone").html(teamRequestData.phone);

							$("#trPanel-" + teamRequestData.teamRequestID + " .trEmail").html(teamRequestData.email);

							var dataToChange = JSON.parse(teamRequestData.trHotelRoomData);

							$.each(dataToChange, function (index,value) {

									$("#trPanel-" + teamRequestData.teamRequestID + " #trRoomRequest-" + value.teamRequestRoomID).html(value.roomsRequested);

							});

							//$('#approvedDataTable').DataTable().ajax.reload();

							$("#tr_edit_modal #tr_edit_form").trigger("reset");
					
							$('.teamRequestEditRoomsHotelCon').hide();
							$('#tr_edit_modal').modal('hide');
							toastr.success(data['message']);
						}
					});

					

					console.log(teamRequestData);

				});


		$('#tr_edit_modal').on('hidden.bs.modal', function (e) {
			console.log('hidden');

			$("#updateTeamRequestForm").trigger("reset");

			$('#updateTeamRequestForm .teamRequestEditRoomsHotelCon').hide();

		});

		$('#teamRequestEditFormCancel').click(function(e) {

			e.preventDefault();

			
			$('#tr_edit_modal').modal('hide');
		});


			$("#approvedDataTable").on('click', '.approvedTR.edit', function (e) { 
				
				$("#updateApprovedTeamRequestForm").trigger("reset");
				
				$(".teamRequestEditRoomsHotelCon").hide();
				
				var trID = $(this).attr('id');
				console.log('hey');

				$.ajax({
					headers: {
								'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
					},
					type: "get",
					data: {"trID": trID},
					dataType:'json',
					url: "/admin/team-requests/get-team-request",
					success:function(data){
						console.log(data);

						//team_request_id teamName contactFirstName contactLastName phoneNumber email

						$('#updateApprovedTeamRequestForm input[name="teamRequestID"]').val(trID);

						$('#updateApprovedTeamRequestForm input[name="teamName"]').val(data['team_name']);

						$('#updateApprovedTeamRequestForm input[name="contactFirstName"]').val(data['contact_first_name']);

						$('#updateApprovedTeamRequestForm input[name="contactLastName"]').val(data['contact_last_name']);

						$('#updateApprovedTeamRequestForm input[name="phoneNumber"]').val(data['phone_number']);

						$('#updateApprovedTeamRequestForm input[name="email"]').val(data['email']);

						$('#updateApprovedTeamRequestForm input[name="tournament_hotel_id"]').val(data['hotel_id']);

						$('#updateApprovedTeamRequestForm .teamRequestEditRooms #hotel-' + data['hotel_id']).show();

						if( data['allow_individual_check_out'] > 0) {
							$("#updateApprovedTeamRequestForm   #allowIndividualCheckInOut").prop( "checked", true );
						}

						if( data['request_are_late'] > 0) {
							$("#updateApprovedTeamRequestForm  #requestAreLate").prop( "checked", true );
						}

						$('#updateApprovedTeamRequestForm  input[name="teamMemberLinkExpiration"]').val(data['team_member_link_expiration']);


						var roomData = JSON.parse(data['rooms_data']);

						$.each(roomData, function (index,value) {

							$('#updateApprovedTeamRequestForm input[name="rooms_requested_' +  value.tournament_hotel_room_id +'"]').val(value.rooms_requested);  

							$('#updateApprovedTeamRequestForm input[name="rooms_requested_' +  value.tournament_hotel_room_id +'"]').data('team-request-room-id', value.tr_room_id);  
							
						});


						$('.teamRequestEditForm').show('fade');
					}
				}); 
			});


				$('#approvedTeamRequestEditFormCancel').click(function(e) {

					e.preventDefault();

					$("#updateApprovedTeamRequestForm").trigger("reset");
					
					$('#updateApprovedTeamRequestForm .teamRequestEditRoomsHotelCon').hide();
					$('.teamRequestEditForm').hide('fade');
				});

				$('#approvedTeamRequestEditFormSubmit').click(function(e) {
					e.preventDefault();

					var hotel_id = $('#updateApprovedTeamRequestForm input[name="tournament_hotel_id"]').val();

					var trID = $('#updateApprovedTeamRequestForm input[name="teamRequestID"]').val();

					var teamName = $('#updateApprovedTeamRequestForm input[name="teamName"]').val();

					var contactFirstName = $('#updateApprovedTeamRequestForm input[name="contactFirstName"]').val();

					var contactLastName = $('#updateApprovedTeamRequestForm input[name="contactLastName"]').val();

					var phone = $('#updateApprovedTeamRequestForm input[name="phoneNumber"]').val();

					var email = $('#updateApprovedTeamRequestForm input[name="email"]').val();

					var allowIndividualCheckInOut = 0;
					if( $('#updateApprovedTeamRequestForm #allowIndividualCheckInOut').is(":checked") ) {
						allowIndividualCheckInOut = 1;
					}
					
					console.log(allowIndividualCheckInOut);

					//var teamMemberLinkExpiration = $('#updateApprovedTeamRequestForm input[name="teamMemberLinkExpiration"]').val();
					
					teamMemberLinkExpiration = $('#updateApprovedTeamRequestForm input[name="teamMemberLinkExpiration"]').val();
					
					var requestAreLate = 0;

					if( $('#tr_edit_modal #requestAreLate').is(":checked") ) {
						requestAreLate = 1;
					}

					

					var trHotelRoomsData = new Array();

					$('#updateApprovedTeamRequestForm .teamRequestEditRooms #hotel-' + hotel_id + ' input[type=number]').each(function(){

						trHotelRoomsData.push({ 'hotelRoomID': $(this).data('hotel-room-id'), 'roomsRequested': $(this).val(), 'teamRequestRoomID':  $(this).data('team-request-room-id')});

					});

					var teamRequestData = {
						'hotelID': hotel_id,
						'teamRequestID': trID,
						'teamName': teamName,
						'contactFirstName': contactFirstName,
						'contactLastName': contactLastName,
						'phone': phone,
						'email': email,
						'allowIndividualCheckInOut': allowIndividualCheckInOut,
						'trHotelRoomData': JSON.stringify(trHotelRoomsData),
						'teamMemberLinkExpiration': teamMemberLinkExpiration + '',
						'requestAreLate': requestAreLate
					};

					$.ajax({
						headers: {
									'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
						},
						type: "post",
						data: teamRequestData,
						dataType:'json',
						url: "/admin/team-requests/trm-update-approved-request",
						success:function(data){
							console.log(data);
							var dataToChange = JSON.parse(data['rdata']);


							$.each(dataToChange, function (index,value) {
								if (value.amount !== 0) {
									console.log(value.tournamentHotelRoom_id);
									
									var currentAvailable = $("#thAvailableRooms-" + value.tournamentHotelRoom_id).data('available');
									var newAvailable = currentAvailable - value.amount;

									$("#thAvailableRooms-" + value.tournamentHotelRoom_id).html(newAvailable);
									$("#thAvailableRooms-" + value.tournamentHotelRoom_id).data('available', newAvailable);


									$('.teamRequestEditRoomsHotelRoomTypeAvailablity #hotelRoom-' + value.tournamentHotelRoom_id + '-available').html(newAvailable);

									var currentHeld = $("#thHeld-" + value.tournamentHotelRoom_id).data('held');
									var newHeld = currentHeld + value.amount;

									$("#thHeld-" + value.tournamentHotelRoom_id).html(newHeld);
									$("#thHeld-" + value.tournamentHotelRoom_id).data('held', newHeld);

								}
							});

							$('#approvedDataTable').DataTable().ajax.reload();

							$("#updateApprovedTeamRequestForm").trigger("reset");
					
							$('.teamRequestEditRoomsHotelCon').hide();
							$('.teamRequestEditForm').hide('fade');
							toastr.success(data['message']);
						}
					});

					

					console.log(teamRequestData);

				});
				
				//console.log(id);
				//});


				/* TEAM REQUEST STUFF STARTS */


				$("#trCreateBtn").on('click', function() {
				$('#tr_create_modal').modal('show');
			});

		$('#tr_create_modal').on('hidden.bs.modal', function (e) {
			console.log('hidden');

			$("#tr_application_form").trigger("reset");

			$('#tr_application_form .teamRequestRoomsHotelCon').hide();

		});

		$('#teamRequestCreateFormCancel').click(function(e) {

			e.preventDefault();

			
			$('#tr_create_modal').modal('hide');
		});

				jQuery.validator.setDefaults({
                ignore: []
            });

            jQuery.validator.addMethod("require_from_group2", function (value, element, options) {
    var validator = this;
    var minRequired = options[0];
    var selector = options[1];
    var minimum = options[2];
    var validOrNot = jQuery(selector, element.form).filter(function () {
        console.log(validator.elementValue(this));
        if(validator.elementValue(this) >= minimum) {

            return validator.elementValue(this);
        } else {
            return 0;
        }
    }).length >= minRequired;
//console.log(jQuery(selector, element.form).filter(function () { return validator.elementValue(this); }).length);
    // remove all events in namespace upload

    jQuery(selector, element.form).off('.require_from_group2');

    if (this.settings.onkeyup) {
        jQuery(selector, element.form).on({
            'keyup.require_from_group2': function (e) {
                jQuery(selector, element.form).valid();
            }
        });
    }
    if (this.settings.onfocusin) {
        jQuery(selector, element.form).on({
            'focusin.require_from_group2': function (e) {
                jQuery(selector, element.form).valid();
            }
        });
    }
    if (this.settings.click) {
        jQuery(selector, element.form).on({
            'click.require_from_group2': function (e) {
                jQuery(selector, element.form).valid();
            }
        });
    }
    if (this.settings.focusout) {
        jQuery(selector, element.form).on({
            'focusout.require_from_group2': function (e) {
                jQuery(selector, element.form).valid();
            }
        });
    }

    return validOrNot;
}, jQuery.validator.format("Please fill at least {0} of these fields."));


var selectedHotel;
                            
            $("select#hotelSelect").change(function(){
				
                $(".teamRequestRoomsHotelCon").hide('fade');

                selectedHotel = jQuery(this).children("option:selected").val();
                $(".teamRequestRoomsHotelCon .trHotelRoomTypeInput").each(function(i, obj) {
                    $(this).val(0);
                });
                //console.log(selectedHotel);
                //$(".teamRequestRoomsHotelCon .trHotelRoomTypeInput").removeClass("selectedHotelRooms");
                if (selectedHotel != "select...") {
					//console.log('hi');
                    $("#tr_create_modal #hotel-" + selectedHotel).show('fade');
                    //$("#hotel-" + selectedHotel + " .trHotelRoomTypeInput").addClass("selectedHotelRooms");
                }
                //alert("You have selected the country - " + selectedHotel);
            });


			var v = $("#tr_application_form").validate({
                errorClass: "error",
                rules: {
                    teamName: {
                        required: true
                    },
                    email:{
                        required: true,
                        email: true
                    },
                    contactFirstName:{
                        required: true
                    },
                    contactLastName:{
                        required: true,
                    },
                    phoneNumber: {
                        required: true
                    },
                     @if ($numberOfHotelRooms > 1)
                            @foreach ($tournament->hotels as $tournamentHotel)
                                @foreach($tournamentHotel->pivot->tournamentHotelRooms() as $teamRequestRoom) 
                                "rooms-requested-{{$teamRequestRoom->id}}": 
                                {
                                    require_from_group2: [1, ".trHotelRoomTypeInput", 1]
                                },
                                @endforeach
                            @endforeach
                        @else 
                        "{{$roomNameIfOneOnly}}": {required: true, min: 1},
                    @endif
                    },
                    @if ($numberOfHotelRooms > 1)
                        groups: {
                            name: "{{ $trRoomGroup }}"
                        },
                    @endif
                    {{--numberOfRooms:{
                        required: true,
                        min: 1
                    }
                }, --}}
                submitHandler: function(form) { 
                    alert('valid form.');
                    return false;
                },
                errorPlacement: function(error, element) {
                    //error.appendTo(element.parents (".form-group"));
                    //console.log('hey');
                    /*console.log(element);
                    error.appendTo(element.parents (".form-group"));*/
                    if ( element.is(".trHotelRoomTypeInput") ) 
                    {
                        //console.log(error);teamRequestRoomsHotelCon
                        error.appendTo( $(".teamRequestRooms") );
                    }
                    else 
                    { // This is the default behavior of the script
                        //error.insertAfter( element );
                        error.appendTo(element.parents (".form-group"));
                    }
                }
            });

			$("#teamRequestFormSubmit").click(function(e) {
                e.preventDefault();

                var teamName = $('#tr_application_form input[name="teamName"]').val();
                var contactFirstName = $('#tr_application_form input[name="contactFirstName"]').val();
                var contactLastName = $('#tr_application_form input[name="contactLastName"]').val();
                var phoneNumber = $('#tr_application_form input[name="phoneNumber"]').val();
                var email = $('#tr_application_form input[name="email"]').val();
                var specialRequest = $('#tr_application_form textarea#specialRequest').val();

				var allowIndividualCheckInOut = 0;
				if( $('#tr_application_form #allowIndividualCheckInOut').is(":checked") ) {
					allowIndividualCheckInOut = 1;
				}

				var teamMemberLinkExpiration = $('#tr_application_form input[name="teamMemberLinkExpiration"]').val();


				var requestAreLate = 0;

				if( $('#tr_application_form #requestAreLate').is(":checked") ) {
					requestAreLate = 1;
				}

                var trHotelRoomsData = new Array();

                //$('#tr_application_form .teamRequestRooms #hotel-' + selectedHotel + ' input[type=number]').hide();

                $('#tr_application_form .teamRequestRooms #hotel-' + selectedHotel + ' input[type=number]').each(function(){
                    console.log($(this).val());
                    trHotelRoomsData.push({ 'hotelRoomID': $(this).data('hotel-room-id'), 'roomsRequested': $(this).val()});

                });

                var teamRequestData = {
                    'hotelID': selectedHotel,
                    'tournamentID': tournament_id,
                    'teamName': teamName,
                    'contactFirstName': contactFirstName,
                    'contactLastName': contactLastName,
                    'phone': phoneNumber,
                    'email': email,
                    'specialRequest': specialRequest,
                    'trHotelRoomData': JSON.stringify(trHotelRoomsData),
					'allowIndividualCheckInOut': allowIndividualCheckInOut,
					'teamMemberLinkExpiration': teamMemberLinkExpiration,
					'requestAreLate': requestAreLate
                };

                console.log(teamRequestData);

                $.ajax({
                    headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: "post",
                    data: teamRequestData,
                    dataType:'json',
                    url: "/team-requests/tr-submit-request",
                    success:function(data){
                        console.log(data);

						$("#tr_create_modal #tr_application_form").trigger("reset");
					
						$('.teamRequestRoomsHotelCon').hide();
						$('#tr_create_modal').modal('hide');

						approveManually(data['trID']);

						toastr.success("Team Request has been added to the approved request");

                        //$("#tr_application_form").hide('fade');

                        //$('#tr_message').html(data['message']);

                        //$('#tr_message').show('fade');
                        /*var dataToChange = JSON.parse(data['rdata']);


                        $.each(dataToChange, function (index,value) {
                            if (value.amount !== 0) {
                                console.log(value.tournamentHotelRoom_id);
                                
                                var currentAvailable = $("#thAvailableRooms-" + value.tournamentHotelRoom_id).data('available');
                                var newAvailable = currentAvailable - value.amount;

                                $("#thAvailableRooms-" + value.tournamentHotelRoom_id).html(newAvailable);
                                $("#thAvailableRooms-" + value.tournamentHotelRoom_id).data('available', newAvailable);


                                $('.teamRequestEditRoomsHotelRoomTypeAvailablity #hotelRoom-' + value.tournamentHotelRoom_id + '-available').html(newAvailable);

                                var currentHeld = $("#thHeld-" + value.tournamentHotelRoom_id).data('held');
                                var newHeld = currentHeld + value.amount;

                                $("#thHeld-" + value.tournamentHotelRoom_id).html(newHeld);
                                $("#thHeld-" + value.tournamentHotelRoom_id).data('held', newHeld);

                            }
                        });*/
                    }
                });
                                
            });

function approveManually(trID) {

//var trID = $(this).data('tr-id');

$.ajax({
	headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
	},
	type: "post",
	data: {"trID": trID },
	dataType:'json',
	url: "/admin/team-request/approve",
	success:function(data){
		//console.log(data);

		//var removedHotelData = JSON.parse(data['removed_hotel']);

		var dataToChange = JSON.parse(data['updateData']);


		$.each(dataToChange, function (index,value) {
			console.log(value.tournamentHotelRoom_id);
			
			var currentAvailable = $("#thAvailableRooms-" + value.tournamentHotelRoom_id).data('available');
			var newAvailable = currentAvailable - value.amount;

			$("#thAvailableRooms-" + value.tournamentHotelRoom_id).html(newAvailable);
			$("#thAvailableRooms-" + value.tournamentHotelRoom_id).data('available', newAvailable);


			$('.teamRequestEditRoomsHotelRoomTypeAvailablity #hotelRoom-' + value.tournamentHotelRoom_id + '-available').html(newAvailable);

			var currentHeld = $("#thHeld-" + value.tournamentHotelRoom_id).data('held');
			var newHeld = currentHeld + value.amount;

			$("#thHeld-" + value.tournamentHotelRoom_id).html(newHeld);
			$("#thHeld-" + value.tournamentHotelRoom_id).data('held', newHeld);

			
		});

		$('#approvedDataTable').DataTable().ajax.reload();

		//$('#trPanel-' + data['trID'] ).hide('fade');

		//toastr.success(data['message']);
	}

});
};

			
		})
	}
</script>
@stop
