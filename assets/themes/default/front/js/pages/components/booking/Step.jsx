import React, {Component} from 'react';

export function Step({id, classStep, title, body, specialFull=null, onClickNext, onClickPrev, nextText="Suivant", expired=false, code=1, final=false, children}) {

    let classBtn = "btn btn-primary ";
    let disabled = "";
    if(code != 1 || expired != false){
        classBtn += "inactive";
        disabled = "disabled";
    }

    return (
        <div className={"step step-" + id + " " + classStep}>
            <div className="step-title">
                <h2>{title}</h2>
                {children ? <div>{children}</div> : null}
            </div>
            <div className="step-content">
                {body}
            </div>
            {final ? null : <div className={"step-actions " + specialFull}>
                <button className="btn btn-back" onClick={onClickPrev}>Retour</button>
                <button className={classBtn} disabled={disabled} onClick={onClickNext}>{nextText}</button>
            </div>}
            
        </div>
    )
}