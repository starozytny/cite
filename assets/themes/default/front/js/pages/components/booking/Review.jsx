import React, {Component} from 'react';
import ReactHtmlParser from 'react-html-parser';
import {Step} from './Step';

export class StepReview extends Component {

    constructor(props){
        super(props);
    }

    render () {
        const {classStep, onClickPrev, prospects, responsable, day, messageInfo} = this.props;

        let itemsProspects = prospects.map((elem, index) => {
            return (
                <div className={elem.registered ? 'review-card registered' : 'review-card' } key={index}>
                    <div>{elem.civility}. {elem.lastname} {elem.firstname}</div>
                    <div className="review-card-email">{elem.email}</div>
                    <div className="txt-discret">{elem.birthday}</div>
                    <div className="txt-discret">{elem.phoneDomicile}</div>
                    <div className="txt-discret">{elem.phoneMobile}</div>
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
                            <div className="txt-discret">{responsable.phoneDomicile}</div>
                            <div className="txt-discret">{responsable.phoneMobile}</div>
                        </div>
                    </div>
                </div>
            </div>
        </>

        return <Step id="3" classStep={classStep} title="Récapitulatif" onClickPrev={onClickPrev} body={body} nextText="Valider">
            <div className="text-regular">
                Inscription pour la journée du : <b>{day}</b> <br/>
                {ReactHtmlParser(messageInfo)}
            </div>
        </Step>
    }
}