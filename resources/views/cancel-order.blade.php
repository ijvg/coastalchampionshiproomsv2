@extends('layouts.app')

@section('content')



    <div id="homeOuterCon">

        <div class="container firstRowContainer">
        
            <div class="container">
                
                
                @if(Session::has('success'))
                	<div class="alert alert-success">
                		{{Session::get('success')}}
                	</div>
                @endif
                
                <h1 style="text-align: center; margin-top: 40px;">Cancellation Request Form</h1>

                
                <div id="cancellationPolicyInfo">
                    <h4>Cancellation Policy:</h4>

                    <b>Team Cancellations:</b>

                    <p>Team cancellations must be made 30 days or more prior to check in to avoid financial penalty. Financial penalty is subject to individual hotel policy, but frequently consists of the first night’s stay plus applicable taxes and fees for the entire team.</p>

                    

                    <b>Individual Cancellations:</b>

                    <p>
                        Individual cancellations are subject to the cancellation policies of the hotel. All cancellations made in compliance with the hotel’s individual cancellation policy (usually 48 – 72 hours prior to arrival) are refundable.
                    </p>

                    
                    <p>
                        Cancellations made in regard to Covid 19 are handled on a case by case basis and are also subject to hotel policy. Reservations are typically refundable, however hotel may require written confirmation of a positive covid 19 test result.
                    </p>
                    
                </div>    

                <form id="cancellationRequestForm" action="/submit-cancel-request-form" method="POST" class="needs-validation" novalidate>

                    {{ csrf_field() }}

                    <div class="form-gorup">
                        <label class="control-label" for="confirmationNumber">Confirmation Number</label>
                        <input type="text" class="form-control" name="confirmationNumber">
                        @if ($errors->has('confirmationNumber'))
                            <span class="text-danger">{{ $errors->first('confirmationNumber') }}</span>
                        @endif
                    </div>

                    <div class="form-group">
                        <label for="cancellationDescription">Cancellation Reason:</label>
                        <textarea class="form-control" rows="5" name="cancellationDescription" id="cancellationDescription"></textarea>
                        @if ($errors->has('cancellationDescription'))
                            <span class="text-danger">The cancellation reason field is required.</span>
                        @endif
                    </div>

                    <div class="form-group clearfix">

                        <button type="submit" id="cancelOrderFormSubmit" class="btn btn-success btn-submit float-right g-recaptcha"  data-sitekey="6LcJDiccAAAAAGmp2UZzphjAAXIF2mimKqubxkdJ" data-callback='onSubmit' data-action='submit'>Submit</button> 
                    </div>
                        
                </form>
                

        </div>

    </div>

@endsection

@section('scripts')
<script async src="https://www.google.com/recaptcha/api.js" defer></script>

<script defer>
    //document.addEventListener("DOMContentLoaded", function(){

        //$(document).ready(function(){
            function onSubmit(token) {
                
                    document.getElementById("cancellationRequestForm").submit();
                
            }
        //});
    //})
</script>

@endsection