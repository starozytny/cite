import React, {Component} from 'react';
import ReactHtmlParser from 'react-html-parser';
import {Step} from './Step';

function formattedPhone(elem){
    if(elem != "" && elem != undefined){
        let a = elem.substr(0,2);
        let b = elem.substr(2,2);
        let c = elem.substr(4,2);
        let d = elem.substr(6,2);
        let e = elem.substr(8,2);

        elem = a + " " + b + " " + c + " " + d + " " + e;
    }

    return elem;
}

export class StepReview extends Component {

    constructor(props){
        super(props);
    }    

    render () {
        const {classStep, onClickPrev, prospects, responsable, day, messageInfo, min, second, timeExpired, code} = this.props;

        let itemsProspects = prospects.map((elem, index) => {
            return (
                <div className={elem.registered ? 'review-card registered' : 'review-card' } key={index}>
                    <div>{elem.civility}. {elem.lastname} {elem.firstname}</div>
                    <div className="review-card-email">{elem.email}</div>
                    <div className="txt-discret">{(new Date(elem.birthday)).toLocaleDateString('fr-FR')}</div>
                    <div className="txt-discret">{formattedPhone(elem.phoneDomicile)}</div>
                    <div className="txt-discret">{formattedPhone(elem.phoneMobile)}</div>
                    <div className="review-card-registered">Déjà inscrit</div>
                </div>
            )
        })

        let body = <>
            <div className="review">
                <div className="review-prospects">
                    <div className="title">Liste des personnes souhaitant s'inscrire : </div>
                    <div className="review-cards">
                        {itemsProspects}
                    </div>
                </div>

                <div className="review-responsable">
                    <div className="title">Responsable des personnes citées ci-dessus : </div>
                    <div className="review-cards">
                        <div className="review-card">
                            <div>{responsable.civility}. {responsable.lastname} {responsable.firstname}</div>
                            <div className="review-card-email">{responsable.email}</div>
                            <div className="txt-discret">{formattedPhone(responsable.phoneDomicile)}</div>
                            <div className="txt-discret">{formattedPhone(responsable.phoneMobile)}</div>
                        </div>
                    </div>
                </div>
            </div>
        </>

        let nextText =  code == 1 ? (timeExpired ? "Expirée" : "Valider (" + min +"min " + second + "s)") : 'Indisponible';

        return <Step id="3" classStep={classStep} title="Récapitulatif" onClickPrev={onClickPrev} body={body} 
        nextText={nextText} expired={timeExpired} code={code}>
            <div className="text-regular">
                <div>Inscription pour la journée du : <b>{day}</b></div>
                {ReactHtmlParser(messageInfo)}
                { code == 1 ? <div>Cette réservation est sauvegardée pendant {timeExpired ? <b>Expirée</b> : <b>{min}min {second}s</b>}. <br/> <br/>
                                <b className="txt-primary">Veuillez valider la réservation</b> pour obtenir votre ticket et bloquer définitivement cette plage horaire.</div> : null }
            </div>
        </Step>
    }
}