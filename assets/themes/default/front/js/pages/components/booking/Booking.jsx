import React, {Component} from 'react';
import {StepProspects} from './Prospect';

export class Booking extends Component {
    constructor(props){
        super(props);

        this.state = {
            classDot: '',
            classStart: '',
            classStep1: ''
        }

        this.handleClickStart = this.handleClickStart.bind(this)
    }

    /**
    * Fonction pour commencer le processus de demande de ticket.
    */
    handleClickStart (e) {
        this.setState({classDot: 'active-1', classStart: 'hide', classStep1: 'active'})
    }

    render () {

        const {classDot, classStart, classStep1} = this.state;

        return <>
        
            <section className={"section-infos " + classStart}>
                <Infos />
                <Starter onClick={this.handleClickStart}/>
            </section>
            <section className="section-steps">
                <StepDot classDot={classDot} classStep1={classStep1}/>
                <div className="steps">
                    <StepProspects classStep={classStep1}/>
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
    return (
        <div className={"steps-dot " + classStep1 + " " + classDot}>
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