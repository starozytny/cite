import React, {Component} from 'react';
import {Step} from './Step';

export class StepReview extends Component {

    constructor(props){
        super(props);
    }
    render () {
        const {classStep} = this.props;

        let body = <>
            <div>Hello recap</div>
        </>

        return <Step id="3" classStep={classStep} title="Récapitulatif" body={body}></Step>
    }
}