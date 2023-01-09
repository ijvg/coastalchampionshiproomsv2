@extends('layouts.app')

@section('content')

    <div id="homeOuterCon">

        <div class="container firstRowContainer">
            
            @if(Session::has('success'))
            	<div class="alert alert-success">
            		{{Session::get('success')}}
            	</div>
            @endif
                
        
            <h1 style="text-align: center; margin-top: 40px;">Reservation Change Request Form</h1>

                
            <div id="cancellationPolicyInfo">
                <p>All reservation changes must be made through CCR. If you reach out to the hotel directly they will not be able to help you.</p>

 

                <p>All reservation changes are subject to the policy and availability of the hotel reserved and are not guaranteed until confirmed by CCR</p>

                

                <b>team member request information:</b>

                <p>
                    All team reservations are submitted on a request basis and must be approved by CCR pending hotel availability. Please allow 2-3 business days before receiving a response.
                </p>

                
                <p>
                    At the time of request approval a booking link will be sent to your email. The booking link is meant to be distributed to all team members for their individual reservation. All reservations must be submitted before the booking link expiration date. Late reservations will be accepted on the basis of hotel availability but are subject to an increased booking fee ($8).
                </p>
                
                <p>
                    30 days prior to arrival a deposit will be drafted from the card on file consisting of charges for the first night of stay plus applicable taxes and fees. The remaining balance of your reservation will be drafted from the card on file 7 days prior to check in.
                </p>
                
                <p>
                    If you need to cancel,  all charges with the exception of the flat booking fee ($5 for standard reservations, $8 for late reservations) are refundable as long as your cancellation is made in accordance with the individual hotel cancellation policy (typically 48-72 hours before check in). Please check the individual cancellation policy of the hotel you have reserved.
                </p>
                
                <p>
                    Prior to check in, an email will be sent to your team confirming hotel information and providing a hotel confirmation number.
                </p>
                
                <p>
                    At check in, you will be asked for credit card to keep on file for incidentals (parking fees or breakfast fees if they apply, room damages, etc.).
                </p>
                
                <p>
                    If you have any questions please contact Marisa Farley, <a href="tel:757-937-1372">757-937-1372</a> / <a href="mailto:Marisa@ccrooms.com">Marisa@ccrooms.com</a>
                </p>

            </div>    

            <form id="changeOrderRequestForm" action="/submit-change-request-form" method="POST" class="needs-validation" novalidate>

                {{ csrf_field() }}

                <div class="form-gorup">
                    <label class="control-label" for="confirmationNumber">Confirmation Number</label>
                    <input type="text" class="form-control" name="confirmationNumber">
                    @if ($errors->has('confirmationNumber'))
                        <span class="text-danger">{{ $errors->first('confirmationNumber') }}</span>
                    @endif
                </div>

                <div class="form-group">
                    <label for="chnageDescription">Changes Requesting:</label>
                    <textarea class="form-control" rows="5" name="changeDescription" id="changeDescription"></textarea>
                    @if ($errors->has('changeDescription'))
                        <span class="text-danger">The changes requested field is required.</span>
                    @endif
                </div>

                <div class="form-group clearfix">

                    <button type="submit" id="changeOrderFormSubmit" class="btn btn-success btn-submit float-right g-recaptcha"  data-sitekey="6LcJDiccAAAAAGmp2UZzphjAAXIF2mimKqubxkdJ" data-callback='onSubmit' data-action='submit'>Submit</button> 
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
                
                    document.getElementById("changeOrderRequestForm").submit();
                
            }
        //});
    //})
</script>

@endsection