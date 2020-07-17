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
            print: '#'
        }

        this.interval = null;
        this.handleClickStart = this.handleClickStart.bind(this);

        this.toResponsableStep = this.toResponsableStep.bind(this);
        this.backToProspects = this.backToProspects.bind(this);
        this.toReviewStep = this.toReviewStep.bind(this);
        this.backToResponsable = this.backToResponsable.bind(this);
        this.toTicketStep = this.toTicketStep.bind(this);
    }

    /**
    * Fonction pour commencer le processus de demande de ticket.
    */
    handleClickStart (e) {
        AjaxSend.loader(true);
        let self = this;
        axios({ 
            method: 'post', 
            url: Routing.generate('app_booking_tmp_book_start', { 'id' : this.props.dayId }),
        }).then(function (response) {
            let data = response.data; let code = data.code; AjaxSend.loader(false);
            if(code === 1){
                self.setState({classDot: 'active-1', classStart: 'hide', classStep1: 'active', 
                               creneauId: data.creneauId, responsableId: data.responsableId,
                               timer: setInterval(() => self.tick(), 1000), min: 4, second: 60})
            }            
        });
    }

    /**
     * Fonction go back step
     */
    backToProspects (e) {
        this.setState({classDot: 'active-1', classStep1: 'active', classStep2: '', min: 4, second: 60});
    }
    backToResponsable (e) {
        this.setState({classDot: 'active-2', classStep2: 'active', classStep3: '', min: 4, second: 60});
    }

    /**
     * Fonction go to step Responsable
     */
    toResponsableStep (data) {
        let dataNoDoublon = data.filter((thing, index, self) =>
            index === self.findIndex((t) => (
                t.civility === thing.civility && t.firstname === thing.firstname && t.lastname === thing.lastname &&
                t.birthday === thing.birthday && t.numAdh === thing.numAdh
            ))
        )

        AjaxSend.loader(true);
        let self = this;
        axios({ 
            method: 'post', 
            url: Routing.generate('app_booking_tmp_book_duplicate', { 'id' : this.props.dayId }), 
            data: { prospects: dataNoDoublon } 
        }).then(function (response) {
            let data = response.data; let code = data.code; AjaxSend.loader(false);
            
            if(code === 1){
                self.setState({prospects: dataNoDoublon, classDot: 'active-2', classStep1: 'full', classStep2: 'active'});                
            }else{
                let newProspects = [];
                dataNoDoublon.forEach(element => {
                    let newProspect = element;
                    data.duplicated.forEach(duplicate => {
                        if(JSON.stringify(element) === JSON.stringify(duplicate)){
                            duplicate.registered = true
                            newProspect = duplicate;
                        }
                    });
                    newProspects.push(newProspect);
                });
                self.setState({ code: 2, prospects: newProspects });
            }
        });
    }    

    /**
     * Get horaire and pre register prospects + responsable if not place = message + waiting list
     */
    toReviewStep (data) {
        this.setState({responsable: data, classDot: 'active-3', classStep2: 'full', classStep3: 'active', min: 4, second: 60});

        const {creneauId} = this.state;

        AjaxSend.loader(true);
        let self = this;
        axios({ 
            method: 'post', 
            url: Routing.generate('app_booking_tmp_book_add', { 'id' : this.props.dayId }), 
            data: {creneauId: creneauId} 
        }).then(function (response) {
            let data = response.data; let code = data.code; AjaxSend.loader(false);
            if(code === 1){
                self.setState({ code: 1, messageInfo: data.message, horaire: data.horaire, timer: setInterval(() => self.tick(), 1000)});
            }else{
                self.setState({ code: 0, messageInfo: '<div class="alert alert-info">' + data.message + '</div>' })
            }
        });
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
            axios({ 
                method: 'post', 
                url: Routing.generate('app_booking_reset_timer', {'responsableId': responsableId})
            }).then(function (response) {
                AjaxSend.loader(false);
                self.setState({ min: 4, second: 60, timeExpired: false });
            });
        }
        
        
    }

    toTicketStep () {
        this.setState({ classDot: 'active-4', classStep3: 'full', classStep4: 'active', timer: clearInterval(this.state.timer), min: 99, second: 99});

        const {prospects, responsable, responsableId, creneauId} = this.state;
        
        AjaxSend.loader(true);
        let self = this;
        axios({ 
            method: 'post', 
            url: Routing.generate('app_booking_confirmed_book_add', { 'id' : this.props.dayId }), 
            data: { prospects: prospects, responsable: responsable, responsableId: responsableId, creneauId: creneauId } 
        }).then(function (response) {
            let data = response.data; let code = data.code; AjaxSend.loader(false);

            if(code === 1){
                self.setState({ code: 1, finalMessage: data.message, ticket: data.ticket, barcode: data.barcode, print: data.print})
            }else{
                self.setState({ code: 0, finalMessage: data.message })
            }
        });
    } 

    render () {
        const {day, days, dayType, dayRemaining} = this.props;
        const {classDot, classStart, classStep1, classStep2, classStep3, classStep4, prospects, responsable, 
            horaire, messageInfo, timeExpired, code, finalMessage, ticket, barcode, print} = this.state;

        return <>
            <section className={"section-infos " + classStart}>
                <Infos day={day} />
                <Starter onClick={this.handleClickStart} days={days} dayRemaining={dayRemaining}/>
            </section>
            <section className="section-steps">
                <StepDot classDot={classDot} classStep1={classStep1} classStep2={classStep2} classStep3={classStep3} classStep4={classStep4} />
                <div className="steps">
                    <StepProspects classStep={classStep1} dayType={dayType} prospects={prospects} toResponsableStep={this.toResponsableStep}/>
                    <StepResponsable classStep={classStep2} prospects={prospects} onClickPrev={this.backToProspects} toReviewStep={this.toReviewStep} />
                    <StepReview classStep={classStep3} prospects={prospects} responsable={responsable} day={day} messageInfo={messageInfo} onClickPrev={this.backToResponsable} 
                                timeExpired={timeExpired} code={code} toTicketStep={this.toTicketStep}/>
                    <StepTicket classStep={classStep4} prospects={prospects} day={day} horaire={horaire} code={code} finalMessage={finalMessage} ticket={ticket} barcode={barcode} print={print}/>
                </div>
            </section> 
        </>
    }
}

function StepDot({classDot, classStep1, classStep2, classStep3, classStep4}) {
    let items = [
        { active: classStep1, text: 'Famille à inscrire'},
        { active: classStep2, text: 'Responsable'},
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

function Infos({day}) {
    return (
        <div className="informations">
            <h1>Réservation d'un ticket</h1>
            <p className="subtitle">Journée d'inscription du {day}</p>
            <p>
                La demande de ticket permet de faire une réservation pour une famille.
                <br />
                Votre <b>numéro de ticket</b> et l’<b>horaire de passage</b> vous seront envoyés par email.
                <br /><br /><br /><br />
                <b className="txt-danger">Important :</b> ????
            </p>
            <p className="informations-complementaire">
                Pour toute information concernant le déroulement de cette journée : 
                <br />
                04 91 39 28 28
            </p>
        </div>
    )
}

function Starter({onClick, days, dayRemaining}) {

    let items = JSON.parse(days).map((elem, index) => {
        return <div key={index} className={elem.isOpen ? 'item active' : 'item'}>
            <span className={"starter-dates-dot starter-dates-dot-" + elem.isOpen}></span>
            <span>{(new Date(Date.parse(elem.day))).toLocaleDateString('fr-FR')}</span>
            <span className="txt-discret">
                 - Journée des {elem.typeString} 
                 <span>{elem.isOpen ? (elem.remaining > 0 ? ' | ouverte aux tickets' : ' | ouverte en liste d\'attente') : null}</span>
            </span>
        </div>
    });

    return (
        <div className="starter">
            <div className="starter-card">
                <div className="starter-infos">
                    <p> Planning des journées d'inscriptions : </p>

                    <div className="starter-dates">{items} </div>

                    <div className="alert alert-info"> A la journée d'inscription, veuillez apporter votre <b>avis d'impôt sur le revenu</b> </div>
                    {dayRemaining ? null : <div className="alert"> Il n'y a plus de place. </div>}
                </div>
                <div className="starter-btn">
                    <button className="btn btn-primary" onClick={onClick}>{dayRemaining > 0 ? "Réserver un ticket" : "COMPLET"}</button>
                </div>
            </div>
        </div>
    )
}