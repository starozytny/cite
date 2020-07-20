import React, {Component} from 'react';
import {Step} from './Step';

export class StepTicket extends Component {

    constructor(props){
        super(props);
    }    

    render () {
        const {classStep, day, horaire, prospects, code, finalMessage, ticket, barcode, print} = this.props;

        let body = <>
            <div className="final-content">
                <div className="txt-success">{finalMessage}</div>
                <div className="alert alert-info ticket">
                    <div>TICKET : <b>{ticket}</b></div>
                    <div className="ticket-barcode">
                        <img src={ "data:image/png;base64," + barcode } />
                    </div>
                    <div>Pour le {day} à {horaire}.</div>
                    <div className="ticket-download">
                        <a href={print} target="_blank" download>Télécharger le billet</a>
                    </div>
                </div>
                <div className="alert">
                    <b>RAPPEL</b> : Avant de vous présenter, vérifiez que vous avez : 
                    <ul>
                        <li>Photocopie de votre avis d'imposition 2019 sur revenus 2018</li>
                        <li>Un masque</li>
                        <li>Photocopie de la carte étudiante pour les étudiants de moins de 26 ans</li>
                        <li>Moyen de paiement: chèque ou espèce (CB non acceptée)</li>
                    </ul>
                </div>
            </div>
            
        </>

        let textRegular = <>
            <div>Inscription pour la journée du : <b>{day}</b></div>
            <div>Horaire de passage : <b>{horaire}</b></div>
            <div>Nombre de personnes à inscrire : <b>{prospects.length}</b></div>
        </>

        if(code != 1){
            body = <>
                <div className="final-content">
                    <div>{finalMessage}</div>
                    <div className="alert alert-error ticket">
                        Erreur dans la réservation du ticket. Veuillez contacter le support pour plus d'informations.
                        {/* Si une place se libère, vous serez automatiquement prévenu par mail. <br/>
                        Ce mail contiendra votre numéro de ticket et votre horaire de passage. <br/> <br/>
                        <b>Attention !</b> Vous êtes en file d'attente que pour la journée du <b>{day}</b>. Pour les prochaines journées, il faudra 
                        réitérer la demande. */}
                    </div>
                    <div className="alert">
                        <b>RAPPEL</b> : Durant cette journée, veuillez amener votre <b>dernier avis d'imposition</b> afin que l'on puisse procéder à votre inscription.
                    </div>
                </div>
            </>

            textRegular = <div>Journée du : <b>{day}</b></div>
        }

        return <Step id="4" classStep={classStep} title="Ticket" body={body} final="true">
            <div className="text-regular">
                {textRegular}
            </div>
        </Step>
    }
}