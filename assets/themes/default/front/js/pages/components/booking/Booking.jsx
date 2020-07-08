import React, {Component} from 'react';
import {StepProspects} from './Prospect';
import {StepResponsable} from './Responsable';
import {StepReview} from './Review';

export class Booking extends Component {
    constructor(props){
        super(props);

        this.state = {
            classDot: '',
            classStart: '',
            classStep1: '',
            classStep2: '',
            classStep3: '',
            prospects: [],
            responsable: ''
        }

        this.handleClickStart = this.handleClickStart.bind(this);

        this.toResponsableStep = this.toResponsableStep.bind(this);
        this.backToProspects = this.backToProspects.bind(this);
        this.toReviewStep = this.toReviewStep.bind(this);
    }

    /**
    * Fonction pour commencer le processus de demande de ticket.
    */
    handleClickStart (e) {
        this.setState({classDot: 'active-1', classStart: 'hide', classStep1: 'active'})
    }

    toResponsableStep (data) {
        data = data.filter((thing, index, self) =>
            index === self.findIndex((t) => (
                t.civility === thing.civility && t.firstname === thing.firstname && t.lastname === thing.lastname &&
                t.birthday === thing.birthday
            ))
        )
        this.setState({prospects: data, classDot: 'active-2', classStep1: 'full', classStep2: 'active'});
    }

    backToProspects (e) {
        this.setState({classDot: 'active-1', classStep1: 'active', classStep2: ''});
    }

    toReviewStep (data) {
        this.setState({responsable: data, classDot: 'active-3', classStep2: 'full', classStep3: 'active'});
    }

    render () {

        const {classDot, classStart, classStep1, classStep2, classStep3, prospects, responsable} = this.state;

        return <>
        
            <section className={"section-infos " + classStart}>
                <Infos />
                <Starter onClick={this.handleClickStart}/>
            </section>
            <section className="section-steps">
                <StepDot classDot={classDot} classStep1={classStep1} classStep2={classStep2} classStep3={classStep3} />
                <div className="steps">
                    <StepProspects classStep={classStep1} toResponsableStep={this.toResponsableStep}/>
                    <StepResponsable classStep={classStep2} prospects={prospects} onClickPrev={this.backToProspects} toReviewStep={this.toReviewStep} />
                    <StepReview classStep={classStep3} prospects={prospects} responsable={responsable} />
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

function Infos() {
    return (
        <div className="informations">
            <h1>Réservation d'un ticket</h1>
            <p className="subtitle">Journée d'inscription du mardi 8 septembre 2020</p>
                    
            <p>
                Pour obtenir votre ticket d’entrée à la journée d’inscription de la Cité de la musique, complétez le formulaire suivant.
                <br /><br />
                Votre ticket et l’heure à laquelle vous devez vous présenter vous seront envoyés par email.
                <br /><br /><br /><br />
                <b className="txt-danger">Important :</b> Compte-tenu du nombre important de demandes, nous ne pouvons délivrer qu’un ticket par famille. Merci pour votre compréhension.
            </p>
            <p className="informations-complementaire">
                Pour toute information concernant le déroulement de cette journée : 
                <br />
                04 91 39 28 28
            </p>
        </div>
    )
}

function Starter({onClick}) {
    return (
        <div className="starter">
            <div className="starter-card">
                <div className="starter-infos">
                    <p>
                        Déroulement : 
                    </p>
                    <ul>
                        <li>Faire sa demande de ticket pour X personnes.</li>
                        <li>Récupérer son ticket et sa plage horaire grâce au mail envoyé.</li>
                        <li>Se rendre à la journée d'inscription à l'horaire indiqué.</li>
                    </ul>
                    <div className="alert alert-danger">
                        A la journée d'inscription veuillez prendre avec vous le document suivant : Avis d'impôts
                    </div>
                </div>
                <div className="starter-btn">
                    <button className="btn btn-primary" onClick={onClick}>Réserver un ticket</button>
                </div>
            </div>
        </div>
    )
}