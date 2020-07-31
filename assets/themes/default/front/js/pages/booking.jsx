import '../../css/pages/booking.scss';
import React, {Component} from 'react';
import ReactDOM from 'react-dom';
import {Booking} from './components/booking/Booking.jsx';

let booking = document.getElementById("booking");
ReactDOM.render(
    <Booking cps={booking.dataset.cps} day={booking.dataset.day} dayId={booking.dataset.id} dayType={booking.dataset.type} dayTypeString={booking.dataset.typeString} dayRemaining={booking.dataset.remaining} days={booking.dataset.days} />,
    booking
)