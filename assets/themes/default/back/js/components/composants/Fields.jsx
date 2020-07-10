import React, {Component} from 'react';

export function Input({type, auto="on", identifiant, value, onChange, error, placeholder="", onBlur=null, children}) {
    return (
        <div className={'form-group' + (error ? " form-group-error" : "")}>
            <label htmlFor={identifiant}>{children}</label>
            <input type={type} autoComplete={auto} name={identifiant} id={identifiant} value={value} placeholder={placeholder} onChange={onChange} onBlur={onBlur}/>
            <div className='error'>{error ? error : null}</div>
        </div>
    );
}

export function TextArea({identifiant, value, onChange, error, children}) {
    return (
        <div className={'form-group' + (error ? " form-group-error" : "")}>
            <label htmlFor={identifiant}>{children}</label>
            <textarea name={identifiant} id={identifiant} value={value} onChange={onChange}/>
            <div className='error'>{error ? error : null}</div>
        </div>
    );
}

export function Select({name, id, value, onChange, error, children, items}) {
    let choices = items.map((item) => 
        <option key={item.value} value={item.value}>{item.libelle}</option>
    )
    return (
        <div className={'form-group' + (error ? " form-group-error" : "")}>
            <label>
                {children}
                <select value={value} id={id} name={name} onChange={onChange}>
                    {choices}
                </select>
            </label>
            <div className="error">{error ? error : null}</div>            
        </div>
    );
}