@extends('layouts.app')

@section('content')

    <div id="contactUsOuterCon">

        <div id="contactUsHeroImageCon">
            <img id="contactUsHeroImage" src="/storage/images/vollyBallHome.jpg" style="width: 100%;">
            <p class="imageCredit">
                Photo by <a href="https://unsplash.com/@vincefleming?utm_source=unsplash&utm_medium=referral&utm_content=creditCopyText">Vince Fleming</a> on 
                <a href="https://unsplash.com/s/photos/volleyball?utm_source=unsplash&utm_medium=referral&utm_content=creditCopyText">Unsplash</a>
            </p>
        </div>

        <div class="container firstRowContainer">
        

            <div class="row mt-5 justify-content-center mb-5">
                <div class="col-8 col-sm-12">
                    <div class="card">
                        <div class="card-header" style="background-color: #489b8a;">
                            <h3 class="text-white">Contact Us</h3>
                        </div>
                        <div class="card-body">
                            
                            @if(Session::has('success'))
                            <div class="alert alert-success">
                                {{ Session::get('success') }}
                                @php
                                    Session::forget('success');
                                @endphp
                            </div>
                            @endif
                       
                            <form method="POST" action="{{ route('contact-form.send') }}" id="contactForm">
                      
                                {{ csrf_field() }}
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <strong>Name:</strong>
                                            <input type="text" name="name" class="form-control" placeholder="Name" value="{{ old('name') }}">
                                            @if ($errors->has('name'))
                                                <span class="text-danger">{{ $errors->first('name') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <strong>Email:</strong>
                                            <input type="text" name="email" class="form-control" placeholder="Email" value="{{ old('email') }}">
                                            @if ($errors->has('email'))
                                                <span class="text-danger">{{ $errors->first('email') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <strong>Phone:</strong>
                                            <input type="text" name="phone" class="form-control" placeholder="Phone" value="{{ old('phone') }}">
                                            @if ($errors->has('phone'))
                                                <span class="text-danger">{{ $errors->first('phone') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <strong>Subject:</strong>
                                            <input type="text" name="subject" class="form-control" placeholder="Subject" value="{{ old('subject') }}">
                                            @if ($errors->has('subject'))
                                                <span class="text-danger">{{ $errors->first('subject') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <strong>Message:</strong>
                                            <textarea name="message" rows="3" class="form-control">{{ old('message') }}</textarea>
                                            @if ($errors->has('message'))
                                                <span class="text-danger">{{ $errors->first('message') }}</span>
                                            @endif
                                        </div>  
                                    </div>
                                </div>
                       
                                <div class="form-group text-center">
                                    <button class="btn btn-success btn-lg btn-submit float-right g-recaptcha"  data-sitekey="6LcJDiccAAAAAGmp2UZzphjAAXIF2mimKqubxkdJ" data-callback='onSubmit' data-action='submit'>Send</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>


        </div>

    </div>

@endsection

@section('scripts')
<script async src="https://www.google.com/recaptcha/api.js" defer></script>

<script defer>
    /*document.addEventListener("DOMContentLoaded", function(){
        $(document).ready(function(){*/
            function onSubmit(token) {

                    document.getElementById("contactForm").submit();
                
            }
        /*});
    })*/
</script>

@endsection