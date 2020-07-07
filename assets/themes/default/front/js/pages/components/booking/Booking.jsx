import React, {Component} from 'react';
import {StepProspects} from './Prospect';

export class Booking extends Component {
    constructor(props){
        super(props);

        this.state = {
            classStart: '',
            classStep1: ''
        }

        this.handleClickStart = this.handleClickStart.bind(this)
    }

    /**
    * Fonction pour commencer le processus de demande de ticket.
    */
    handleClickStart (e) {
        this.setState({classStart: 'hide', classStep1: 'active'})
    }

    render () {

        const {classStart, classStep1} = this.state;

        return <>
            <section className={"section-infos " + classStart}>
                <Infos />
                <Starter onClick={this.handleClickStart}/>
            </section>
            <section className="section-steps">
                <div className="steps">
                    <StepProspects classStep={classStep1}/>
                </div>
            </section>
        </>
    }
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
                    <button className="btn btn-primary" onClick={onClick}>Prendre de ticket</button>
                </div>
            </div>
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