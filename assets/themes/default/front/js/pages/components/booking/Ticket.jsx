import React, {Component} from 'react';
import {Step} from './Step';

export class StepTicket extends Component {

    constructor(props){
        super(props);
    }    

    render () {
        const {classStep, day} = this.props;

        let body = <>
            <div>Ticket</div>
        </>

        return <Step id="4" classStep={classStep} title="Ticket" body={body} final="true">
            <div className="text-regular">
                <div>Inscription pour la journée du : <b>{day}</b></div>
            </div>
        </Step>
    }
}