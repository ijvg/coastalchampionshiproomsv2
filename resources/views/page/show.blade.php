@extends('layouts.app')

@section('content')

    <div id="homeOuterCon">

        @if (isset($page->image))
            <div id="homeHeroImageCon">
                <img id="homeHeroImage" src="{{ Voyager::image( $page->image ) }}" style="width: 100%;">
            </div>
        @endif

        <div class="container firstRowContainer">
            
        
            <div class="pageContent card">
                <h1 class="pageHeader">{{ $page->title }}</h1> 

                <div class="card-body">
                    {!! $page->body !!}
                </div>
            </div>

        </div>

    </div>

@endsection