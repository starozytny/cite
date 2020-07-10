import React, {Components} from 'react';
import {Success, Error} from './Alert';
import {ButtonPrimary} from './Button';

export function Formulaire({onSubmit, inputs, success, error, btn, children}) {
    return ( 
        <form onSubmit={onSubmit}> 
            <div>
                <Success elem={success}/>
                <Error elem={error}/>
            </div>
            <div>
                {inputs}
            </div>
            <div>
                {children}
            </div>
            <div>
                <ButtonPrimary>{btn}</ButtonPrimary>
            </div>
        </form> 
    );
}