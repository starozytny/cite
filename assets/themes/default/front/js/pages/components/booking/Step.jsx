import React, {Component} from 'react';

export function Step({id, classStep, title, body, specialFull=null, onClickNext, onClickPrev, children}) {
    return (
        <div className={"step step-" + id + " " + classStep}>
            <div className="step-title">
                <h2>{title}</h2>
                {children ? <p>{children}</p> : null}
            </div>
            <div className="step-content">
                {body}
            </div>
            <div className={"step-actions " + specialFull}>
                <button className="btn btn-back" onClick={onClickPrev}>Retour</button>
                <button className="btn btn-primary" onClick={onClickNext}>Suivant</button>
            </div>
        </div>
    )
}