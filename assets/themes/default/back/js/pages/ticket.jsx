import '../../css/pages/ticket.scss';
import React, {Component} from 'react';
import ReactDOM from 'react-dom';
import {Details} from './components/ticket/Details.jsx';
import {Slots} from './components/ticket/Slots.jsx';

let details = document.getElementById("details");
if(details){
    ReactDOM.render(
        <Details prospects={details.dataset.prospects}/>,
        details
    )
}

let slots = document.getElementById("slots");
if(slots){
    ReactDOM.render(
        <Slots dayId={slots.dataset.dayId} slots={slots.dataset.slots} />,
        slots
    )
}