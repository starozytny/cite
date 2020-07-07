import React, {Component} from 'react';
import {Step} from './Step';

export class StepResponsable extends Component {
    render () {

        const {classStep} = this.props;

        let body = <div>Ok</div>

        return <Step id="2" classStep={classStep} title="Responsable" body={body}>
            Les informations recueillies à partir de ce formulaire sont transmises au service de la Cité de la musique dans le but 
            de pré-remplir les inscriptions. Plus d'informations sur le traitement de vos données dans notre 
            politique de confidentialité.
        </Step>
    }
}