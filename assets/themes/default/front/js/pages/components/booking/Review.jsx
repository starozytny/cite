import React, {Component} from 'react';
import ReactHtmlParser from 'react-html-parser';
import {Step} from './Step';

function formattedPhone(elem){
    if(elem != "" && elem != undefined){
        let arr = elem.match(/[0-9]/g);
        if(arr != null){
            elem = arr.join('');
            if(!(/^((\+)33|0)[1-9](\d{2}){4}$/.test(elem))){
                return "";
            }else{
                let a = elem.substr(0,2);
                let b = elem.substr(2,2);
                let c = elem.substr(4,2);
                let d = elem.substr(6,2);
                let e = elem.substr(8,2);
        
                return a + " " + b + " " + c + " " + d + " " + e;
            }
        }
    }else{
        return "";
    }
}

export class StepReview extends Component {

    constructor(props){
        super(props);
    }    

    render () {
        const {classStep, onClickPrev, onToStep4, prospects, responsable, day, messageInfo, timeExpired, code, onAnnulation} = this.props;

        let itemsProspects = prospects.map((elem, index) => {

            return (
                <div className={elem.registered ? 'review-card registered' : 'review-card' } key={index}>
                    {elem.numAdh != "" ? <div>{elem.numAdh}</div> : null}
                    <div>{elem.civility}. {elem.lastname} {elem.firstname}</div>
                    <div className="review-card-email">{elem.email}</div>
                    <div className="txt-discret">{elem.birthday}</div>
                    <div className="txt-discret">{formattedPhone(elem.phoneDomicile)}</div>
                    <div className="txt-discret">{formattedPhone(elem.phoneMobile)}</div>
                    <div className="review-card-registered">Déjà inscrit</div>
                </div>
            )
        })

        let body = <>
            <div className="review">
                <div className="review-responsable">
                    <div className="title">Responsable : </div>
                    <div className="review-cards">
                        <div className="review-card">
                            <div>{responsable.civility}. {responsable.lastname} {responsable.firstname}</div>
                            <div className="review-card-email">{responsable.email}</div>
                            <div className="txt-discret">{formattedPhone(responsable.phoneDomicile)}</div>
                            <div className="txt-discret">{formattedPhone(responsable.phoneMobile)}</div>
                        </div>
                    </div>
                </div>

                <div className="review-prospects">
                    <div className="title">Liste des personnes souhaitant s'inscrire : </div>
                    <div className="review-cards">
                        {itemsProspects}
                    </div>
                </div>                
            </div>
        </>

        let nextText = "Obtenir mon ticket";
        if(code == 1){
            nextText = (timeExpired ? "Expirée" : nextText);
        }else if(code == 2){
            nextText = "Indisponible"
        }

        return <Step id="3" classStep={classStep} title="Récapitulatif" onClickPrev={onClickPrev} onClickNext={onToStep4} body={body} 
        nextText={nextText} expired={timeExpired} code={code}>
            <div className="text-regular">
                <div>Inscription pour la journée du : <b>{day}</b></div>
                {ReactHtmlParser(messageInfo)}
                { code == 1 ? <div> <br/>Attention ! <b>Si vous fermez ou rafraichissez cette page</b>, vous devrez attendre 5 minutes pour réitérer la demande.</div> : null }
            </div>
            
            <div className="annulation">
                <button className="btn" onClick={onAnnulation}>Annuler la réservation</button>
            </div>
        </Step>
    }
}