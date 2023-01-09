@extends('layouts.app')

@section('content')

        @if ($teamRequest->totalTeamRoomsAvailable() === 0)
            
            <h1 class="text-center" style="padding-top: 50px;">All rooms have been allocated for this teams request</h1>

            <div class="fill-content-area"></div>

            @section('scripts')
            <script type="text/javascript">
                document.addEventListener("DOMContentLoaded", function(){
                    $(document).ready(function() {
                        var headerHeight = $("body .main-nav-menu").outerHeight();
                        var footerHeight = $("body footer").outerHeight();
                        var combinedheight = headerHeight + footerHeight;
                        var messageHeight = $("main").outerHeight();
                        var bodyHeight = $("body").height();
                        var fillHeight = bodyHeight - combinedheight;
                        fillHeight = fillHeight - messageHeight;
                        $(".fill-content-area").height(fillHeight);
                    });

                });
                
            </script>
            @endsection
        
        @elseif (Carbon\Carbon::parse($teamRequest->link_expire_date) <= Carbon\Carbon::today())

            <h1 class="text-center" style="padding-top: 50px;">This link is expired</h1>
            <div class="fill-content-area"></div>

            @section('scripts')
            <script type="text/javascript">
                document.addEventListener("DOMContentLoaded", function(){
                    $(document).ready(function() {
                        var headerHeight = $("body .main-nav-menu").outerHeight();
                        var footerHeight = $("body footer").outerHeight();
                        var combinedheight = headerHeight + footerHeight;
                        var messageHeight = $("main").outerHeight();
                        var bodyHeight = $("body").height();
                        var fillHeight = bodyHeight - combinedheight;
                        fillHeight = fillHeight - messageHeight;
                        $(".fill-content-area").height(fillHeight);
                    });

                });
                
            </script>
            @endsection
        @else

            @php $roomsTypesAvailable = 0; $roomNameIfOneOnly = ""; $hotelTournament = $teamRequest->getHotelTournament(); @endphp

            <div id="homeOuterCon">

                <div id="homeHeroImageCon">
                    <img id="homeHeroImage" src="{{ Voyager::image( $tournament->image ) }}" style="width: 100%;">
                    <p class="imageCredit">
                        {!! $tournament->image_credit !!}
                    </p>
                </div>

                <div class="container">
                    <h1 style="text-align: center; margin-top: 40px;">{{ $tournament->name }}</h1>


                    <div class="hotelImagesAndInfoCon">
						
						<div class="teamMemberHotelNameCon" style="width: 100%; text-align: center;">
                        	<h3 class="teamMemberHotelName">{{ $teamRequest->getHotel()->name }}</h3>
						</div>
						

                        <div class="row">
                            <div class="col-md-12 teamMemberFormHotelImageCon" style="text-align: center;">
                                <img id="teamMemberFormHotelImage" src="{{ Voyager::image( $teamRequest->getHotel()->image ) }}">
                            </div>
                            <div class="col-md-12 teamMemberFormHotelInfo">
                                
                                
                                <div class="col-md-12 teamMemberFormHotelInfo">
                                @if (!is_null($hotelTournament[0]->min_nights_stay))
	                            	<h5 style="margin-bottom: 25px;">Minimum Number of Nights: {{ $hotelTournament[0]->min_nights_stay }}</h5>
	                            @endif
	                            
	                            @if (!is_null($hotelTournament[0]->cancellation_policy))
	                                <div class="cancellationPolicyCon" width="100%">
	                                	<h4 class="tournamentCancellationPolicy">Cancellation Policy</h4>
	                                
	                                
										{!! $hotelTournament[0]->cancellation_policy !!}
	                                </div>
	                                
	                                <hr>
	                                
                                @endif
	                            
                                {!! $teamRequest->getHotel()->description !!}
                                
                            
                                @if (!is_null($hotelTournament[0]->amenities))
	                                <div class="amenitiesCon" width="100%">
	                                	<h4 class="tournamentAmenities">Amenities</h4>
	                                
	                                
										{!! $hotelTournament[0]->amenities !!}
	                                </div>
                                @endif
                            </div>

                            </div>
                        </div>

                    </div>

                    <br>
                    <br>

                    <h2 style="text-align: center; margin-top: 25px;">{{ $teamRequest->team_name }}
                        <br>Team Members Room's Form</h2>

                    <div id="tr_message" style="margin-top: 50px; margin-bottom: 50px; text-align: center; display: none; font-weight: bolder; font-size: 2.4rem;">

                    </div>

                    <div id="reservationProcessCon" style="margin-bottom: 50px; margin-top: 50px;">

                        <i>
                            Approximately 30 days before you check in to the hotel, we will take a deposit of your first night’s stay plus tax from the card you have put on file. 
                            If making a reservation within 30 days of check in, a deposit will be taken at the time you make your reservation. 7 days prior to arrival we will charge 
                            your card for the remaining balance. Both charges are refundable if you cancel before your hotel’s individual cancellation timeframe. This timeframe is 
                            typically between 48-72 hours prior to arrival but is specific to the hotel you booked. You can see your hotel’s cancellation policy on the hotel listing 
                            page under the tournament you signed up for. Prior to hotel check in you will receive an email with your reservation information and your hotel confirmation 
                            number. At check in, you will be asked to provide a credit card to keep on file for incidentals such as parking fees or breakfast if your hotel does not provide 
                            those amenities complimentary.
                        </i>

                    </div>


                    <form action="/team-requests/tmr-submit-request" class="needs-validation" novalidate id="tmr_application_form" method="POST" style="margin-bottom: 0px;">

                        <input type="hidden" name="trID" value="{{ $teamRequest->id }}">

                        <input type="hidden" name="tID" value="{{ $tournament->id }}">

                        <input type="hidden" name="hID" value="{{ $teamRequest->getHotel()->id }}">

                        <div class="form-group">
                            <label class="control-label">First Name</label>
                            <input class="form-control required" type="text" name="firstName" id="firstName" required>
                        </div>

                        <div class="form-group">
                            <label class="control-label">Last Name</label>
                            <input class="form-control" type="text" name="lastName" required>
                        </div>

                        <div class="form-group">
                            <label for="address">Address</label>
                            <input type="text" class="form-control" id="address" name="address" placeholder="1234 Main St" required>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group col-md-6">
                            <label for="city">City</label>
                            <input type="text" class="form-control" id="city" name="city" required>
                            </div>
                            <div class="form-group col-md-4">
                            <label for="state">State</label>
                            <select id="state" class="form-control" name="state" required>
                                <option selected disabled value="">Choose...</option>
                                @foreach ($states as $key => $value)
                                    <option value="{{ $key }}">{{ $value }}</option>
                                @endforeach
                            </select>
                            </div>
                            <div class="form-group col-md-2">
                            <label for="zip">Zip</label>
                            <input type="text" class="form-control" id="zip" name="zip" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label">Phone</label>
                            <input class="form-control" type="phone" name="phone">
                        </div>

                        <div class="form-group">
                            <label class="control-label">Email</label>
                            <input class="form-control" type="email" name="email" required>
                        </div>

                        <div style="width: 100%; text-align: center;">
                            <h4>{{ $teamRequest->getHotel()->name }}</h4>
                        </div>

                        <div id="rooms-form-group" class="rooms-form-group-container">
                        
                        
                        @foreach( $teamRequest->teamRequestRooms as $teamRequestRoom)
                            @if ( $teamRequestRoom->available >= 1)

                                @php $roomNameIfOneOnly = "room-" . $teamRequestRoom->id;@endphp
                                <div class="form-group">
                                    {{-- <label class="control-label">{{ $teamRequestRoom->tournamentHotelRoom->hotelRoomtype->type }} - Price Per Night ${{ number_format($teamRequestRoom->tournamentHotelRoom->price_per_night + $teamRequestRoom->tournamentHotelRoom->booking_fee_per_night, 2, '.', '') }} - Rooms Available for your team: {{ $teamRequestRoom->available }}</label>
                                    <input class="form-control roomsInput" data-team-request-room-id="{{ $teamRequestRoom->id }}" data-price-per-night="{{ $teamRequestRoom->tournamentHotelRoom->price_per_night + $teamRequestRoom->tournamentHotelRoom->booking_fee_per_night }}" type="number" max="{{ $teamRequestRoom->available }}" name="room-{{ $teamRequestRoom->id }}" value="0"> --}}

                                    <label class="control-label">{{ $teamRequestRoom->tournamentHotelRoom->hotelRoomtype->type }} - Price Per Night ${{ number_format($teamRequestRoom->tournamentHotelRoom->gross_price_per_night,2, '.', '') }} - Rooms Available for your team: {{ $teamRequestRoom->available }}</label>
                                    <input class="form-control roomsInput" data-team-request-room-id="{{ $teamRequestRoom->id }}" data-price-per-night="{{ $teamRequestRoom->tournamentHotelRoom->gross_price_per_night }}" type="number" max="{{ $teamRequestRoom->available }}" name="room-{{ $teamRequestRoom->id }}" value="0">
                                </div>

                                <input type="hidden" name="total-cost-room-{{ $teamRequestRoom->id }}" value="">
                            @endif
                        @endforeach

                        </div>

                        @if( $teamRequest->allow_individual_check_in_out_dates != 0)
                            <div class="form-group row g-3">
                                <div class="col-md-6">
                                    <label for="checkInDate" class="control-label">Check In Date</label>
                                    <input type="text" class="form-control" id="checkInDatepicker" name="checkInDate" value="{{ $teamRequest->check_in }}">
                                </div>

                                <div class="col-md-6">
                                    <label for="checkOutDate" class="control-label">Check Out Date</label>
                                    <input type="text" class="form-control" id="checkOutDatepicker" name="checkOutDate" value="{{ $teamRequest->check_out }}">
                                </div>

                            </div>

                            <label style="font-weight: 500; font-size: 1.4rem;" for="numberOfNights" class="control-label" id="numberOfNightsLabel"><span>{{ $teamRequest->number_of_nights }}</span> nights</label>
                            <input type="hidden" name="numberOfNights" value="{{ $teamRequest->number_of_nights }}">
                            

                            <!-- <input type="date" name="checkInDate">

                            <input type="hidden" name="checkOutDate">

                            <input type="number" name="numberOfNights">-->
                        @else
                            <input type="hidden" name="checkInDate" value="{{ $teamRequest->check_in }}">
                            <input type="hidden" name="checkOutDate" value="{{ $teamRequest->check_out }}">
                            <input type="hidden" name="numberOfNights" value="{{ $teamRequest->number_of_nights }}">
                            
                            <h5>Check-in: {{ $teamRequest->check_in }}</h5>
                            <h5>Check-out: {{ $teamRequest->check_out }}</h5>
                            <h5>Number of Nights: {{ $teamRequest->number_of_nights }}</h5>

                        @endif

							

                        <div id="priceCon"> <!-- style="display: none;" -->
                            <div id="hotelRoomsPriceCon">Rooms Total: <span id="hotelRoomsPrice"></span></div>
                            <div id="hotelRoomsTaxCon">State & Local Tax: <span id="hotelRoomsTax"></span></div>
                            <div id="purchasingFeeCon">Occupancy Tax: <span id="purchasingFee"></span></div>
                            <div id="transactionFeeCon">Transaction Fee: <span id="transactionFee"></span></div>

                            <div id="totalPriceCon">Total: <span id="totalPrice"></span></div>
                        </div>
                        
                        <div id="specialRequestCon form-group">
	                        <label for="specialRequest">Special Request:</label>
							<textarea class="form-control" rows="5" id="specialRequest"></textarea>
                        </div>

                        <div class="form-group input-field">
                            <label for="ccnumber">Card number</label>
                            <div class="input-group">
                            
                            {{-- <input type="text" name="ccnumber" onkeypress="return checkDigit(event)" placeholder="Your card number" class="form-control" required> --}}
                            <!-- <input id="ccnumber" type="tel" class="input-lg form-control cc-number" autocomplete="cc-number" placeholder="•••• •••• •••• ••••" required> -->
                            <div id="ccnumber"></div>
                            
                            <div class="input-group-append">
                                <span class="input-group-text text-muted">
                                                            <i class="fa fa-cc-visa mx-1"></i>
                                                            <i class="fa fa-cc-amex mx-1"></i>
                                                            <i class="fa fa-cc-mastercard mx-1"></i>
                                                        </span>
                                </div>
                            </div>
                            <span id="ccnumberInvalid" style="display: none;">* required</span>
                        </div>
                        <div class="form-group input-field">
                            <label for="ccexp">Expiration</label>
                            <div id="ccexp"></div>
                            <span id="ccexpInvalid" style="display: none;">* required</span>
                        </div>

                        <div class="form-group input-field">
                            <label for="cccvv">CCV</label>
                            <div id="cccvv"></div>
                            <span id="cccvvInvalid" style="display: none;">* required</span>
                        </div>
                        {{-- <div class="row">
                            <div class="col-sm-8">
                                <div class="form-group">
                                    <label><span class="hidden-xs">Expiration</span></label>

                                    <div class="form-group"> <label for="cc-exp" class="control-label">CARD EXPIRY</label> 
                                        <!-- <input id="ccexp" type="tel" class="input-lg form-control cc-exp" autocomplete="cc-exp" placeholder="•• / ••" required> -->
                                        <div id="ccexp"></div>
                                    </div>
                                </div>
                            </div>
                        </div> --}}

                        <div class="form-group">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="agreeCheck">
                                <label class="form-check-label" for="agreeCheck">
                                Do you agree to the <a href="#" id="termsAndConditionsLink" data-toggle="modal" data-target="#tcModal">terms & conditions</a>
                                </label>
                            </div>
                        </div>

                        <input type="hidden" name="recaptcha_response" id="recaptchaResponse">

                        <div class="form-group clearfix">

                            <button type="submit" style="display: none;" id="teamRequestFormSubmit" class="btn btn-success btn-submit float-right">Submit</button> 
                        </div>

                    </form>
                
                </div>

            </div>

        

        <div id="loader" class="lds-dual-ring display-none overlay"></div>

        @php $trRoomGroup = ""; $trrgCount = 0; @endphp
        @foreach( $teamRequest->teamRequestRooms as $teamRequestRoom) 
            @php if ($trrgCount == 0) { $trRoomGroup .= "room-" . $teamRequestRoom->id; } else { $trRoomGroup .= " room-" . $teamRequestRoom->id; } $trrgCount++; @endphp
        @endforeach

        <div class="modal fade" id="tcModal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog" role="document">
              <div class="modal-content">
                <div class="modal-header">
                    <h2 class="modal-title"><strong>Terms and Conditions</strong></h2>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
                <div class="modal-body">
                
        

 

        <p>Welcome to Championship City Rooms !</p>
        
         
        
        <p>These terms and conditions outline the rules and regulations for the use of Championship City Rooms's Website, located at www.ChampionshipCityRooms.com.</p>
        
         
        
        <p>By accessing this website we assume you accept these terms and conditions. Do not continue to use Championship City Rooms  if you do not agree to take all of the terms and conditions stated on this page.</p>
        
         
        
        <p>The following terminology applies to these Terms and Conditions, Privacy Statement and Disclaimer Notice and all Agreements: "Client", "You" and "Your" refers to you, the person log on this website and compliant to the Company’s terms and conditions. "The Company", "Ourselves", "We", "Our" and "Us", refers to our Company. "Party", "Parties", or "Us", refers to both the Client and ourselves. All terms refer to the offer, acceptance and consideration of payment necessary to undertake the process of our assistance to the Client in the most appropriate manner for the express purpose of meeting the Client’s needs in respect of provision of the Company’s stated services, in accordance with and subject to, prevailing law of the USA. Any use of the above terminology or other words in the singular, plural, capitalization and/or he/she or they, are taken as interchangeable and therefore as referring to same.</p>
        
         
        
        <h3><strong>Cookies</strong></h3>
        
         
        
        <p>We employ the use of cookies. By accessing Championship City Rooms , you agreed to use cookies in agreement with the Championship City Rooms's Privacy Policy. </p>
        
         
        
        <p>Most interactive websites use cookies to let us retrieve the user’s details for each visit. Cookies are used by our website to enable the functionality of certain areas to make it easier for people visiting our website. Some of our affiliate/advertising partners may also use cookies.</p>
        
         
        
        <h3><strong>License</strong></h3>
        
         
        
        <p>Unless otherwise stated, Championship City Rooms and/or its licensors own the intellectual property rights for all material on Championship City Rooms . All intellectual property rights are reserved. You may access this from Championship City Rooms  for your own personal use subjected to restrictions set in these terms and conditions.</p>
        
         
        
        <p>You must not:</p>
        
        <ul>
        
            <li>Republish material from Championship City Rooms </li>
        
            <li>Sell, rent or sub-license material from Championship City Rooms </li>
        
            <li>Reproduce, duplicate or copy material from Championship City Rooms </li>
        
            <li>Redistribute content from Championship City Rooms </li>
        
        </ul>
        
         
        
        <p>This Agreement shall begin on the date hereof. Our Terms and Conditions were created with the help of the <a href=https://www.termsandconditionsgenerator.com>Terms And Conditions Generator</a>.</p>
        
         
        
        <p>Parts of this website offer an opportunity for users to post and exchange opinions and information in certain areas of the website. Championship City Rooms does not filter, edit, publish or review Comments prior to their presence on the website. Comments do not reflect the views and opinions of Championship City Rooms,its agents and/or affiliates. Comments reflect the views and opinions of the person who post their views and opinions. To the extent permitted by applicable laws, Championship City Rooms shall not be liable for the Comments or for any liability, damages or expenses caused and/or suffered as a result of any use of and/or posting of and/or appearance of the Comments on this website.</p>
        
         
        
        <p>Championship City Rooms reserves the right to monitor all Comments and to remove any Comments which can be considered inappropriate, offensive or causes breach of these Terms and Conditions.</p>
        
         
        
        <p>You warrant and represent that:</p>
        
         
        
        <ul>
        
            <li>You are entitled to post the Comments on our website and have all necessary licenses and consents to do so;</li>
        
            <li>The Comments do not invade any intellectual property right, including without limitation copyright, patent or trademark of any third party;</li>
        
            <li>The Comments do not contain any defamatory, libelous, offensive, indecent or otherwise unlawful material which is an invasion of privacy</li>
        
            <li>The Comments will not be used to solicit or promote business or custom or present commercial activities or unlawful activity.</li>
        
        </ul>
        
         
        
        <p>You hereby grant Championship City Rooms a non-exclusive license to use, reproduce, edit and authorize others to use, reproduce and edit any of your Comments in any and all forms, formats or media.</p>
        
         
        
        <h3><strong>Hyperlinking to our Content</strong></h3>
        
         
        
        <p>The following organizations may link to our Website without prior written approval:</p>
        
         
        
        <ul>
        
            <li>Government agencies;</li>
        
            <li>Search engines;</li>
        
            <li>News organizations;</li>
        
            <li>Online directory distributors may link to our Website in the same manner as they hyperlink to the Websites of other listed businesses; and</li>
        
            <li>System wide Accredited Businesses except soliciting non-profit organizations, charity shopping malls, and charity fundraising groups which may not hyperlink to our Web site.</li>
        
        </ul>
        
         
        
        <p>These organizations may link to our home page, to publications or to other Website information so long as the link: (a) is not in any way deceptive; (b) does not falsely imply sponsorship, endorsement or approval of the linking party and its products and/or services; and (c) fits within the context of the linking party’s site.</p>
        
         
        
        <p>We may consider and approve other link requests from the following types of organizations:</p>
        
         
        
        <ul>
        
            <li>commonly-known consumer and/or business information sources;</li>
        
            <li>dot.com community sites;</li>
        
            <li>associations or other groups representing charities;</li>
        
            <li>online directory distributors;</li>
        
            <li>internet portals;</li>
        
            <li>accounting, law and consulting firms; and</li>
        
            <li>educational institutions and trade associations.</li>
        
        </ul>
        
         
        
        <p>We will approve link requests from these organizations if we decide that: (a) the link would not make us look unfavorably to ourselves or to our accredited businesses; (b) the organization does not have any negative records with us; (c) the benefit to us from the visibility of the hyperlink compensates the absence of Championship City Rooms; and (d) the link is in the context of general resource information.</p>
        
         
        
        <p>These organizations may link to our home page so long as the link: (a) is not in any way deceptive; (b) does not falsely imply sponsorship, endorsement or approval of the linking party and its products or services; and (c) fits within the context of the linking party’s site.</p>
        
         
        
        <p>If you are one of the organizations listed in paragraph 2 above and are interested in linking to our website, you must inform us by sending an e-mail to Championship City Rooms. Please include your name, your organization name, contact information as well as the URL of your site, a list of any URLs from which you intend to link to our Website, and a list of the URLs on our site to which you would like to link. Wait 2-3 weeks for a response.</p>
        
         
        
        <p>Approved organizations may hyperlink to our Website as follows:</p>
        
         
        
        <ul>
        
            <li>By use of our corporate name; or</li>
        
            <li>By use of the uniform resource locator being linked to; or</li>
        
            <li>By use of any other description of our Website being linked to that makes sense within the context and format of content on the linking party’s site.</li>
        
        </ul>
        
         
        
        <p>No use of Championship City Rooms's logo or other artwork will be allowed for linking absent a trademark license agreement.</p>
        
         
        
        <h3><strong>iFrames</strong></h3>
        
         
        
        <p>Without prior approval and written permission, you may not create frames around our Webpages that alter in any way the visual presentation or appearance of our Website.</p>
        
         
        
        <h3><strong>Content Liability</strong></h3>
        
         
        
        <p>We shall not be hold responsible for any content that appears on your Website. You agree to protect and defend us against all claims that is rising on your Website. No link(s) should appear on any Website that may be interpreted as libelous, obscene or criminal, or which infringes, otherwise violates, or advocates the infringement or other violation of, any third party rights.</p>
        
         
        
        <h3><strong>Your Privacy</strong></h3>
        
         
        
        <p>Please read Privacy Policy</p>
        
         
        
        <h3><strong>Reservation of Rights</strong></h3>
        
         
        
        <p>We reserve the right to request that you remove all links or any particular link to our Website. You approve to immediately remove all links to our Website upon request. We also reserve the right to amen these terms and conditions and it’s linking policy at any time. By continuously linking to our Website, you agree to be bound to and follow these linking terms and conditions.</p>
        
         
        
        <h3><strong>Removal of links from our website</strong></h3>
        
         
        
        <p>If you find any link on our Website that is offensive for any reason, you are free to contact and inform us any moment. We will consider requests to remove links but we are not obligated to or so or to respond to you directly.</p>
        
         
        
        <p>We do not ensure that the information on this website is correct, we do not warrant its completeness or accuracy; nor do we promise to ensure that the website remains available or that the material on the website is kept up to date.</p>
        
         
        
        <h3><strong>Disclaimer</strong></h3>
        
         
        
        <p>To the maximum extent permitted by applicable law, we exclude all representations, warranties and conditions relating to our website and the use of this website. Nothing in this disclaimer will:</p>
        
         
        
        <ul>
        
            <li>limit or exclude our or your liability for death or personal injury;</li>
        
            <li>limit or exclude our or your liability for fraud or fraudulent misrepresentation;</li>
        
            <li>limit any of our or your liabilities in any way that is not permitted under applicable law; or</li>
        
            <li>exclude any of our or your liabilities that may not be excluded under applicable law.</li>
        
        </ul>
        
         
        
        <p>The limitations and prohibitions of liability set in this Section and elsewhere in this disclaimer: (a) are subject to the preceding paragraph; and (b) govern all liabilities arising under the disclaimer, including liabilities arising in contract, in tort and for breach of statutory duty.</p>
        
         
        
        <p>As long as the website and the information and services on the website are provided free of charge, we will not be liable for any loss or damage of any nature.</p>

    </div>
    <div class="modal-footer">
      <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
    </div>
  </div>
</div>
</div>



@endsection
{{-- 
    @foreach( $teamRequest->teamRequestRooms as $teamRequestRoom) 
                     
                    "room-{{$teamRequestRoom->id}}": { 
                        @endforeach
--}}
{{-- "room-{{$teamRequest->teamRequestRooms[0]->id}}": { --}}

@section('styles')
<!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.css"> -->
 <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css"> 
@endsection

@section('scripts')
<script async src="https://www.google.com/recaptcha/api.js?render=6LcJDiccAAAAAGmp2UZzphjAAXIF2mimKqubxkdJ"></script>

<script src="https://integratepayments.transactiongateway.com/token/Collect.js" data-tokenization-key="T8bm75-az9U37-4rqn6f-a57Gnu" defer></script>

<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js" defer></script> -->

<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js" defer></script> 

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/jquery.validate.min.js" defer></script>
<script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.3/dist/additional-methods.min.js" defer></script>

{{-- <script src="{{ asset('js/jquery.validate.js') }}" defer></script>
<script src="{{ asset('js/additional-methods.js') }}" defer></script> --}}
<script defer>

    document.addEventListener("DOMContentLoaded", function(){

        var tournamentID = <?php echo $tournament->id; ?>;

        var validCardNumber = false; 
        var validExpiration = false; 
        var validCvv = false;

        var validRooms = false;

        var ppnTotal = 0;
        var totalTax = 0;
        var siteTransactionPercent = 0;
        var siteTransactionFee = 0;
        var hotelFee = 0;

        var flatFeePerNight = {{ $teamRequest->getHotel()->flat_fee_per_night }};
        var hotelTax = {{ $teamRequest->getHotel()->tax_fee_per_night }};

        var siteTransactionFlatFee = {{ $siteFee }};
        var siteTransactionFeePercent = {{ $sitePercentFee }};

        var totalCost = 0;

        var firstNightDeposit = 0;
        //var roomNights = 0;

        $(document).ready(function(){

        $("#termsAndConditions").click(function() {
            ('#myModal').on('shown.bs.modal', function () {
            });
        });


        var checkInMin = new Date("{{ $teamRequest->check_in }}".replace(/-0+/g, '-'));
        var checkInMax = new Date("{{ $teamRequest->check_out }}".replace(/-0+/g, '-'));
        checkInMax.setDate(checkInMax.getDate());

        checkInMin.setDate(checkInMin.getDate() + 1);

        var checkOutMin = new Date();
        checkOutMin.setDate(checkInMin.getDate() + 1);

        var checkOutMax = new Date("{{ $teamRequest->check_out }}".replace(/-0+/g, '-'));
        checkOutMax.setDate(checkOutMax.getDate() + 1);

        $("#checkInDatepicker").datepicker({
            dateFormat: 'yy-mm-dd',
            minDate: checkInMin,
            maxDate: checkInMax,
            onSelect: function(date){
                
                var selectedDate = new Date(date.replace(/-0+/g, '-'));

                selectedDate.setDate(selectedDate.getDate() + 1);

                var checkOutMinDate = new Date();
                checkOutMinDate.setDate(selectedDate.getDate());


                $("#checkOutDatepicker").datepicker("option", "minDate", checkOutMinDate);

                var selectedCheckOutDate = new Date($("#checkOutDatepicker").val().replace(/-0+/g, '-'));

                selectedCheckOutDate.setDate(selectedCheckOutDate.getDate() + 1);

                var dateDifference = daysdifference(selectedDate, selectedCheckOutDate);

                $('#tmr_application_form input[name="numberOfNights"]').val(dateDifference);
                
                $("#numberOfNightsLabel span").html(dateDifference);

                calculatePrice();
            }
        });
        $("#checkOutDatepicker").datepicker({
            dateFormat: 'yy-mm-dd',
            minDate: checkOutMin,
            maxDate: checkOutMax,
            onSelect: function(date){
                var selectedDate = new Date(date.replace(/-0+/g, '-'));
                selectedDate.setDate(selectedDate.getDate() + 1);

                var selectedCheckInDate = new Date($("#checkInDatepicker").val().replace(/-0+/g, '-'));
                selectedCheckInDate.setDate(selectedCheckInDate.getDate() + 1);

                var dateDifference = daysdifference(selectedCheckInDate, selectedDate);

                $('#tmr_application_form input[name="numberOfNights"]').val(dateDifference);

                $("#numberOfNightsLabel span").html(dateDifference);

                calculatePrice();
            }
        });

        function daysdifference(firstDate, secondDate){
            var startDay = new Date(firstDate);
            var endDay = new Date(secondDate);

            console.log(startDay + ' ' + endDay);

            var millisBetween = startDay.getTime() - endDay.getTime();
            var days = millisBetween / (1000 * 3600 * 24);
        
            days = days;

            return Math.round(Math.abs(days));
        }


        /*var nowTemp = new Date();
        var now = new Date(nowTemp.getFullYear(), nowTemp.getMonth(), nowTemp.getDate(), 0, 0, 0, 0);

        var checkin = $('#dp1').datepicker({

        beforeShowDay: function(date) {
            return date.valueOf() >= now.valueOf();
        },
        autoclose: true

        }).on('changeDate', function(ev) {
        if (ev.date.valueOf() > checkout.datepicker("getDate").valueOf() || !checkout.datepicker("getDate").valueOf()) {

            var newDate = new Date(ev.date);
            newDate.setDate(newDate.getDate() + 1);
            checkout.datepicker("update", newDate);

        }
        $('#dp2')[0].focus();
        });


        var checkout = $('#dp2').datepicker({
        beforeShowDay: function(date) {
            if (!checkin.datepicker("getDate").valueOf()) {
            return date.valueOf() >= new Date().valueOf();
            } else {
            return date.valueOf() > checkin.datepicker("getDate").valueOf();
            }
        },
        autoclose: true

        }).on('changeDate', function(ev) {}); */


        jQuery.validator.addMethod("require_from_group2", function (value, element, options) {
    var validator = this;
    var minRequired = options[0];
    var selector = options[1];
    var minimum = options[2];
    var validOrNot = jQuery(selector, element.form).filter(function () {
        //console.log(validator.elementValue(this));
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

    validRooms = validOrNot;

    return validOrNot;
}, jQuery.validator.format("Please fill at least {0} of these fields."));


            //4111 1111 1111 1111
            var tr_form = document.getElementById('tmr_application_form');
             var v = $("#tmr_application_form").validate({
                errorClass: "error",
                rules: {
                    email:{
                        required: true,
                        email: true
                    },
                    firstName:{
                        required: true
                    },
                    lastName:{
                        required: true,
                    },
                    @if ($teamRoomAvailableCount > 1)
                        @foreach( $teamRequest->teamRequestRooms as $teamRequestRoom) 
                        "room-{{$teamRequestRoom->id}}": {
                            require_from_group2: [1, ".roomsInput", 1]
                        },
                        @endforeach
                    @else 
                    "{{$roomNameIfOneOnly}}": {required: true, min: 1},
                   @endif
                },
                @if ($teamRoomAvailableCount > 1)
                    groups: {
                        name: "{{ $trRoomGroup }}"
                    },
                @endif
                submitHandler: function(form) { 
                    alert('valid form.');
                    return false;
                },
                errorPlacement: function(error, element) {
                    //error.appendTo(element.parents (".form-group"));
                    /*console.log(element);
                    error.appendTo(element.parents (".form-group"));*/
                    if ( element.is(".roomsInput") ) 
                    {
                        error.appendTo( element.parents('#rooms-form-group') );
                    }
                    else 
                    { // This is the default behavior of the script
                        //error.insertAfter( element );
                        error.appendTo(element.parents (".form-group"));
                    }
                }
            });

            /*$("#tmr_application_form input").blur(function() {
                v.element($(this)); 
                setTimeout(function() {
                    if ($(this).attr('aria-invalid') == "true") {
                        console.log($(this).attr('name'));
                    }
                }, 1000);
            });*/


            $('input.roomsInput').on('blur keyup', function() {
                @if ($teamRoomAvailableCount == 1)
                    if($('#tmr_application_form input[name="{{$roomNameIfOneOnly}}"]').valid()) {
                        validRooms = true;
                    } else {
                        validRooms = false;
                    }
                @endif
                calculatePrice();
            });

            function calculatePrice() {

                ppnTotal = 0;

                var nights = $('#tmr_application_form input[name="numberOfNights"]').val();
                console.log(nights);

                var totalNumberOfRooms = 0;
                
                if (validRooms) {
                    $('.roomsInput').each(function(i, obj) {
                        //console.log($(this).data('price-per-night'));
                        ppnTotal += $(this).data('price-per-night') * $(this).val();

                        /* if per room per night */

                        totalNumberOfRooms += Number($(this).val());
                    });

                    ppnTotal = ppnTotal * nights;

                    totalTax = ppnTotal * hotelTax;

                    console.log(totalTax);

                    /* if per room per night occupancy fee*/
                    hotelFee = totalNumberOfRooms * flatFeePerNight;
                    hotelFee = hotelFee * nights;

                    //ppnTotal = ppnTotal + hotelFee;

                    /* if not flat fee per room per night, but just per night
                    hotelFee = nights * flatFeePerNight;
                    ppnTotal = hotelFee + ppnTotal;*/
                    

                    

                    //totalCost = totalTax + ppnTotal + flatFeePerNight + siteTransactionFlatFee;
                    
                    var orderSiteTransactionFlatFee = siteTransactionFlatFee * totalNumberOfRooms;

                    //totalCost = totalTax + ppnTotal + siteTransactionFlatFee + hotelFee;
                    
                    
                    totalCost = totalTax + ppnTotal + orderSiteTransactionFlatFee + hotelFee;

                    siteTransactionPercent = totalCost * siteTransactionFeePercent;
                   
                    //siteTransactionFee = siteTransactionFlatFee + siteTransactionPercent;
                    
                    siteTransactionFee = orderSiteTransactionFlatFee + siteTransactionPercent;
                    
                    totalCost += siteTransactionPercent;

                    

                    //$("#priceCon").show('fade');
                } else {
                    ppnTotal = 0;
                    totalTax = 0;
                    totalCost = 0;
                    siteTransactionPercent = 0;
                    siteTransactionFee = 0;
                    hotelFee = 0;

                    //$("#priceCon").hide('fade');
                }

                $("#hotelRoomsPrice").html("$" + ppnTotal.toFixed(2));
                $("#hotelRoomsTax").html("$" + totalTax.toFixed(2));
                //$("#purchasingFee").html("$" + flatFeePerNight.toFixed(2));
                $("#purchasingFee").html("$" + hotelFee.toFixed(2));
                $("#transactionFee").html("$" + siteTransactionFee.toFixed(2));

                $("#totalPrice").html("$" + totalCost.toFixed(2));
            };

            $('input').on('blur keyup', function() {
                console.log($("#tmr_application_form").validate().checkForm());

                //console.log($(this).valid());
                //$(this).valid();
                

                if ($("#tmr_application_form").validate().checkForm() && document.querySelector("#cccvv .CollectJSValid") !== null && document.querySelector("#ccexp .CollectJSValid") !== null && document.querySelector("#ccnumber .CollectJSValid") !== null) {
                    $('#teamRequestFormSubmit').show();  
                } else {
                    $('#teamRequestFormSubmit').hide();
                }
            });

            $('input #agreeCheck').on('change', function() {
                if ($("#tmr_application_form").validate().checkForm() && document.querySelector("#cccvv .CollectJSValid") !== null && document.querySelector("#ccexp .CollectJSValid") !== null && document.querySelector("#ccnumber .CollectJSValid") !== null) {
                    $('#teamRequestFormSubmit').show();  
                } else {
                    $('#teamRequestFormSubmit').hide();
                }
            });
            
           /* tr_form.querySelectorAll('.form-control').forEach(input => {
            input.addEventListener(('input'), () => {
                console.log('hey');
                if (input.checkValidity()) {
                input.classList.remove('is-invalid')
                input.classList.add('is-valid');
                // $("#submitBtn").attr("disabled",false);          
                } else {
                input.classList.remove('is-valid')
                input.classList.add('is-invalid');
                }
                var is_valid = $('.form-control').length === $('.form-control.is-valid').length;
                $("#teamRequestFormSubmit").attr("disabled", !is_valid);
            });
            });*/

            CollectJS.configure({
                'paymentSelector' : '#teamRequestFormSubmit',
                "variant" : "inline",
                "customCss" : {
                    "display": "block",
                    "width": "100%",
                    "height": "calc(1.6em + 0.75rem + 2px)",
                    "padding": "0.375rem 0.75rem",
                    "font-size": "0.9rem",
                    "font-weight": "400",
                    "line-height": "1.6",
                    "color": "#495057",
                    "background-color": "#fff",
                    "background-clip": "padding-box",
                    "border": "1px solid #ced4da",
                    "border-radius": "0.25rem",
                    "transition": "border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out",
                },
                "focusCss": {
                    "color": "#495057",
                    "background-color": "#fff",
                    "border-color": "#a1cbef",
                    "outline": "0",
                    "box-shadow": "0 0 0 0.2rem rgba(52, 144, 220, 0.25)",
                },
                "fields": {
                        "ccnumber": {
                            "selector": "#ccnumber",
                            "title": "Card Number",
                            "placeholder": "0000 0000 0000 0000"
                        },
                        "ccexp": {
                            "selector": "#ccexp",
                            "title": "Card Expiration",
                            "placeholder": "00 / 00"
                        },
                        "cvv": {
                            "selector": "#cccvv",
                            "title": "CVV Code",
                            "placeholder": "***"
                        }
                    },
                    'validationCallback' : function(field, status, message) {
                        
                        if (status) {
                            var message = field + " is now OK: " + message;
                        } else {
                            var message = field + " is now Invalid: " + message;
                        }
                        console.log(message);
                    },
                    "timeoutDuration" : 4000,
                    "timeoutCallback" : function () {
                        console.log("The tokenization didn't respond in the expected timeframe.  This could be due to an invalid or incomplete field or poor connectivity");
                    },
                    "fieldsAvailableCallback" : function () {

                        var frames = document.querySelectorAll(".input-field iframe.CollectJSInlineIframe")
                    for (var i = 0; i < frames.length; i++) {
                        frames[i].addEventListener("focus", function (event) {
                            var panel = event.target.parentNode.parentNode;
                            if (panel.querySelector("label")) {
                                panel.querySelector("label").classList.add("active");
                            }
                        });
                        frames[i].addEventListener("blur", function (event) {
                            var panel = event.target.parentNode.parentNode;
                            if(event.detail && event.detail.empty) {
                                if (panel.querySelector("label")) {
                                    panel.querySelector("label").classList.remove("active");
                                }
                            }

                            setTimeout(function() {

                            
                                if (event.target.id == "CollectJSInlineccnumber") {
                                    if (document.querySelector("#ccnumber .CollectJSValid")) {
                                        //validCardNumber = true;
                                        $("#ccnumberInvalid").hide();
                                        checkValid();
                                    } else {
                                        $("#ccnumberInvalid").show();
                                    }
                                }

                                if (event.target.id == "CollectJSInlineccexp") {
                                    if (document.querySelector("#ccexp .CollectJSValid")) {
                                        //validExpiration = true;
                                        $("#ccexpInvalid").hide();
                                        checkValid();
                                    } else {
                                        $("#ccexpInvalid").show();
                                    }
                                }

                                if(event.target.id == "CollectJSInlinecvv") {
                                    if (document.querySelector("#cccvv .CollectJSValid")) {
                                        //validCvv = true;
                                        $("#cccvvInvalid").hide();
                                        checkValid();
                                    } else {
                                        $("#cccvvInvalid").show();
                                    }
                                }

                            }, 100);
                            //console.log(event.target.id);

                                
                        });
                    }
                        console.log("Collect.js loaded the fields onto the form");

                    },
                    'callback' : function(response) {
                        //alert(response.token);
                        var input = document.createElement("input");
                        input.type = "hidden";
                        input.name = "payment_token";
                        input.value = response.token;
                        var form = document.getElementsByTagName("form")[0];
                        form.appendChild(input);
                        //form.submit();
                        teamRequestSubmitForm();
                    }
                
                });

            function checkValid() {
                setTimeout(function () {
                    if ($("#tmr_application_form").validate().checkForm() && document.querySelector("#cccvv .CollectJSValid") !== null && document.querySelector("#ccexp .CollectJSValid") !== null && document.querySelector("#ccnumber .CollectJSValid") !== null) {
                        $('#teamRequestFormSubmit').show()
                    //alert($("#CollectJSInlineccnumber #ccnumber").val());
                    } else {
                        $('#teamRequestFormSubmit').hide();
                    }
                }, 1000);
            }

                
                //4111 1111 1111 1111

            
            var selectedHotel;
                            
            $("select#hotelSelect").change(function(){
                $(".teamRequestRoomsHotelCon").hide('fade');

                selectedHotel = jQuery(this).children("option:selected").val();
                //console.log(selectedHotel);

                if (selectedHotel != "select...") {
                    $("#hotel-" + selectedHotel).show('fade');
                }
                //alert("You have selected the country - " + selectedHotel);
            });

            $("#teamRequestFormSubmit").click(function(e) {
                e.preventDefault();
                $('#loader').removeClass('display-none');
                $("#tmr_application_form").valid();
            });

            function teamRequestSubmitForm () {
                

                grecaptcha.ready(function() {
                    grecaptcha.execute('6LcJDiccAAAAAGmp2UZzphjAAXIF2mimKqubxkdJ', {action: 'submit'}).then(function(token) {
                        var recaptchaResponse = document.getElementById('recaptchaResponse');
                        recaptchaResponse.value = token;
                    // Add your logic to submit to your backend server here.

                    
                
                        //e.preventDefault();

                        //var teamName = $('#tmr_application_form input[name="teamName"]').val();
                        var teamRequestID = $('#tmr_application_form input[name="trID"]').val();
                        var tournamentID = $('#tmr_application_form input[name="tID"]').val();
                        var hotelID = $('#tmr_application_form input[name="hID"]').val();
                        var contactFirstName = $('#tmr_application_form input[name="firstName"]').val();
                        var contactLastName = $('#tmr_application_form input[name="lastName"]').val();
                        var phoneNumber = $('#tmr_application_form input[name="phone"]').val();
                        var email = $('#tmr_application_form input[name="email"]').val();
                        var address = $('#tmr_application_form input[name="address"]').val();
                        var zip = $('#tmr_application_form input[name="zip"]').val();
                        var city = $('#tmr_application_form input[name="city"]').val();
                        var state = $('#tmr_application_form #state option:selected').val();
                        //var specialRequest = $('#tmr_application_form textarea#specialRequest').val();
                        var numberOfnights = $('#tmr_application_form input[name="numberOfNights"]').val();
                        var checkInDate = $('#tmr_application_form input[name="checkInDate"]').val();
                        var checkOutDate = $('#tmr_application_form input[name="checkOutDate"]').val();
                        
                        var specialRequest = $('#tmr_application_form textarea#specialRequest').val();

                        var payment_token = $('#tmr_application_form input[name="payment_token"').val();

                        var trHotelRoomsData = new Array();

                        //$('#tmr_application_form .teamRequestRooms #hotel-' + selectedHotel + ' input[type=number]').hide();

                        $('#tmr_application_form #rooms-form-group .form-group input[type=number]').each(function(){
                            console.log($(this).val());
                            trHotelRoomsData.push({ 'trRoomID': $(this).data('team-request-room-id'), 'roomsRequested': $(this).val()});

                        });

                        //'specialRequest': specialRequest,
                        //'trHotelRoomData': JSON.stringify(trHotelRoomsData)
                        //'hotelID': selectedHotel,

                        var teamMemberRequestData = {
                            'tok': payment_token,
                            'tournamentID': tournamentID,
                            'teamRequestID': teamRequestID,
                            'hotelID': hotelID,
                            'firstName': contactFirstName,
                            'lastName': contactLastName,
                            'phone': phoneNumber,
                            'email': email,
                            'address': address,
                            'city': city,
                            'state': state,
                            'zip': zip,
                            'pricePerNightTotal': ppnTotal,
                            'hotelFee': hotelFee,
                            'totalTax': totalTax,
                            'totalCost': totalCost,
                            'siteTransactionPercent': siteTransactionPercent,
                            'siteTransactionFee': siteTransactionFee,
                            'checkInDate': checkInDate,
                            'checkOutDate': checkOutDate,
                            'numberOfNights': numberOfnights,
                            'roomsRequested': trHotelRoomsData,
                            'specialRequest': specialRequest
                        };


                        console.log(teamMemberRequestData);
                        
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

                        $.ajax({
                            headers: {
                                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            type: "post",
                            data: teamMemberRequestData,
                            //contentType: 'application/json; charset=utf-8',
                            dataType: 'json',
                            url: "/team-requests/tmr-submit-request",
                            success:function(data){
                                //JSON.parse(data);
                                console.log(data);
                                //console.log(data['responseData']);

                                $("#tmr_application_form").hide('fade');

                                $('#tr_message').html(data['message']);

                                $('#tr_message').show('fade');


                                
                            },
                            error:function(err) {
                                //console.log('asshole');
                            },
                            complete:function(){
                                $('#loader').addClass('display-none');
                            }
                        });
                    });
                });
            }                  
            //});

            
            
        });
        
    });

</script>
@endif
@endsection
