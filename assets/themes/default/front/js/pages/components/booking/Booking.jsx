import React, {Component} from 'react';
import axios from 'axios/dist/axios';
import Routing from '../../../../../../../../public/bundles/fosjsrouting/js/router.min.js';
import AjaxSend from '../../../components/functions/ajax_classique';
import {StepProspects} from './Prospect';
import {StepResponsable} from './Responsable';
import {StepReview} from './Review';
import {StepTicket} from './Ticket';
import Swal from 'sweetalert2';

export class Booking extends Component {
    constructor(props){
        super(props);

        this.state = {
            day: this.props.day,
            dayId: this.props.dayId,
            dayType: this.props.dayType,
            cps: JSON.parse(JSON.parse(this.props.cps)),
            creneauId: null,
            classDot: '',
            classStart: '',
            classStep1: '',
            classStep2: '',
            classStep3: '',
            classStep4: '',
            prospects: [],
            responsable: '',
            messageInfo: '', // for review page
            responsableId: null, // pour delete si go back in review page
            code: 0,
            min: 4,
            second: 60,
            timer: null,
            timeExpired: false,
            finalMessage: '',
            ticket: null,
            barcode: null,
            print: '#',
            historyId: null
        }

        this.handleClickStart = this.handleClickStart.bind(this);
        this.handleAnnulation = this.handleAnnulation.bind(this);

        this.handleToStep2 = this.handleToStep2.bind(this);

        this.handleBackStep1 = this.handleBackStep1.bind(this);
        this.handleToStep3 = this.handleToStep3.bind(this);

        this.handleBackStep2 = this.handleBackStep2.bind(this);
        this.handleToStep4 = this.handleToStep4.bind(this);   
        
        this.handleUnload = this.handleUnload.bind(this);
        this.handleConfirmeExit = this.handleConfirmeExit.bind(this);

    }

    handleConfirmeExit (e) {
        e.preventDefault();
        e.returnValue = "En quittant cette page, les modifications apportées ne seront pas sauvegardées.";
    }

    handleUnload () {
        const {responsableId} = this.state;

        var fd = new FormData();
	    fd.append('responsableId', responsableId);
        navigator.sendBeacon(Routing.generate('app_booking_tmp_book_unload', { 'id' : this.props.dayId }), fd);
    }

    tick(){
        const {min, second, responsableId} = this.state;
        
        let oldMin = parseInt(min);
        let oldSecond = parseInt(second);
        let expired = false;
        let nMin = oldMin
        let nSecond = oldSecond - 1;
    
        if(oldMin == 0 && oldSecond == 0){
            nMin = 0; nSecond = 0; expired = true;
        }else{
            if(nSecond < 0){
                nSecond = oldMin > 0 ? 60 : 0;
                nMin = oldMin - 1;       
            }
        }

        this.setState({ second: nSecond, min: nMin, timeExpired: expired });

        if(nMin == 1 && nSecond == 60){
            
            let self = this;
            AjaxSend.loader(false);
            let loader = document.querySelector('#loader');
            console.log(loader)
            axios({ 
                method: 'post', 
                url: Routing.generate('app_booking_reset_timer', {'responsableId': responsableId})
            }).then(function (response) {
                AjaxSend.loader(false);
                self.setState({ min: 2, second: 6, timeExpired: false });
            });
        }
        
        
    }
    /**
    * Fonction pour commencer le processus de demande de ticket.
    */
    handleClickStart (e) {
        let self = this;
        axios({ 
            method: 'post', 
            url: Routing.generate('app_booking_tmp_book_start', { 'id' : this.props.dayId }),
        }).then(function (response) {
            let data = response.data; let code = data.code;
            if(code === 1){
                self.setState({classDot: 'active-1', classStart: 'hide', classStep1: 'active', 
                               creneauId: data.creneauId, responsableId: data.responsableId, historyId: data.historyId,
                               timer: setInterval(() => self.tick(), 1000), min: 4, second: 60});
                let input0 = document.querySelector('.ext-responsable #firstname');
                input0.focus();
                window.scrollTo({ top: 0, behavior: 'smooth' });

                window.addEventListener("beforeunload", self.handleConfirmeExit);
                window.addEventListener('unload', self.handleUnload);
            }            
        });
    }

    handleAnnulation (e) {
        e.preventDefault();

        const {responsableId} = this.state;

        let self = this;
        Swal.fire({
            title: 'Etes-vous sur d\'annuler la réservation?',
            text: "L'action est irréversible.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Oui',
            cancelButtonText: "Non",
            }).then((result) => {
            if (result.value) {
                AjaxSend.loader(true)
                axios({ 
                    method: 'post', 
                    url: Routing.generate('app_booking_tmp_book_cancel', { 'id' : self.props.dayId }), 
                    data: { responsableId: responsableId } 
                }).then(function (response) {
                    let data = response.data;
                    if(data.url !== undefined){
                        window.history.replaceState(null, null, data.url);
                        setTimeout(function () {
                            location.reload()
                        }, 500);
                    }
                });
            }
        })
    }

    handleToStep2 (data) {
        this.setState({responsable: data, classDot: 'active-2', classStep1: 'full', classStep2: 'active', min: 4, second: 60});
        let input0 = document.querySelector('.step-prospect-0 #numAdh-0');
        let input1 = document.querySelector('.step-prospect-0 #firstname-0');
        setTimeout(() => {
            input0 != null ? input0.focus() : input1.focus();
        }, 10);
        listenScroll();
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    handleBackStep1 (e) {
        this.setState({classDot: 'active-1', classStep1: 'active', classStep2: '', min: 4, second: 60});
    }

    handleToStep3 (prospectsNoDoublon) {
        this.setState({prospects: prospectsNoDoublon});

        const {creneauId, historyId, responsable} = this.state;

        AjaxSend.loader(true);
        let self = this;
        axios({ 
            method: 'post', 
            url: Routing.generate('app_booking_tmp_book_duplicate', { 'id' : this.props.dayId }), 
            data: { prospects: prospectsNoDoublon, historyId: historyId, creneauId: creneauId, responsable: responsable} 
        }).then(function (response) {
            let data = response.data; let code = data.code;  AjaxSend.loader(false);
            
            if(code === 1){       
                self.setState({code: 1, classDot: 'active-3', classStep2: 'full', classStep3: 'active', messageInfo: data.message, horaire: data.horaire,  min: 4, second: 60});                
            }else{
                
                let newProspects = [];
                prospectsNoDoublon.forEach(element => {
                    let newProspect = element;
                    data.duplicated.forEach(duplicate => {
                        if(JSON.stringify(element) === JSON.stringify(duplicate)){
                            duplicate.registered = true;
                            newProspect = duplicate;
                        }
                    });
                    newProspects.push(newProspect);
                });
                self.setState({ code: 2, prospects: newProspects, min: 4, second: 60});
            }
        });
    }

    handleBackStep2 (e) {
        this.setState({classDot: 'active-2', classStep2: 'active', classStep3: '', min: 4, second: 60});
    }

    handleToStep4 () {
        this.setState({ classDot: 'active-4', classStep3: 'full', classStep4: 'active', timer: clearInterval(this.state.timer), min: 99, second: 99});

        const {prospects, responsable, responsableId, creneauId} = this.state;
        
        AjaxSend.loader(true);
        let self = this;
        axios({ 
            method: 'post', 
            url: Routing.generate('app_booking_confirmed_book_add', { 'id' : this.props.dayId }), 
            data: { prospects: prospects, responsable: responsable, responsableId: responsableId, creneauId: creneauId, historyId: this.state.historyId } 
        }).then(function (response) {
            let data = response.data; let code = data.code; AjaxSend.loader(false);

            if(code === 1){
                self.setState({ code: 1, finalMessage: data.message, ticket: data.ticket, barcode: data.barcode, print: data.print})
                window.removeEventListener('beforeunload', self.handleConfirmeExit);
                window.removeEventListener('unload', self.handleUnload);
            }else{
                self.setState({ code: 0, finalMessage: data.message })
            }
        });
    } 

    render () {
        const {day, days, dayType, dayRemaining, dayTypeString} = this.props;
        const {classDot, classStart, classStep1, classStep2, classStep3, classStep4, prospects, responsable, 
            horaire, messageInfo, timeExpired, code, finalMessage, ticket, barcode, print, cps} = this.state;

        return <>
            <section className={"section-infos " + classStart}>
                <Infos day={day} dayTypeString={dayTypeString}/>
                <Starter onClick={this.handleClickStart} days={days} dayRemaining={dayRemaining}/>
            </section>
            <section className="section-steps">
                <StepDot classDot={classDot} classStep1={classStep1} classStep2={classStep2} classStep3={classStep3} classStep4={classStep4} />
                <div className="steps">
                    <StepResponsable classStep={classStep1} cps={cps} onClickPrev={this.handleAnnulation} onToStep2={this.handleToStep2} onAnnulation={this.handleAnnulation}/>
                    <StepProspects classStep={classStep2} dayType={dayType} prospects={prospects} onClickPrev={this.handleBackStep1} onStep3={this.handleToStep3} onAnnulation={this.handleAnnulation}/>
                    <StepReview classStep={classStep3} prospects={prospects} responsable={responsable} day={day} messageInfo={messageInfo} onClickPrev={this.handleBackStep2} 
                                timeExpired={timeExpired} code={code} onToStep4={this.handleToStep4} onAnnulation={this.handleAnnulation}/>
                    <StepTicket classStep={classStep4} prospects={prospects} day={day} horaire={horaire} code={code} finalMessage={finalMessage} ticket={ticket} barcode={barcode} print={print}/>
                </div>
            </section> 
        </>
    }
}

function StepDot({classDot, classStep1, classStep2, classStep3, classStep4}) {
    let items = [
        { active: classStep1, text: 'Responsable'},
        { active: classStep2, text: 'Elève(s) à inscrire'},
        { active: classStep3, text: 'Récapitulatif'},
        { active: classStep4, text: 'Ticket'}
    ];
    let liste = items.map((elem, index) => {
        let numero = index + 1;
        return <div className={"item item-" + numero + " " + elem.active } key={index}>
            <div className="circle"></div>
            <span className="numero">{numero}</span>
            <span className="text">{elem.text}</span>
        </div>
    })
    let classActive = "";
    if(classStep1 != undefined || classStep2 != undefined || classStep3 != undefined || classStep4 != undefined){
        if(classDot != ""){
            classActive = "active";
        }
    }
    return (
        <div className={"steps-dot " + classActive + " " + classDot}>
            {liste}
        </div>
    )
}

function Infos({day, dayTypeString}) {
    return (
        <div className="informations">
            <h1>Réservation d'un ticket</h1>
            <p className="subtitle">Journée d'inscription des {dayTypeString} du {day} </p>
            <p>
                La réservation d'un ticket permet d'obtenir 1 ticket par famille. <br/>
                <br />
                Votre <b>ticket</b> et <b>horaire de rendez-vous</b> vous seront envoyés par email. <br/>
                Veuillez à vérifier vos spams/courriers indésirables.
                <br /><br /><br />
                <b className="txt-danger">Important :</b> Pour des raisons sanitaires, nous vous invitons à limiter le nombre d'accompagnants
                 et tout particulièrement les petits enfants. Le port du masque est obligatoire
            </p>
            <p className="informations-complementaire">
                Pour toutes informations concernant le déroulement de cette journée : 
                <br />
                04 91 39 28 28
            </p>
        </div>
    )
}

function Starter({onClick, days, dayRemaining}) {

    let items = JSON.parse(days).map((elem, index) => {
        if(elem.isOpen){
            return <div key={index} className={elem.isOpen ? 'item active' : 'item'}>
            <span className={"starter-dates-dot starter-dates-dot-" + elem.isOpen}></span>
            <span> {elem.fullDateString} </span>
            <span className="txt-discret">
                 - Journée des {elem.typeString}
            </span>
        </div>
        }else{
            return null;
        }
        
    });

    return (
        <div className="starter">
            <div className="starter-card">
                <div className="starter-infos">
                    <p> Réservation pour le : </p>

                    <div className="starter-dates">{items} </div>

                    <div className="alert alert-info">
                        <b>A apporter</b> à la journée d'inscriptions : 
                        <ul>
                            <li>Photocopie de votre avis d'imposition 2019 sur revenus 2018</li>
                            <li>Un masque</li>
                            <li>Photocopie de la carte étudiante pour les étudiants de moins de 26 ans</li>
                            <li>Moyen de paiement: chèque ou espèces (CB non acceptée)</li>
                        </ul>
                    </div>
                    {dayRemaining ? null : <div className="alert"> Il n'y a plus de place. </div>}
                </div>
                <div className="starter-btn">
                    <button className="btn btn-primary" onClick={dayRemaining > 0 ? onClick : null}>{dayRemaining > 0 ? "Réserver un ticket" : "COMPLET"}</button>
                </div>
            </div>
        </div>
    )
}

function listenScroll(){
    setTimeout(() => {
        var position_scroll = 0;

        let actions = document.querySelector('.step-2 .step-actions-static ');
    
        window.addEventListener('scroll', function(e) {
            position_scroll = window.scrollY != undefined ? window.scrollY : window.pageYOffset;
            let ancre = document.querySelector('.step-prospects-add-anchor');

            if(position_scroll >= ancre.offsetTop){
                actions.classList.remove('fixe')
            }else{
                actions.classList.add('fixe')
            }
        });
    }, 500);
}