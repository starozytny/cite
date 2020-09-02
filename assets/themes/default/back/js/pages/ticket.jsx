import '../../css/pages/ticket.scss';
import React, {Component} from 'react';
import ReactDOM from 'react-dom';
import axios from 'axios/dist/axios';
import AjaxSend from '../components/functions/ajax_classique';
import {Details} from './components/ticket/Details.jsx';
import {Slots} from './components/ticket/Slots.jsx';
import {Ticket} from './components/ticket/Ticket.jsx';
import {ToolbarTicket} from './components/ticket/ToolbarTicket.jsx';
import {EditResponsable, ResendTicket} from './components/ticket/Responsable.jsx';
import Swal from 'sweetalert2';

let details = document.getElementById("details");
if(details){
    ReactDOM.render(
        <Details responsables={details.dataset.responsables} dayId={details.dataset.dayId} />,
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

let ouvertureAncien = document.getElementById('toolbar-ancien');
if(ouvertureAncien){
    ReactDOM.render(
        <Ticket type={ouvertureAncien.dataset.type} dateOpen={ouvertureAncien.dataset.dateOpen} id={ouvertureAncien.dataset.id} />,
        ouvertureAncien
    )
}

let ouvertureNouveau = document.getElementById('toolbar-nouveau');
if(ouvertureNouveau){
    ReactDOM.render(
        <Ticket type={ouvertureNouveau.dataset.type} dateOpen={ouvertureNouveau.dataset.dateOpen} id={ouvertureNouveau.dataset.id} />,
        ouvertureNouveau
    )
}

let editResp = document.getElementById('editResp');
if(editResp){
    ReactDOM.render(
        <EditResponsable cps={editResp.dataset.cps} resp={editResp.dataset.resp}/>,
        editResp
    )
}

let resendTicket = document.getElementById('resendTicket');
if(resendTicket){
    ReactDOM.render(
        <ResendTicket responsableId={resendTicket.dataset.id}/>,
        resendTicket
    )
}

let toolbarCommun = document.getElementById('toolbar-commun');
if(toolbarCommun){
    ReactDOM.render(
        <ToolbarTicket />,
        toolbarCommun
    )
}

let deletes = document.querySelectorAll('.btn-delete');
if(deletes){
    deletes.forEach((elem) => {
        elem.addEventListener('click', function(e){
            e.preventDefault();
            let url = e.currentTarget.href
            Swal.fire({
                title: 'Etes-vous sur de vouloir supprimer cette journée ?',
                text: "Cette action est irréversible.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Confirmer',
                cancelButtonText: 'Annuler'
              }).then((result) => {
                if (result.value) {
                    AjaxSend.loader(true);
                    axios({ 
                        method: 'post', 
                        url: url,
                    }).then(function (response) {
                        location.reload()
                    });
                }
              })
        })
    })
}