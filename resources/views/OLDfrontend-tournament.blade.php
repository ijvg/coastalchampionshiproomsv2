@extends('layouts.app')

@section('content')


        <div id="homeOuterCon">

            <div id="homeHeroImageCon">
                <img id="homeHeroImage" src="{{ Voyager::image( $tournament->image ) }}" style="width: 100%;">
                <p class="imageCredit">
                    {!! $tournament->image_credit !!}
                </p>
            </div>

            <div class="container">
                <h1 id="tournamentTitle">{{ $tournament->name }}</h1>


                <div id="tournamentBodyCon">
                    {!! $tournament->description !!}
                </div>


                <div id="tournamentBtnsCon">
                    <a class="btn btn-lg btn-success tournamentHotelApplicationBtn" href="/tournament/{{ $tournament->slug }}/tournament-application-form">Apply Now</a>

                    <a style="color: white;" class="btn btn-lg tournamentCancelRequestFormBtn" href="/cancel-request-form">Cancellaction Request</a>

                    <a style="color: white;" class="btn btn-lg btn-info tournamentChangeRequestFormBtn" href="/change-request-form">Change Reservation Request</a>
                </div>

                <div id="tournamentHotelsSection">

                    @foreach ($tournament->hotels as $hotel)

                    <div class="card tournamentHotelCard">
                        <div class="card-header">
                            <h4 class="hotelName">{{ $hotel->name }}</h4>
                        </div>

                        <div class="card-body">
                            <div class="hotelAddress">{{ $hotel->address }}</div>

                            <div class="hotelImageCon">
                                <img src="{{ Voyager::image( $hotel->image ) }}" class="hotelImage" />
                            </div>

                            <div class="hotelDescriptionCon">
	                            @if (!is_null($hotel->pivot->min_nights_stay))
	                            	<h5 style="margin-bottom: 25px;">Minimum Number of Nights: {{ $hotel->pivot->min_nights_stay }}</h5>
	                            @endif
	                            
	                            @foreach ($hotel->pivot->hotelTournamentRooms() as $hotelTournamentRoom)
                                    
                                    
                                    <h5 style="margin-bottom: 25px;">{{ $hotelTournamentRoom->type }} Price Per night - {{ $hotelTournamentRoom->gross_price_per_night }}</h5>
                                    
	                            @endforeach
	                            
	                            @if (!is_null($hotel->pivot->cancellation_policy))
	                                <div class="cancellationPolicyCon" width="100%">
	                                	<h4 class="tournamentCancellationPolicy">Cancellation Policy</h4>
	                                
	                                
										{!! $hotel->pivot->cancellation_policy !!}
	                                </div>
	                                
	                                <hr>
	                                
                                @endif
	                            
                                {!! $hotel->description !!}
                                
                                
                                @if (!is_null($hotel->pivot->amenities))
	                                <div class="amenitiesCon" width="100%">
	                                	<h4 class="tournamentAmenities">Amenities</h4>
	                                
	                                
										{!! $hotel->pivot->amenities !!}
	                                </div>
                                @endif
                                
                            </div>

                            @if (isset($hotel->body_images))

                                <div id="carouselControls{{$hotel->slug}}" class="carousel slide" data-ride="carousel" data-interval="false">
                                    <div class="carousel-inner">
                                {{-- @foreach ($hotel->body_images as $hotelImage)
                                    <div class="carousel-item active">
                                        <img class="d-block w-100" src="{{ Voyager::image( $hotelImage ) }}">
                                    </div>
                                    @foreach --}}
                                    @php $count = 0; @endphp
                                    <?php foreach (json_decode($hotel->body_images) as $hotelImage) { ?>

                                        <div class="carousel-item {{ $count ==  0 ? 'active' : ''  }}">
                                            <img class="d-block w-100" src="{{ Voyager::image( $hotelImage ) }}" alt="{{ $hotel->slug }}-{{$loop->index + 1}}">
                                        </div>

                                    <?php $count++; } ?>
                                    
                                    </div>
                                    <a class="carousel-control-prev" href="#carouselControls{{$hotel->slug}}" role="button" data-slide="prev">
                                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                    <span class="sr-only">Previous</span>
                                    </a>
                                    <a class="carousel-control-next" href="#carouselControls{{$hotel->slug}}" role="button" data-slide="next">
                                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                    <span class="sr-only">Next</span>
                                    </a>
                                </div>
                              @endif
                        </div>

                    </div>

                    @endforeach

                </div>
            </div>
        </div>


    

@endsection

@section('scripts')

<script defer>

document.addEventListener("DOMContentLoaded", function(){
        $(document).ready(function(){

            $('.carousel').carousel();

        });
});

</script>

@endsection