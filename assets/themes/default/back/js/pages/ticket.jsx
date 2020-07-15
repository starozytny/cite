import '../../css/pages/ticket.scss';
import React, {Component} from 'react';
import ReactDOM from 'react-dom';
import {Details} from './components/ticket/Details.jsx';

let details = document.getElementById("details");
if(details){
    ReactDOM.render(
        <Details prospects={details.dataset.prospects}/>,
        details
    )
}