@extends('layouts.app')

@section('content')

<div id="homeOuterCon">

    <div id="homeHeroImageCon">
        <img id="homeHeroImage" src="/storage/images/vollyBallHome.jpg" style="width: 100%;">
        <p class="imageCredit">
            Photo by <a
                href="https://unsplash.com/@vincefleming?utm_source=unsplash&utm_medium=referral&utm_content=creditCopyText">Vince
                Fleming</a> on <a
                href="https://unsplash.com/s/photos/volleyball?utm_source=unsplash&utm_medium=referral&utm_content=creditCopyText">Unsplash</a>
        </p>
    </div>

    <div class="container firstRowContainer">
        <div class="row homeFirstRow homeRow">
            <div class="col col-8 col-sm-12">
                <h3 class="homeSectionHeader firstSectionHeader">
                    Why Book with Championship City Rooms?
                </h3>
                <div>
                    <p>
                        Booking with CCR means your travel details are taken care of so you can concentrate on winning.
                        We’ll work hard to negotiate the best rate for you and your team. It even says so in our
                        contracts with the hotels we work with; CCR is guaranteed to have the best rate out of any rates
                        the hotel advertises online.
                    </p>

                    <p>
                        In addition to a good deal we can also guarantee that you will receive excellent customer
                        service from the time your team requests a group block all the way until check in. We will
                        communicate all the details and changes to the hotel for you so all you have to do is check in
                        and play.
                    </p>
                </div>

                <h3 class="homeSectionHeader firstSectionHeader">
                    Meet Heather!
                </h3>

                <div>
                    <p>Heather Desormeaux has joined CCR as the Executive Director. Heather has been in the hospitality
                        business her entire career in many roles, most recently as a general manager. She has lived in
                        this area her entire life and is a native of Norfolk.</p>

                    <p>She has 4 children who played in team sports in their youth and is very familiar with the
                        tournament arena and the needs. She also has 5 grandchildren that are her joy.</p>

                    <p>In her spare time, she shoots pool billiards with a women’s league as well as scotch doubles.</p>

                    <p>Charities are important to her, and she is passionate about EnJewel to help fight against human
                        trafficking and the Love & Caring Ministries to help homeless women and children.</p>

                    <p>She looks forward to working with tournament directors and coaches to ensure great success and
                        outcomes for all!</p>
                </div>





            </div>
            <div class="col col-4 col-sm-12">

                <h4 class="homeSectionHeader firstSectionHeader">
                    Do you have any questions or need any assistance?
                </h4>

                <div>
                    <p>
                        We’re here to help. Click the button below to fill out a contact form. Want to speak to a real
                        person? Give us a call at <a href="tel:757-937-1372">757-937-1372</a>.
                        No automated machines or long hold times, if someone isn’t available to answer right away we
                        will give you a call back as soon as possible.
                    </p>
                </div>

                <div id="homeContactUsBtnCon" style="width: 100%; text-align: center;">

                    <a href="/contact-us" class="btn btn-lg btn-blue">Contact us</a>

                </div>

            </div>
        </div>

    </div>

    <div class="secondRowOuterContainer">

        <div class="container secondRowContainer">

            <div class="row homeSecondRow homeRow">
                <div class="col-12 col-sm-12">

                    <h2 class="homeSectionHeader secondSectionHeader">Let's Get Started!</h2>

                    <div class="row letsGetStartedRow no-gutters">
                        <div class="col col-4 col-sm-12">

                            <div class="getStartedCard card">

                                <div class="card-body">

                                    <div class="getStartedCardNumber">
                                        <div class="getStartedCardNumberInnerRing">
                                            <h4>1</h4>
                                        </div>
                                    </div>

                                    <div class="getStartedCardHeader">
                                        Find your event
                                    </div>

                                    <div class="getStartedCardContent">
                                        At the top of the page, click on “sport” and select the type of sporting event
                                        you’re attending. From there, select the name of the tournament
                                    </div>

                                </div>

                            </div>

                        </div>

                        <div class="col col-4 col-sm-12">

                            <div class="getStartedCard card">

                                <div class="card-body">

                                    <div class="getStartedCardNumber">
                                        <div class="getStartedCardNumberInnerRing">
                                            <h4>2</h4>
                                        </div>
                                    </div>

                                    <div class="getStartedCardHeader">
                                        Check out the Event information page
                                    </div>

                                    <div class="getStartedCardContent">
                                        Here you’ll find some general event information like location, dates, and some
                                        deadlines for when you’ll need to make your reservation by
                                    </div>

                                </div>

                            </div>

                        </div>

                        <div class="col col-4 col-sm-12">

                            <div class="getStartedCard card">

                                <div class="card-body">

                                    <div class="getStartedCardNumber">
                                        <div class="getStartedCardNumberInnerRing">
                                            <h4>3</h4>
                                        </div>
                                    </div>

                                    <div class="getStartedCardHeader">
                                        Get ready to search hotels and book!
                                    </div>

                                    <div class="getStartedCardContent">
                                        <p>
                                            When you’re ready to look for hotels, click the link on the event page to
                                            “Book Now” to enter our reservation system. Here you’ll find discounted
                                            hotels available for your event. You
                                        </p>

                                        <p>
                                            You can browse and make a reservation for yourself or request a block for
                                            your whole team.
                                        </p>
                                    </div>

                                </div>

                            </div>

                        </div>
                    </div>

                </div>
            </div>

        </div>

    </div>

<!--
    <div class="container thirdRowContainer">

        <div class="row thirdRow homeRow">
            <div class="col col-12">

                <h3 class="homeSectionHeader firstSectionHeader">
                    Need to adjust your reservation?
                </h3>


                <div>
                    <p>
                        Please use the links below to change or cancel your reservation. Please note all changes or
                        cancellations are requests and must coincide with the policies of the hotel you have booked to
                        be approved.
                    </p>

                    <p>
                        If you have any questions please reach out on the contact form below.
                    </p>

                </div>

            </div>

            <div class="col col-4 col-sm-12 thirdRowBtnCol">
                <a class="btn btn-lg btn-block btn-blue" href="/contact-us">Contact Us</a>
            </div>

            <div class="col col-4 col-sm-12 thirdRowBtnCol">
                <a class="btn btn-lg btn-block btn-blue" href="/change-request-form">CHANGE MY RESERVATION</a>
            </div>

            <div class="col col-4 col-sm-12 thirdRowBtnCol">
                <a class="btn btn-lg btn-block btn-blue" href="/cancel-request-form">REQUEST A CANCELLATION</a>
            </div>
        </div>
-->

        <!--<div class="card" style="width: 18rem;">
                <div class="card-body">
                    <h5 class="card-title">Card title</h5>
                    <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card's content.</p>
                    <a href="#" class="btn btn-primary">Go somewhere</a>
                </div>
            </div>-->


    </div>

</div>

@endsection