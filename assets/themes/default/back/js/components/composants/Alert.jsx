import React, {Components} from 'react';

function Alert({type, elem}){
    const className = 'alert-' + type;
    return (
        <> {elem ? <div className={className}>{elem}</div> : null} </>
    );
}

export function Success({elem}) {
    
    return ( <> <Alert type="success" elem={elem} /> </> );
}

export function Error({elem}) {
    return ( <> <Alert type="error" elem={elem} /> </> );
}