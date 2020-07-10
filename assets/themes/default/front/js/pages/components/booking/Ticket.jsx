import React, {Component} from 'react';
import {Step} from './Step';

export class StepTicket extends Component {

    constructor(props){
        super(props);
    }    

    render () {
        const {classStep, day, horaire, prospects, code, finalMessage, ticket} = this.props;

        let body = <>
            <div>{finalMessage}</div>
            <div>
                TICKET : <b>{ticket}</b>
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