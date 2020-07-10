import React, {Component} from 'react';
import {Step} from './Step';

export class StepTicket extends Component {

    constructor(props){
        super(props);
    }    

    render () {
        const {classStep, day, horaire, prospects, code, finalMessage, ticket} = this.props;

        let body = <>
            <div className="final-content">
                <div>{finalMessage}</div>
                <div className="alert alert-info ticket">
                    <div>
                        TICKET : <b>{ticket}</b>
                    </div>
                    <div>
                        Pour le {day} à {horaire}.
                    </div>
                </div>
                <div className="alert">
                    <b>RAPPEL</b> : Durant cette journée, veuillez amener votre <b>dernier avis d'imposition</b> afin que l'on puisse procéder à votre inscription.
                </div>
            </div>
            
        </>

        let textRegular = <>
            <div>Inscription pour la journée du : <b>{day}</b></div>
            <div>
                Horaire de passage : <b>{horaire}</b>
            </div>
            <div>
                Nombre de personnes à inscrire : <b>{prospects.length}</b>
            </div>
        </>

        if(code != 1){
            body = <>
                <div>{finalMessage}</div>
            </>

            textRegular = null
        }

        return <Step id="4" classStep={classStep} title="Ticket" body={body} final="true">
            <div className="text-regular">
                {textRegular}
            </div>
        </Step>
    }
}