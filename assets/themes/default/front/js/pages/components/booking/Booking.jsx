import React, {Component} from 'react';
import axios from 'axios/dist/axios';
import Routing from '../../../../../../../../public/bundles/fosjsrouting/js/router.min.js';
import AjaxSend from '../../../components/functions/ajax_classique';
import {StepProspects} from './Prospect';
import {StepResponsable} from './Responsable';
import {StepReview} from './Review';

export class Booking extends Component {
    constructor(props){
        super(props);

        this.state = {
            day: this.props.day,
            dayId: this.props.id,
            classDot: '',
            classStart: '',
            classStep1: '',
            classStep2: '',
            classStep3: '',
            prospects: [],
            responsable: '',
            messageInfo: '', // for review page
            responsableId: null // pour delete si go back in review page
        }

        this.handleClickStart = this.handleClickStart.bind(this);

        this.toResponsableStep = this.toResponsableStep.bind(this);
        this.backToProspects = this.backToProspects.bind(this);
        this.toReviewStep = this.toReviewStep.bind(this);
        this.backToResponsable = this.backToResponsable.bind(this);
    }

    /**
    * Fonction pour commencer le processus de demande de ticket.
    */
    handleClickStart (e) {
        this.setState({classDot: 'active-1', classStart: 'hide', classStep1: 'active'})
    }

    /**
     * Fonction go back step
     */
    backToProspects (e) {
        this.setState({classDot: 'active-1', classStep1: 'active', classStep2: ''});
    }
    backToResponsable (e) {
        this.setState({classDot: 'active-2', classStep2: 'active', classStep3: ''});

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
        this.setState({responsable: data, classDot: 'active-3', classStep2: 'full', classStep3: 'active'});

        AjaxSend.loader(true);
        let self = this;
        axios({ 
            method: 'post', 
            url: Routing.generate('app_booking_tmp_book_add', { 'id' : this.props.dayId }), 
            data: { prospects: this.state.prospects, responsable: data } 
        }).then(function (response) {
            let data = response.data; let code = data.code; AjaxSend.loader(false);
            if(code === 1){
                self.setState({messageInfo: data.message, responsableId: data.responsableId});
            }else if(code === 2){
                self.setState({messageInfo: '<div class="alert alert-info">' + data.message + '</div>'})
            }else{
                self.setState({messageInfo: data.message})
            }
        });
    }

    

    render () {
        const {day, days} = this.props;
        const {classDot, classStart, classStep1, classStep2, classStep3, prospects, responsable, messageInfo} = this.state;

        return <>
        
            <section className={"section-infos " + classStart}>
                <Infos day={day} />
                <Starter onClick={this.handleClickStart} days={days}/>
            </section>
            <section className="section-steps">
                <StepDot classDot={classDot} classStep1={classStep1} classStep2={classStep2} classStep3={classStep3} />
                <div className="steps">
                    <StepProspects classStep={classStep1} toResponsableStep={this.toResponsableStep}/>
                    <StepResponsable classStep={classStep2} prospects={prospects} onClickPrev={this.backToProspects} toReviewStep={this.toReviewStep} />
                    <StepReview classStep={classStep3} prospects={prospects} responsable={responsable} day={day} messageInfo={messageInfo} onClickPrev={this.backToResponsable}/>
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
        return <div className={"item " + elem.active } key={index}>
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

    let items = JSON.parse(days).map((elem, index) => {
        return <div key={index} className={elem.isOpen ? 'item active' : 'item'}>
            <span className={"starter-dates-dot starter-dates-dot-" + elem.isOpen}></span>
            <span>{(new Date(Date.parse(elem.day))).toLocaleDateString('fr-FR')}</span>
            <span className="txt-discret"> - Journée des {elem.typeString} <span>{elem.isOpen ? ' | ouverte aux tickets' : null}</span></span>
        </div>
    });

    return (
        <div className="starter">
            <div className="starter-card">
                <div className="starter-infos">

                    <p>
                        Planning des journées d'inscriptions :
                    </p>

                    <div className="starter-dates">
                        {items}
                    </div>

                    <div className="alert alert-info">
                        A la journée d'inscription veuillez apporter votre <b>avis d'impôt sur le revenu</b>
                    </div>
                </div>
                <div className="starter-btn">
                    <button className="btn btn-primary" onClick={onClick}>Réserver un ticket</button>
                </div>
            </div>
        </div>
    )
}