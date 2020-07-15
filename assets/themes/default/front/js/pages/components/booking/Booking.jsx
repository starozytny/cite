import React, {Component} from 'react';
import axios from 'axios/dist/axios';
import Routing from '../../../../../../../../public/bundles/fosjsrouting/js/router.min.js';
import AjaxSend from '../../../components/functions/ajax_classique';
import {StepProspects} from './Prospect';
import {StepResponsable} from './Responsable';
import {StepReview} from './Review';
import {StepTicket} from './Ticket';

export class Booking extends Component {
    constructor(props){
        super(props);

        this.state = {
            day: this.props.day,
            dayId: this.props.dayId,
            dayType: this.props.dayType,
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
            ticket: null
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
        this.setState({classDot: 'active-1', classStart: 'hide', classStep1: 'active'});

        AjaxSend.loader(true);
        let self = this;
        axios({ 
            method: 'post', 
            url: Routing.generate('app_booking_tmp_book_start', { 'id' : this.props.dayId }), 
            data: { prospects: prospects, responsable: data } 
        }).then(function (response) {
            let data = response.data; let code = data.code; AjaxSend.loader(false);
            
            if(code === 1){

                self.setState({ code: 1, messageInfo: data.message, horaire: data.horaire, responsableId: data.responsableId, timer: setInterval(() => self.tick(), 1000)});
                
            }else if(code === 2){ // some already registered

                let newProspects = [];
                prospects.forEach(element => {
                    let newProspect = element;
                    data.duplicated.forEach(duplicate => {
                        if(JSON.stringify(element) === JSON.stringify(duplicate)){
                            duplicate.registered = true
                            newProspect = duplicate;
                        }
                    });
                    newProspects.push(newProspect);
                });
                self.setState({ code: 2, prospects: newProspects, messageInfo: '<div class="alert alert-info">' + data.message + '</div>' });

            }else{
                self.setState({ code: 0, responsableId: data.responsableId, messageInfo: '<div class="alert alert-info">' + data.message + '</div>' })
            }
        });
    }

    /**
     * Fonction go back step
     */
    backToProspects (e) {
        this.setState({classDot: 'active-1', classStep1: 'active', classStep2: ''});
    }
    backToResponsable (e) {
        this.setState({classDot: 'active-2', classStep2: 'active', classStep3: '', timer: clearInterval(this.state.timer)});

        // remove 
        AjaxSend.loader(true);
        let self = this;
        axios({ 
            method: 'post', 
            url: Routing.generate('app_booking_tmp_book_delete', { 'id' : this.props.dayId }), 
            data: { responsable: this.state.responsableId } 
        }).then(function (response) {
            AjaxSend.loader(false);
            self.setState({messageInfo: '', responsableId: null})
        });
    }

    /**
     * Fonction go to step Responsable
     */
    toResponsableStep (data) {
        data = data.filter((thing, index, self) =>
            index === self.findIndex((t) => (
                t.civility === thing.civility && t.firstname === thing.firstname && t.lastname === thing.lastname &&
                t.birthday === thing.birthday
            ))
        )
        this.setState({prospects: data, classDot: 'active-2', classStep1: 'full', classStep2: 'active'});
    }    

    /**
     * Get horaire and pre register prospects + responsable if not place = message + waiting list
     */
    toReviewStep (data) {
        this.setState({responsable: data, classDot: 'active-3', classStep2: 'full', classStep3: 'active', min: 4, second: 60});

        const {prospects} = this.state;

        AjaxSend.loader(true);
        let self = this;
        axios({ 
            method: 'post', 
            url: Routing.generate('app_booking_tmp_book_add', { 'id' : this.props.dayId }), 
            data: { prospects: prospects, responsable: data } 
        }).then(function (response) {
            let data = response.data; let code = data.code; AjaxSend.loader(false);
            
            if(code === 1){

                self.setState({ code: 1, messageInfo: data.message, horaire: data.horaire, responsableId: data.responsableId, timer: setInterval(() => self.tick(), 1000)});
                
            }else if(code === 2){ // some already registered

                let newProspects = [];
                prospects.forEach(element => {
                    let newProspect = element;
                    data.duplicated.forEach(duplicate => {
                        if(JSON.stringify(element) === JSON.stringify(duplicate)){
                            duplicate.registered = true
                            newProspect = duplicate;
                        }
                    });
                    newProspects.push(newProspect);
                });
                self.setState({ code: 2, prospects: newProspects, messageInfo: '<div class="alert alert-info">' + data.message + '</div>' });

            }else{
                self.setState({ code: 0, responsableId: data.responsableId, messageInfo: '<div class="alert alert-info">' + data.message + '</div>' })
            }
        });
    }
    tick(){
        const {min, second} = this.state;
        
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
    }

    toTicketStep () {
        this.setState({ classDot: 'active-4', classStep3: 'full', classStep4: 'active', timer: clearInterval(this.state.timer)});

        const {responsableId} = this.state;
        
        AjaxSend.loader(true);
        let self = this;
        axios({ 
            method: 'post', 
            url: Routing.generate('app_booking_confirmed_book_add', { 'id' : this.props.dayId }), 
            data: { responsable: responsableId } 
        }).then(function (response) {
            let data = response.data; let code = data.code; AjaxSend.loader(false);

            if(code === 1){
                self.setState({ code: 1, finalMessage: data.message, ticket: data.ticket})
            }else{
                self.setState({ code: 0, finalMessage: data.message })
            }
        });
    } 

    render () {
        const {day, days, dayType } = this.props;
        const {classDot, classStart, classStep1, classStep2, classStep3, classStep4, prospects, responsable, 
            horaire, messageInfo, min, second, timeExpired, code, finalMessage, ticket} = this.state;

        return <>
            <section className={"section-infos " + classStart}>
                <Infos day={day} />
                <Starter onClick={this.handleClickStart} days={days}/>
            </section>
            <section className="section-steps">
                <StepDot classDot={classDot} classStep1={classStep1} classStep2={classStep2} classStep3={classStep3} classStep4={classStep4} />
                <div className="steps">
                    <StepProspects classStep={classStep1} dayType={dayType} prospects={prospects} toResponsableStep={this.toResponsableStep}/>
                    <StepResponsable classStep={classStep2} prospects={prospects} onClickPrev={this.backToProspects} toReviewStep={this.toReviewStep} />
                    <StepReview classStep={classStep3} prospects={prospects} responsable={responsable} day={day} messageInfo={messageInfo} onClickPrev={this.backToResponsable} 
                                timeExpired={timeExpired} min={min} second={second} code={code} toTicketStep={this.toTicketStep}/>
                    <StepTicket classStep={classStep4} prospects={prospects} day={day} horaire={horaire} code={code} finalMessage={finalMessage} ticket={ticket}/>
                </div>
            </section> 
        </>
    }
}

function StepDot({classDot, classStep1, classStep2, classStep3, classStep4}) {
    let items = [
        { active: classStep1, text: 'Personnes à inscrire'},
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
                La demande de ticket permet de faire une réservation pour X personnes.
                <br /><br />
                Votre <b>numéro de ticket</b> et l’<b>horaire de passage</b> vous seront envoyés par email.
                <br /><br /><br /><br />
                <b className="txt-danger">Important :</b> Pour toute information concernant le déroulement de cette journée
            </p>
            <p className="informations-complementaire">
                Pour toute information concernant le déroulement de cette journée : 
                <br />
                04 91 39 28 28
            </p>
        </div>
    )
}

function Starter({onClick, days}) {

    let remaining = true;
    let items = JSON.parse(days).map((elem, index) => {
        if(elem.remaining <= 0 ){
            remaining = false;
        }
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

                    <div className="alert alert-info"> A la journée d'inscription veuillez apporter votre <b>avis d'impôt sur le revenu</b> </div>
                    {remaining ? null : <div className="alert"> Il n'y a plus de place pour le moment. Vous serez placez en file d'attente. </div>}
                </div>
                <div className="starter-btn">
                    <button className="btn btn-primary" onClick={onClick}>Réserver un ticket</button>
                </div>
            </div>
        </div>
    )
}