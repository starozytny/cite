import '../../css/pages/booking.scss';
import React, {Component} from 'react';
import ReactDOM from 'react-dom';
import {Booking} from './components/booking/Booking.jsx';

let booking = document.getElementById("booking");
ReactDOM.render(
    <Booking day={booking.dataset.day} dayId={booking.dataset.id} />,
    booking
)