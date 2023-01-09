


<table>
    <thead>
    <tr>
        <th>Hotel</th>
        <th>Total</th>
        <th>Room Rates Paid</th>
        <th>Hotel Taxes</th>
        <th>Hotel Flat Fee</th>
        <th>Credit Card Transaction Fee</th>
        <th>Flat Booking Fee</th>
    </tr>
    </thead>
    <tbody>
    @foreach($tournament->hotels as $hotel)
        <tr>
            <td>{{ $hotel->name }}</td>
            <td>{{ $hotel->pivot->getTotalPaid() }}</td>
            <td>{{ $hotel->pivot->getTotalRoomRatesPaid() }}</td>
            <td>{{ $hotel->pivot->getTotalHotelTaxes() }}</td>
            <td>{{ $hotel->pivot->getTotalHotelFlatFee() }}</td>
            <td>{{ $hotel->pivot->getTotalPercentCreditCardFees() }}</td>
            <td>{{ $hotel->pivot->getTotalTransactionFees() }}</td>
        </tr>
        
        <!--<tr>
        </tr>-->
    @endforeach
    </tbody>
</table>