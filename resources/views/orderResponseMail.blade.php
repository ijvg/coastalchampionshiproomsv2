<h2>Championship City Rooms</h2> <br><br>

Thank you for placing your order.

<br>
<br>

Your Confirmation Number is: {{ $confirmationNumber }}

<br>
<br>

Your order information is broken down below.

<br>
<br>

You have ordered the following rooms at the {{ $hotelName }} Hotel

<br>
<br>

@php 

foreach ($orderRooms as $orderRoom) {

    echo $orderRoom->quantity . " of the " . $orderRoom->type;

    echo "<br>";

    echo "For " . $orderRoom->number_of_nights . " nights";

    echo "<br>";
    echo "<br>";
}

@endphp

<b>Check-in: {{ $checkInDate }}</b>

<br>
<br>

<b>Check-out: {{ $checkOutDate }}</b>

<br>
<br>

<b>You will be charged the first night plus fees 30 days prior to check-in and the remaining balance 7 days prior to check-in.</b>


<br>
<br>

<b>Rooms Total:</b> $<?php echo number_format($roomsTotal, 2); ?>

<br>
<br>

<b>State & Local Tax:</b> $<?php echo number_format($stateAndLocalTax, 2); ?>

<br>
<br>

<b>Occupancy Tax:</b> $<?php echo number_format($occupancyTax, 2); ?>

<br>
<br>

<b>Transaction Fee:</b> $<?php echo $transactionFee; ?>

<br>
<br>

<b>Total:</b> $<?php echo number_format($total, 2) ?> 