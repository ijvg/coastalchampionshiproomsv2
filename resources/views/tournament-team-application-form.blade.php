@extends('layouts.app')

@section('content')

        @php $numberOfHotelRooms = 0; @endphp
        <div id="homeOuterCon">

            <div id="homeHeroImageCon">
                <img id="homeHeroImage" src="{{ Voyager::image( $tournament->image ) }}" style="width: 100%;">
                <p class="imageCredit">
                    {!! $tournament->image_credit !!}
                </p>
            </div>

            <div class="container">
                <h1 style="text-align: center; margin-top: 40px;">{{ $tournament->name }} Application Form</h1>

                <div id="tr_message" style="margin-top: 50px; margin-bottom: 50px; text-align: center; display: none; font-weight: bolder; font-size: 2.4rem;">

                </div>

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
										
										@if ($tournamentHotelRoom->rooms_available < 1)
											
										<div class="teamRequestRoomsHotelSoldOut">
										The rooms for this type are currently unavailable, please another hotel option / room type <!--or contact us if you would for us 
											to reach out to the hotel to request more of these rooms before submitting.-->
										</div>
											
										
										@endif

										@if ($tournamentHotelRoom->rooms_available > 0)
										<div class="form-group requestedRoomsFormGroup hotelRoom">
											<label class="control-label">Rooms Requesting</label>
											<input class="form-control trHotelRoomTypeInput" type="number" max="{{ $tournamentHotelRoom->rooms_available }}" data-hotel-room-id="{{ $tournamentHotelRoom->id }}" name="rooms-requested-{{ $tournamentHotelRoom->id }}" value="0">
										</div>
										@endif

									@endforeach

								</div>

							@endif
						@endforeach

                        <input type="hidden" class="roomCounter" name="numberOfRooms" value="0">

					</div>

					<div class="form-group clearfix">

						<button type="submit" id="teamRequestFormSubmit" style="display: none;" class="btn btn-success btn-submit float-right">Submit</button> 
					</div>
					
                </form>
            </div>
        </div>

        @php $trRoomGroup = ""; $trrgCount = 0; @endphp
        @foreach ($tournament->hotels as $tournamentHotel)
            @foreach( $tournamentHotel->pivot->tournamentHotelRooms() as $teamRequestRoom) 
                @php if ($trrgCount == 0) { $trRoomGroup .= "rooms-requested-" . $teamRequestRoom->id; } else { $trRoomGroup .= " rooms-requested-" . $teamRequestRoom->id; } $trrgCount++; @endphp
            @endforeach
        @endforeach

@endsection


@section('scripts')

<script async src="https://www.google.com/recaptcha/api.js?render=6LcJDiccAAAAAGmp2UZzphjAAXIF2mimKqubxkdJ"></script>

<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js" defer></script> -->

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/jquery.validate.min.js" defer></script>
<script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.3/dist/additional-methods.min.js" defer></script>

<script defer>
    document.addEventListener("DOMContentLoaded", function(){

        var tournamentID = <?php echo $tournament->id; ?>;

        $(document).ready(function(){

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

    /*$('input.trHotelRoomTypeInput').on('blur keyup', function() {
        var totalRooms = 0;
        $('.roomsInput').each(function(i, obj) {
            totalRooms += $(this).val();
        });

        $('#tr_application_form input[name="numberOfRooms"]').val(totalRooms);

        $('#tr_application_form input[name="numberOfRooms"]')[0].checkValidity();
    });*/

            
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
                    $("#hotel-" + selectedHotel).show('fade');
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

            $('input').on('blur keyup', function() {
                //console.log($("#tr_application_form").validate().checkForm());

                //console.log($(this).valid());
                //$(this).valid();
                

                if ($("#tr_application_form").validate().checkForm()) {
                    $('#teamRequestFormSubmit').show();  
                } else {
                    $('#teamRequestFormSubmit').hide();
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

                var trHotelRoomsData = new Array();

                //$('#tr_application_form .teamRequestRooms #hotel-' + selectedHotel + ' input[type=number]').hide();

                $('#tr_application_form .teamRequestRooms #hotel-' + selectedHotel + ' input[type=number]').each(function(){
                    console.log($(this).val());
                    trHotelRoomsData.push({ 'hotelRoomID': $(this).data('hotel-room-id'), 'roomsRequested': $(this).val()});

                });

                var teamRequestData = {
                    'hotelID': selectedHotel,
                    'tournamentID': tournamentID,
                    'teamName': teamName,
                    'contactFirstName': contactFirstName,
                    'contactLastName': contactLastName,
                    'phone': phoneNumber,
                    'email': email,
                    'specialRequest': specialRequest,
                    'trHotelRoomData': JSON.stringify(trHotelRoomsData)
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

                        $("#tr_application_form").hide('fade');

                        $('#tr_message').html(data['message']);

                        $('#tr_message').show('fade');
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
        });
        
    });

</script>
@endsection
