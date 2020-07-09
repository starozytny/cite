import React, {Component} from 'react';

export function Step({id, classStep, title, body, specialFull=null, onClickNext, onClickPrev, nextText="Suivant", expired=false, children}) {
    return (
        <div className={"step step-" + id + " " + classStep}>
            <div className="step-title">
                <h2>{title}</h2>
                {children ? <div>{children}</div> : null}
            </div>
            <div className="step-content">
                {body}
            </div>
            <div className={"step-actions " + specialFull}>
                <button className="btn btn-back" onClick={onClickPrev}>Retour</button>
                <button className="btn btn-primary" onClick={onClickNext}>{nextText}</button>
            </div>
        </div>
    )
}