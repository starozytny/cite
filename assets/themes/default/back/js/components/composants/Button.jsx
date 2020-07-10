import React, {Components} from 'react';

function Button({type, children}){
    const className = 'btn btn-' + type;
    return (
        <> <button type="submit" className={className}>{children}</button> </>
    );
}

export function ButtonPrimary({children}) {
    return ( <> <Button type="primary" >{children}</Button> </> );
}