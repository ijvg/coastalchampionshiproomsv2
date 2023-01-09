@extends('voyager::master')

@section('page_header')
	{{--<meta name="csrf-token" content="{{ csrf_token() }}" />--}}
    <div class="container-fluid">
		<h1 class="page-title my-page-title">Transactions Manager</h1>
	</div>
@stop

@section('content')

<div class="flash-message"></div>


<div class="page-content container-fluid transactions-manager-page">
	
	<div class="row">
		<div class="col-md-12">
			<div class="panel panel-bordered" style="margin-bottom: 50px;">
				
				{{--<div class="panel-header trmSectionHeaderCon">
					<h1 style="text-align: center;">Tournament Hotels</h1>
				</div>--}}

				<div class="panel-body">
					
					<div class="row no-gutters">
						<div class="col-12">
							
				
							<div id="transManagerCon">

								{{-- @php dd($tournaments); @endphp --}}

								<div class="row">
									<div class="col-md-12">
										<div class="panel panel-bordered">
											<div class="panel-header">
												<h2 style="text-align: center;"> All Customer Order Information </h2>
											</div>
											<div class="panel-body">
											  <div class="table-responsive">
												<table id="customersDataTable" class="table table-hover" style="width: 100%;">
													<thead>
														<tr>
														  <th>First Name</th>
														  <th>Last Name</th>
														  <!--<th>Team</th>-->
														  <th>Tournament</th>
														  <th>Confirmation#</th>
														  <th>Total</th>
														  <th>Remaining</th>
														  <th>Order Date</th>
														  <th class="actions text-right dt-not-orderable">Actions</th>
														</tr>
													</thead>
												</table>
											  </div>
											</div>
										</div>
									</div>
								</div>

								  {{-- Approved TRANSACTIONS TABLE GOES HERE --}}

								<div class="row">
									<div class="col-md-12">
										<div class="panel panel-bordered">
											<div class="panel-header">
												<h2 style="text-align: center;">Approved Transactions</h2>
											</div>
											<div class="panel-body">
											  <div class="table-responsive">
												<table id="approvedTransactionsDataTable" class="table table-hover" style="width: 100%;">
													<thead>
														<tr>
														  <th>First Name</th>
														  <th>Last Name</th>
														  <!--<th>Team</th>-->
														  <th>Tournament</th>
														  <th>Transaction#</th>
														  <th>Type</th>
														  <th>Amount</th>
														  <th>Transaction Date</th>
														</tr>
													</thead>
												</table>
											  </div>
											</div>
										</div>
									</div>
								</div>

								<div class="row">
									<div class="col-md-12">
										<div class="panel panel-bordered">
											<div class="panel-header">
												<h2 style="text-align: center;">Declined Transactions</h2>
											</div>
											<div class="panel-body">
											  <div class="table-responsive">
												<table id="declinedTransactionsDataTable" class="table table-hover" style="width: 100%;">
													<thead>
														<tr>
														  <th>First Name</th>
														  <th>Last Name</th>
														  <!--<th>Team</th>-->
														  <th>Tournament</th>
														  <th>Transaction#</th>
														  <th>Type</th>
														  <th>Amount</th>
														  <th>Declined Reason</th>
														  <th>Transaction Date</th>
														</tr>
													</thead>
												</table>
											  </div>
											</div>
										</div>
									</div>
								</div>

								  {{-- TRANSACTION ERRORS GO HERE --}}

								<div class="row">
									<div class="col-md-12">
										<div class="panel panel-bordered">
											<div class="panel-header">
												<h2 style="text-align: center;">Transaction Errors</h2>
											</div>
											<div class="panel-body">
											  <div class="table-responsive">
												<table id="transactionsErrorDataTable" class="table table-hover" style="width: 100%;">
													<thead>
														<tr>
														  <th>First Name</th>
														  <th>Last Name</th>
														  <!--<th>Team</th>-->
														  <th>Tournament</th>
														  <!--<th>Transaction#</th>
														  <th>Type</th>
														  <th>Amount</th>-->
														  <th>Transaction Error</th>
														  <th>Error Date</th>
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
		</div>
	</div>

</div>


<div class="modal modal-danger fade" tabindex="-1" id="_modal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('voyager::generic.close') }}"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Are you sure you want to deny this team request?</h4>
            </div>

			<!--<div class="modal-body">

			</div>-->
            <div class="modal-footer">
                <form action="#" id="_form" method="POST" style="margin-bottom: 0px;">
                    {{ csrf_field() }}

					

					
					
                    <input type="submit" class="btn btn-danger pull-right delete-confirm" id="_form_submit_btn" value="Deny Request">
                </form>
                <button type="button" class="btn btn-default pull-right" data-dismiss="modal">{{ __('voyager::generic.cancel') }}</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

@stop

@section('css')

<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css" integrity="sha384-DyZ88mC6Up2uqS4h/KRgHuoeGwBcD4Ng9SiP4dIRy0EXTlnuz47vAwmeGwVChigm" crossorigin="anonymous">
<!--<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.1/css/bootstrap-select.css" />-->
@stop

@section('javascript')

<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.1/js/bootstrap-select.min.js"></script> -->

<script type="text/javascript">
	window.onload = function() {
		

		
		$(document).ready(function(){

			/*
			"id" => $id,
            "customer" => $customer,
            "team_name" => $teamName,
            "tournament" => $tournament,
            "confirmation_number" => $confirmationNumber,
            "total" => $total,
            "remaining" => $remaining,
            "order_date" => $orderDate
            */
			$('#customersDataTable').DataTable({
				processing: true,
				serverSide: true,
				responsive: true,
				ajax: { "url": "/admin/orders/get-all-customers-data"
					},
				columns: [
					//{ data: 'id' },
					/*{ data: { first_name: 'first_name', last_name: 'last_name' },
					"render": function (data, type, row, meta) {
						return '<div>' + data.first_name + ' ' + data.last_name + '</div>';
					} },*/
					{ data: 'first_name'},
					{ data: 'last_name'},
					//{ data: 'team_name'},
					{ data: 'name' },
					{ data: 'confirmation_number' },
					{ data: 'total' },
					{ data: 'remaining' },
					{ data: 'order_date' },
					{
					"data": {id: 'id', total_paid: 'total_paid', remaining: 'remaining', deposit_amount: 'deposit_amount', total: 'total'},
					"render": function (data, type, row, meta) {

						var rData = '';

						if (data.total_paid != 0.00) {
							rData = '<button class="btn btn-sm btn-danger refundCustomer pull-right delete" id="'+data.id+'"><i class="voyager-trash" style="margin-right: 3px;"></i><span class="hidden-xs hidden-sm">Cancel</span></a>';
						}
						
						
						if (data.deposit_amount == 0.00 && data.total_paid == 0.00) {
							rData += '<button class="btn btn-sm btn-primary chargeDeposit pull-right edit" data-remaining="'+data.remaining+'" id="'+data.id+'"><i class="voyager-edit" style="margin-right: 3px;"></i><span class="hidden-xs hidden-sm">Charge Deposit</span></a>';
						}

						if (data.deposit_amount != 0.00 && data.total_paid != 0.00 && data.total != data.total_paid) {
							rData += '<button class="btn btn-sm btn-primary chargeRemaining pull-right edit" data-transaction-type="1" data-remaining="'+data.remaining+'" id="'+data.id+'"><i class="voyager-edit" style="margin-right: 3px;"></i><span class="hidden-xs hidden-sm">Charge Remaining</span></a>';
						}

						if (data.deposit_amount == 0.00 && data.total_paid == 0.00) {
							rData += '<button class="btn btn-sm btn-primary chargeRemaining pull-right edit" data-transaction-type="4" data-remaining="'+data.remaining+'" id="'+data.id+'"><i class="voyager-edit" style="margin-right: 3px;"></i><span class="hidden-xs hidden-sm">Charge Full</span></a>';
						}

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

			$('#customersDataTable').on('click', '.refundCustomer', function (e) {
				console.log('hi');
				var customerID = $(this).attr('id');

				$.ajax({
					headers: {
								'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
					},
					type: "post",
					data: {
							"customerID": customerID,
							"type": "3"
						 },
					dataType:'json',
					url: "/admin/transactions/charge-remaining",
					success:function(data){

						$('#customersDataTable').DataTable().ajax.reload();

						toastr.success(data['message']);
					},
					error:function(err) {

					}

				});
			});


			$('#customersDataTable').on('click', '.chargeRemaining', function (e) {
				//console.log('hey');
				var customerID = $(this).attr('id');
				var amount = $(this).data('remaining');

				var transactionType = $(this).data('transaction-type');

				$.ajax({
					headers: {
								'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
					},
					type: "post",
					data: {
							"customerID": customerID,
							"amount": amount,
							"type": transactionType
						 },
					dataType:'json',
					url: "/admin/transactions/charge-remaining",
					success:function(data){

						$('#customersDataTable').DataTable().ajax.reload();

						toastr.success(data['message']);
					},
					error:function(err) {

					}

				});

			});

			$('#customersDataTable').on('click', '.chargeDeposit', function (e) {
				//console.log('hey');
				var customerID = $(this).attr('id');
				var amount = $(this).data('remaining');

				$.ajax({
					headers: {
								'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
					},
					type: "post",
					data: {
							"customerID": customerID,
							"amount": amount,
							"type": '2'
						 },
					dataType:'json',
					url: "/admin/transactions/charge-remaining",
					success:function(data){
						console.log(data);
						toastr.success(data['message']);

						$('#customersDataTable').DataTable().ajax.reload();

						
					},
					error:function(err) {
						console.log(err);
					}

				});

			});

		$('#approvedTransactionsDataTable').DataTable({
				processing: true,
				serverSide: true,
				responsive: true,
				ajax: { "url": "/admin/transactions/get-all-approved-transactions-data"
					},
				columns: [
					//{ data: 'id' },
					/*{ data: { first_name: 'first_name', last_name: 'last_name' },
					"render": function (data, type, row, meta) {
						return '<div>' + data.first_name + ' ' + data.last_name + '</div>';
					} },*/
					{ data: 'first_name'},
					{ data: 'last_name'},
					//{ data: 'team_name'},
					{ data: 'name' },
					{ data: 'transaction_id' },
					{ data: 'transaction_type',
					render: function(data, type, row, meta) {
						var renderedData = "<div>";
						if (data == 1) {
							renderedData += "Full Charged";
						} else if (data == 2) {
							renderedData += "Deposit Charged";
						} else if (data == 3) {
							renderedData += "Refund";
						} else if (data == 4) {
							renderedData += "Remaining Charged";
						}
						renderedData += "</div>";
						return renderedData;
					} },
					{ data: 'amount' },
					{ data: 'created_at',
					render: function(data, type, row, meta) {
						return "<div>" + data.substr(0,data.indexOf(' ')) + "</div>";
					}
					}
					//{ data: 'endDate'}
				],
				columnDefs: [
				{'targets': 'dt-not-orderable', 'searchable': false, 'orderable': false},
				]
			});

			$('#declinedTransactionsDataTable').DataTable({
				processing: true,
				serverSide: true,
				responsive: true,
				ajax: { "url": "/admin/transactions/get-all-declined-transactions-data"
					},
				columns: [
					//{ data: 'id' },
					/*{ data: { first_name: 'first_name', last_name: 'last_name' },
					"render": function (data, type, row, meta) {
						return '<div>' + data.first_name + ' ' + data.last_name + '</div>';
					} },*/
					{ data: 'first_name'},
					{ data: 'last_name'},
					//{ data: 'team_name'},
					{ data: 'name' },
					{ data: 'transaction_id' },
					{ data: 'transaction_type',
					render: function(data, type, row, meta) {
						var renderedData = "<div>";
						if (data == 1) {
							renderedData += "Full Charged";
						} else if (data == 2) {
							renderedData += "Deposit Charged";
						} else if (data == 3) {
							renderedData += "Refund";
						} else if (data == 4) {
							renderedData += "Remaining Charged";
						}
						renderedData += "</div>";
						return renderedData;
					} },
					{ data: 'amount' },
					{ data: 'declined_response'},
					{ data: 'created_at',
					render: function(data, type, row, meta) {
						return "<div>" + data.substr(0,data.indexOf(' ')) + "</div>";
					}
					}
					//{ data: 'endDate'}
				],
				columnDefs: [
				{'targets': 'dt-not-orderable', 'searchable': false, 'orderable': false},
				]
			});

			$('#transactionsErrorDataTable').DataTable({
				processing: true,
				serverSide: true,
				responsive: true,
				ajax: { "url": "/admin/transactions/get-all-transactions-error-data"
					},
				columns: [
					//{ data: 'id' },
					/*{ data: { first_name: 'first_name', last_name: 'last_name' },
					"render": function (data, type, row, meta) {
						return '<div>' + data.first_name + ' ' + data.last_name + '</div>';
					} },*/
					{ data: 'first_name'},
					{ data: 'last_name'},
					//{ data: 'team_name'},
					{ data: 'name' },
					/*{ data: 'transaction_id' },
					{ data: 'transaction_type',
					render: function(data, type, row, meta) {
						var renderedData = "<div>";
						if (data == 1) {
							renderedData += "Full Charged";
						} else if (data == 2) {
							renderedData += "Deposit Charged";
						} else if (data == 3) {
							renderedData += "Refund";
						} else if (data == 4) {
							renderedData += "Remaining Charged";
						}
						renderedData += "</div>";
						return renderedData;
					} },
					{ data: 'amount' },*/
					{ data: 'error_given'},
					{ data: 'created_at',
					render: function(data, type, row, meta) {
						return "<div>" + data.substr(0,data.indexOf(' ')) + "</div>";
					}
					}
					//{ data: 'endDate'}
				],
				columnDefs: [
				{'targets': 'dt-not-orderable', 'searchable': false, 'orderable': false},
				]
			});

			/*

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
						'trHotelRoomData': JSON.stringify(trHotelRoomsData)
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
						'trHotelRoomData': JSON.stringify(trHotelRoomsData)
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
				//});*/

			
		})
	}
</script>
@stop
