import React, {Components} from 'react';
import {Input} from '../../../components/composants/Fields';
import {Formulaire} from '../../../components/composants/Formulaire';
import Validateur from '../../../components/functions/validate_input';
import AjaxSend from '../../../components/functions/ajax_classique';

class FormReinit extends React.Component {
    constructor(props) {
        super(props);

        this.state = {
            success: '',
            error: '',
            password: { value: '', error: '' },
            password2: { value: '', error: '' }
        }

        this.handleChange = this.handleChange.bind(this);
        this.handleSubmit = this.handleSubmit.bind(this);
    }

    handleChange(e){
        const name = e.target.name;
        const value = e.target.value;
        this.setState({
            success: '',
            [name]: {value: value}
        });
    } 

    handleSubmit(e){
        e.preventDefault();

        //Validation
        let validate = Validateur.validateur([
            {type: "text", id: 'password', value: this.state.password.value},
            {type: "text", id: 'password2', value: this.state.password2.value}
        ]);

        //Display error if validate != true else call Ajax password lost
        if(!validate.code){
            this.setState(validate.errors);
        }else{
            AjaxSend.sendAjax(this, this.props.url, this.state, {
                password: { value: '', error: '' },
                password2: { value: '', error: '' }
            });
        }
    }

    render() {
        const {password, password2, success, error} = this.state;
        const a = "password", b = "password2";

        return (
            <>
                <div className="password-rules">
                    <span>Règles de création de mot de passe</span>
                    <ul>
                        <li>12 caractères minimum</li>
                        <li>au moins 1 majuscule</li>
                        <li>au moins 1 minuscule</li>
                        <li>au moins 1 chiffre</li>
                        <li>au moins 1 caractère spécial</li>
                    </ul>
                </div>
                <Formulaire 
                    onSubmit={this.handleSubmit}
                    success={success} error={error}
                    inputs={
                        <>
                            <Input value={password.value} type="password" identifiant={a} onChange={this.handleChange} error={password.error}>Mot de passe</Input>
                            <Input value={password2.value} type="password" identifiant={b} onChange={this.handleChange} error={password2.error}>Confirmer le mot de passe</Input>
                        </>
                    }
                    btn="Réinitialiser"
                />
            </>
        );
    }
}

export default FormReinit;