@extends('voyager::master')

@section('page_header')
	{{--<meta name="csrf-token" content="{{ csrf_token() }}" />--}}
    <div class="container-fluid">

		<h1 class="page-title my-page-title">{{ $tournament->name }}</h1>

		@if ($tournament->tournament_type_id != 2)
            <div class="team-request-manager-btn-con">
                <a class="btn btn-info btn-icon-text" href="{{ url('/admin/tournament-manager/tournament/' . $tournament->slug . '/team-request-manager') }}">
                    <i class="fas fa-book-open btn-icon-prepend" style="vertical-align: middle; font-size: 2.4em;"></i>
                    <span aria-label="Left Align" style="display: inline-flex; vertical-align: middle; margin-left: 10px;">Team Request<br>Manager</span>
                </a>
            </div>
        @endif

	</div>
@stop

@section('content')

<!-- Success message -->
{{--@if(Session::has('success'))
	<div class="alert alert-success">
		{{Session::get('success')}}
	</div>
@endif

@if(count($errors) > 0 )
	<div class="alert alert-danger">
		Upload Validation Error
		<ul>
			@foreach($errors->all() as $error)
				<li>{{ $error }}</li>
			@endforeach
		</ul>
	</div>
@endif--}}

<div class="flash-message"></div>


<div class="page-content container-fluid tournament-manager-page">


	
	<div class="row">
		<div class="col-md-12">
			<div class="panel panel-bordered" style="margin-bottom: 50px;">
				<div class="panel-body">

					<h4 style="margin-bottom: 25px;">Tournament Settings</h4>

                    @if ($tournament->tournament_type_id != 2)
                        <div class="col-md-6">
                            <form id="setDefaultTeamLinkExpireForm" method="POST" action="{{ url('/admin/hotel/add-to-tournament') }}">
                                {{ csrf_field() }}

                                <div class="form-group col-md-8">
                                    <label class="control-label">Set Default Date Team Links Expire (for this tournament)</label>
                                    <input type="date" name="defaultLinkExpires" class="form-control" @if(isset($tournament->default_team_link_expiration)) value="{{$tournament->default_team_link_expiration}}" @endif>
                                </div>

                                
                                <div class="form-group col-md-12">

                                    <button type="submit" id="settingsLinkExpirationSubmit" class="btn btn-lg btn-success btn-submit">Set</button>
                    
                                </div>
                            </form>
                        </div>
                    @endif

					<div class="col-md-6">
			
						<form id="addHotelsForm" method="POST" action="{{ url('/admin/hotel/add-to-tournament') }}">
								{{ csrf_field() }}
								
								<input type="hidden" id="add_hotel_tournament_id" name="tournament_id" value="{{ $tournament->id }}" >

								<div class="form-group" id="hotelSelectDropdown">
									<label class="control-label">Select Hotels to add to this tournament</label>
									<select id="hotelSelect" name="hotel_ids[]" multiple class="form-control">
										@foreach($hotelsNotIn as $hotelNotIn)

											<option value="{{ $hotelNotIn->id }}">{{ $hotelNotIn->name }}</option>
							
										@endforeach
									</select>
							
								</div>

								<div class="form-group">

									<button type="submit" id="addHotelSubmit" class="btn btn-success btn-submit">Add Hotels</button>
					
								</div>
						</form>
					</div>

			{{--@foreach($hotelsNotIn as $hotelNotIn)

				<div>{{ $hotelNotIn->name }}</div>

			@endforeach--}}

			{{--@foreach ($hotels as $hotel)
				@foreach ($tournamentHotelRooms as $tournamentHotelRoom)
					@if ($hotel->id != $tournamentHotelRoom)

					@endif
				@endforeach
			@endforeach--}}

				</div>
			</div>

		</div>
	</div>

	<div class="row no-gutters">
		<div class="col-12">
			<h1 style="text-align: center;">Tournament Hotels</h1>

			<div id="tournamentHotelsManagerCon">
				@foreach ($tournament->hotels as $tournamentHotel)
				<div class="panel panel-bordered" style="margin-bottom: 50px;">
					<div class="panel-body">
						<div class="row no-gutters padding tournamentHotelRow" id="tournamentHotelRow-{{ $tournamentHotel->id }}" data-hotel-id="{{ $tournamentHotel->id }}">
							<div class="col-md-12 align-items-center">
								<div style="display: inline-flex; width: max-content;">
									<h3 class='tmHotelHeader'>{{ $tournamentHotel->name }}</h3>
	
									<button class="btn btn-danger btn-icon-text tmHotelDeleteBtn delete" aria-label="Left Align" data-hotel-id="{{ $tournamentHotel->id }}" data-slug="hotel/remove-from-tournament">
										<i class="fas fa-trash btn-icon-prepend"></i>
										<span class="hidden-xs">Remove</span>
									</button>
								</div>
								
								<div style="width: max-content; float: right;">
									<h3>Total Paid: @if($tournamentHotel->pivot->getTotalPaid() != 0) ${{ number_format($tournamentHotel->pivot->getTotalPaid(), 2, '.', ',') }} @endif</h3>
								</div>

							</div>
							
							

							<div class="col-md-12">
								<div class="row">
									<div class="col-md-8">
										
										{{-- @foreach ($tournamentHotel->pivot->tournamentHotelRooms() as $tournamentHotelRoom)
											{{ $tournamentHotelRoom->hotel_room_type_id }}
										@endforeach --}}

										

										<form id="addHotelRoomsForm" method="POST" action="{{ url('/admin/room/assign-to-tournament') }}">
											{{ csrf_field() }}


												<input type="hidden" id="add_room_tournament_id" name="tournament_id" value="{{ $tournament->id }}" >

												<input type="hidden" id="add_room_hotel_id" name="hotel_id" value="{{ $tournamentHotel->id }}" >
							
												<div class="form-group" id="roomSelectDropdown">
													<label class="control-label">Select Rooms to add to this Hotel</label>
													<select id="roomSelect" name="room_type_ids[]" multiple class="form-control roomSelect">
														@foreach ($tournamentHotel->pivot->roomsNotUsedTournamentHotel() as $roomsNotInUse)
															<option value="{{ $roomsNotInUse->id }}">{{ $roomsNotInUse->type }}</option>
														@endforeach
													</select>
											
												</div>
							
												<div class="form-group">
							
													<button id="addHotelSubmit" class="btn btn-success btn-submit">Add Rooms</button>
									
												</div>
										</form>

									</div>
								</div>
								
								
								<div class="row">
										<div class="col-md-12">
											<form id="saveTournamentHotelData" method="POST" action="{{ url('/admin/hotel-tournament/save-data') }}">
												{{ csrf_field() }}
	
	
													<input type="hidden" id="add_room_tournament_id" name="tournament_id" value="{{ $tournament->id }}" >
	
													<input type="hidden" id="add_room_hotel_id" name="hotel_id" value="{{ $tournamentHotel->id }}" >
								
								
													<div class="form-group">
														<label class="control-label">Min Nights Stay</label>
														<input class="form-control" type="number" name="minNightsStay" value="{{ $tournamentHotel->pivot->min_nights_stay }}">
													</div>
													
													<div class="form-group">
														<label class="control-label">Amenities</label>
														<textarea class="form-control richTextBox" rows="5" name="amenities" value="{{ $tournamentHotel->pivot->amenities }}">{{ $tournamentHotel->pivot->amenities }}</textarea>
													</div>
													
													<div class="form-group">
														<label class="control-label">Cancellation Policy</label>
														<textarea class="form-control richTextBox" rows="5" name="cancellationPolicy" value="{{ $tournamentHotel->pivot->cancellation_policy }}">{{ $tournamentHotel->pivot->cancellation_policy }}</textarea>
													</div>

                                                    @if ($tournament->tournament_type_id == 2)
                                                        <div class="form-group">
                                                            <div class="form-check">
                                                                <input class="form-check-input allowCustomNights" type="checkbox" {{ $tournamentHotel->pivot->custom_nights_stay == 1 ? 'checked':'' }} data-tournament-hotel-id="{{$tournamentHotel->id}}">
                                                                <label class="form-check-label" for="allowCustomNights">
                                                                Allow custom nights stay for Singles
                                                                </label>
                                                            </div>
                                                        </div>
                                                    @endif

                                                    <input type="hidden" id="customNights-{{$tournamentHotel->id}}" name="customNights" value="{{ $tournamentHotel->pivot->custom_nights_stay }}">
								
													<div class="form-group">
								
														<button id="addHotelTournamentInfo" class="btn btn-success btn-submit">Save</button>
										
													</div>
											</form>
										</div>
								</div>
								

								@if($tournamentHotel->pivot->tournamentHotelRooms()->count() > 0)

									<div class="row">
										<div class="col-md-12">

											<h2 style="text-align: center; margin-bottom: 25px;" class="tournamentHotelRoomManagerTitle">Hotel Room Manager</h2>
											@foreach ($tournamentHotel->pivot->tournamentHotelRooms() as $tournamentHotelRoom)
												<div class="panel panel-bordered">
													<div class="panel-body">
														<div class="row">
															<div class="col-md-12">
																<h3 class="tournamentHotelRoomTitle">{{ $tournamentHotelRoom->type }}</h3>
															</div>
														</div>
														
														
														<div class="row">
															<div class="col col-8 col-sm-12">
																<div class="checkbox">
																  <label>
																    <input class="hotelTournamentRoomToggle" data-roomID="{{ $tournamentHotelRoom->id }}" type="checkbox" {{ $tournamentHotelRoom->disabled == 1 ? 'checked=checked' :'' }}  data-toggle="toggle">
																    Enabled
																  </label>
																</div>
																
															</div>
														</div>

														<div class="row">
															<div class="col-md-12">
																<div class="table-responsive">
																	<table id="dataTable" class="table table-hover thrTable" style="width: 100%;">
																		<thead>
																			<tr>
																			<th>Gross Price Per Night</th>
																			<th>Net Price Per Night</th>
																			<th>Total Quantity</th>
																			<th>Rooms Available</th>
																			<th>Rooms on Hold</th>
																			<th>Rooms Allocated</th>
																			<th class="actions text-right dt-not-orderable">Actions</th>
																			</tr>
																		</thead>
																			<tr>
																				<td>
																					$ {{ $tournamentHotelRoom->gross_price_per_night }}
																				</td>
																				<td>
																					$ {{ $tournamentHotelRoom->net_price_per_night }}
																				</td>
																				<td>
																					{{ $tournamentHotelRoom->quantity }}
																				</td>
																				<td>
																					{{ $tournamentHotelRoom->rooms_available }}
																				</td>
																				<td>
																					{{ $tournamentHotelRoom->held }}
																				</td>
																				<td>
																					{{ $tournamentHotelRoom->allocated }}
																				</td>
																				<td>
																					<button class="btn btn-sm btn-danger pull-right delete roomDelete" data-room-id="{{$tournamentHotelRoom->id}}" data-slug="room/delete-tournament-hotel-room"><i class="voyager-trash" style="margin-right: 3px;"></i><span class="hidden-xs hidden-sm">Delete</span></button>

																					<button class="btn btn-sm btn-primary pull-right edit roomEdit" data-room-id="{{$tournamentHotelRoom->id}}"><i class="voyager-edit" style="margin-right: 3px;"></i><span class="hidden-xs hidden-sm">Edit</span></button>
																					
																				</td>
																			</tr>
																	</table>
																</div>

																

																<div style="display: none;" class="tournamentHotelRoomForm room-{{$tournamentHotelRoom->id}}">
																	<form id="updateHotelRoomsForm-{{$tournamentHotelRoom->id}}" style="max-width: 500px;" method="POST" action="{{ url('/admin/room/update-tournament-hotel-room') }}">
																		{{ csrf_field() }}
																		
																		<input type="hidden" name="tournament_hotel_room_id" value="{{ $tournamentHotelRoom->id }}">

																		<div class="form-group" id="roomPricePerNight">
																			<label class="control-label">Gross Price Per Night</label>
																			<input class="form-control" type="number" step="any" name="pricePerNight" value="{{ $tournamentHotelRoom->gross_price_per_night }}">
																		</div>

																		<div class="form-group" id="roomQuantity">
																			<label class="control-label">Quantity</label>
																			<input class="form-control" type="number" name="quantity" value="{{ $tournamentHotelRoom->quantity }}">
																		</div>
													
																		<div class="form-group">
													
																			<button type="submit" id="editHotelRoomSubmit-{{$tournamentHotelRoom->id}}" class="btn btn-success btn-submit">Save</button> 
																			<button id="cancelEditHotelRoom-{{$tournamentHotelRoom->id}}" data-room-id="{{$tournamentHotelRoom->id}}" class="btn btn-danger cancelEditHotelRoom">Cancel</button>
																		</div>
																	</form>
																</div>

															</div>
														</div>
													
													</div>
												</div>
											@endforeach
										</div>
									</div>

								@endif


							</div>

							{{-- <div class="col-md-12">
								<div class="row">
									<div class="col-md-4">
										
									</div>
								</div>
							</div>--}}
						</div>
					</div>
				</div>
				@endforeach
			</div>
			@php 
			//dd();
			@endphp
		</div>
	</div>
</div>
		  <!--<div class="panel panel-bordered">
			  <div class="panel-body">

			  </div>
		  </div>-->

<!--<div class="container contestManagerWrap">
    <div class="section-body">

        <div class="page-content contestCon">

        </div>

    </div>

</div>-->

<div class="modal modal-danger fade" tabindex="-1" id="hotel_delete_modal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('voyager::generic.close') }}"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><i class="voyager-trash"></i> {{ __('voyager::generic.delete_question') }} This?</h4>
            </div>
            <div class="modal-footer">
                <form action="#" id="hotel_delete_form" method="POST" style="margin-bottom: 0px;">
                    {{ csrf_field() }}

					<input type="hidden" id="delete_hotel_tournament_id" name="tournament_id" value="{{ $tournament->id }}" >

					<input type="hidden" id="delete_hotel_hotel_id" name="hotel_id" value="">
					
                    <input type="submit" class="btn btn-danger pull-right delete-confirm" value="{{ __('voyager::generic.delete_confirm') }}">
                </form>
                <button type="button" class="btn btn-default pull-right" data-dismiss="modal">{{ __('voyager::generic.cancel') }}</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div class="modal modal-danger fade" tabindex="-1" id="hotel_room_delete_modal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('voyager::generic.close') }}"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><i class="voyager-trash"></i> {{ __('voyager::generic.delete_question') }} This?</h4>
            </div>
            <div class="modal-footer">
                <form action="#" id="hotel_room_delete_form" method="POST" style="margin-bottom: 0px;">
                    {{ csrf_field() }}

					<input type="hidden" id="tournament_hotel_room_id" name="tournament_hotel_room_id" value="" >
					
                    <input type="submit" class="btn btn-danger pull-right delete-confirm" value="{{ __('voyager::generic.delete_confirm') }}">
                </form>
                <button type="button" class="btn btn-default pull-right" data-dismiss="modal">{{ __('voyager::generic.cancel') }}</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

@stop

@section('css')

<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css" integrity="sha384-DyZ88mC6Up2uqS4h/KRgHuoeGwBcD4Ng9SiP4dIRy0EXTlnuz47vAwmeGwVChigm" crossorigin="anonymous">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.1/css/bootstrap-select.css" />
@stop

@section('javascript')

<script>
        $(document).ready(function() {

            $("input.allowCustomNights").on('change', function(e) {

                var tournamentHotelID = $(this).data('tournament-hotel-id');
                console.log($(this).data('tournament-hotel-id'));
                if (event.currentTarget.checked) {
                    //console.log('in')
                    $("#customNights-" + tournamentHotelID).val(1);
                } else {
                    $("#customNights-" + tournamentHotelID).val(0);
                }
            });

            var additionalConfig = {
                selector: 'textarea.richTextBox',
            }

            $.extend(additionalConfig, {!! json_encode($options->tinymceOptions ?? '{}') !!})

            tinymce.init(window.voyagerTinyMCE.getConfig(additionalConfig));
        });
 </script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.1/js/bootstrap-select.min.js"></script>

<script type="text/javascript">
	window.onload = function() {
		var tournament_id = <?php echo $tournament->id; ?>;

		$('.roomSelect').selectpicker();

		$('#hotelSelect').selectpicker();

		
		$(document).ready(function(){

			$("#settingsLinkExpirationSubmit").on('click', function(e) {
				e.preventDefault();

				var dateExpire = $("#setDefaultTeamLinkExpireForm input[name='defaultLinkExpires']").val();

				$.ajax({
					headers: {
								'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
					},
					type: "post",
					data: {"tID": tournament_id, "lateDateRequestStart": dateExpire },
					dataType:'json',
					url: "/admin/tournament-manager/set-default-team-request-expire-link",
					success:function(data){

						toastr.success(data['message']);
					}

				});
				

			});
			
			$("input").on('change', '.hotelTournamentRoomToggle', function(e) {
				//e.preventDefault();
				var hotelTournamentRoomStatus = 0;
				var hotelTournamentRoomID = $(this).data('roomID');
				
				if ($(this).prop('checked')) {
					hotelTournamentRoomStatus = 1;
				}
				
				if ($(this).is(':checked')) {
					hotelTournamentRoomStatus = 1;
				}

				$.ajax({
					headers: {
								'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
					},
					type: "post",
					data: {"tournamentHotelRoomId": hotelTournamentRoomID, "status": hotelTournamentRoomStatus },
					url: "/admin/tournament-manager/set-hotel-room-status",
					success:function(data){

						toastr.success(data['message']);
					}

				});
				

			});
			
			
			

			var deleteFormAction;
			$('div').on('click', '.tmHotelDeleteBtn', function (e) {
				console.log($(this).data('hotel-id'));
				$('#hotel_delete_form')[0].action = "/admin/" + $(this).data('slug');
				$('#hotel_delete_modal #hotel_delete_form input[name="hotel_id"]').val($(this).data('hotel-id'));
				$('#hotel_delete_modal').modal('show');
			});

			$('div').on('click', '.roomDelete', function (e) {
				$('#hotel_room_delete_form')[0].action = "/admin/" + $(this).data('slug');
				$('#hotel_room_delete_modal #hotel_room_delete_form input[name="tournament_hotel_room_id"]').val($(this).data('room-id'));
				$('#hotel_room_delete_modal').modal('show');
			});

			//$(".tmHotelDeleteBtn").click(function(event) {

			$(".roomEdit").click(function() {
				console.log($(this).attr('id'));

				$('.tournamentHotelRoomForm.room-' + $(this).data('room-id')).show( "slow" );
			});

			$(".cancelEditHotelRoom").click(function(e) {
				e.preventDefault();
				$('.tournamentHotelRoomForm.room-' + $(this).data('room-id')).hide( "slow" );
			});


			/*$("#tournamentHotelsManagerCon").on("click", ".tmHotelDeleteBtn", function(event) {
				event.preventDefault();
				var hotelID = $(this).data("hotel-id");

				console.log(hotelID);

				$.ajax({
					headers: {
								'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
					},
					type: "post",
					data: {"hotel_id": hotelID, "tournament_id": tournament_id },
					dataType:'json',
					url: "{{ url('admin/hotel/remove-from-tournament') }}",
					success:function(data){
						console.log(data);

						var removedHotelData = JSON.parse(data['removed_hotel']);

						$('#tournamentHotelsManagerCon #tournamentHotelRow-' + removedHotelData.id ).remove();


						$('#hotelSelect').append("<option value='" + removedHotelData.id +"'>" + removedHotelData.name + "</option>");

						$('#hotelSelect').selectpicker('refresh');

						toastr.success(data['message']);
					}

				});

					
				
			});


			$("#addHotelSubmit").click(function(e) {
			//$("#addHotelsForm").click(function(e) {

				

				e.preventDefault()
				var hotel_ids = $("#hotelSelect").val();

				
				console.log('hey');

				//$('#tournamentHotelsManagerCon').append('<button class="btn btn-danger btn-icon-text tmHotelDeleteBtn" aria-label="Left Align"><i class="fas fa-trash btn-icon-prepend"></i>&nbsp;<span class="hidden-xs">Remove</span></button>');
			
				$.ajax({
					headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
					//'X-CSRF-TOKEN': token
					},
					type: "post",
					data: {"hotel_ids": hotel_ids, "tournament_id": tournament_id },
					dataType:'json',
					url: "{{ url('admin/hotel/add-to-tournament') }}",
					success:function(data){
						console.log(JSON.parse(data['added_hotels']));

						var addedHotelData = JSON.parse(data['added_hotels']);

						//$.each(data['added_hotels'], function(i, item) {
						$.each(addedHotelData, function (index,value) {

							console.log(value.id);

							$('#hotelSelect').find('[value=' + value.id +']').remove();

							$('#hotelSelect').selectpicker('refresh');

							var hotelConData = "";

							hotelConData += "<div class='row no-gutters padding' id='tournamentHotelRow-" + value.id + "' data-hotel-id='" + value.id + "'><div class='col-md-8'>";

							hotelConData += "<h3 class='tmHotelHeader'>" + value.name + "</h3>";

							hotelConData += '<button class="btn btn-danger btn-icon-text tmHotelDeleteBtn" aria-label="Left Align" data-hotel-id="' + value.id + '">';

							hotelConData += "<i class='fas fa-trash btn-icon-prepend'></i>&nbsp;";

							hotelConData += "<span class='hidden-xs'>Remove</span>";
								
							hotelConData += "</button>";

							hotelConData += "</div></div>";

							$('#tournamentHotelsManagerCon').append(hotelConData);
							

							//console.log(JSON.parse(data[i]));
							//data[i].id);
						});

						toastr.success(data['message']);
					
					//$('#hotelSelectDropdown .selected').remove();

					//$('#hotelSelect').selectpicker('refresh');
					}
				});
			})*/

			
		})
	}
</script>
@stop
