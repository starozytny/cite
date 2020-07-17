import React, {Components} from 'react';
import {Input} from '../../../components/composants/Fields';
import {Formulaire} from '../../../components/composants/Formulaire';
import Validateur from '../../../components/functions/validate_input';
import AjaxSend from '../../../components/functions/ajax_classique';

class FormLost extends React.Component {
    constructor(props) {
        super(props);

        this.state = {
            success: '',
            error: '',
            email: { value: '', error: '' },
            open: ''
        }

        this.handleChange = this.handleChange.bind(this);
        this.handleSubmit = this.handleSubmit.bind(this);
        this.handleOpen = this.handleOpen.bind(this);
        this.handleClose = this.handleClose.bind(this);
    }

    handleChange(e){
        const name = e.currentTarget.name;
        const value = e.currentTarget.value;
        this.setState({
            success: '',
            [name]: {value: value}
        });
    } 

    handleSubmit(e){
        e.preventDefault();

        //Validation
        let validate = Validateur.validateur([
            {type: "email", id: 'email', value: this.state.email.value}
        ]);

        //Display error if validate != true else call Ajax password lost
        if(!validate.code){
            this.setState(validate.errors);
        }else{
            AjaxSend.sendAjax(this, this.props.url, this.state, {
                email: { value: '', error: '' }
            });
        }
    }

    handleOpen (e) {
        e.preventDefault();
        this.setState({open: 'active'})
    }

    handleClose (e) {
        this.setState({open: '', email: { value: '', error: '' }, error: '', success: ''})
    }

    render() {
        const {success, error, email, open} = this.state;

        return (
            <>
                <button className="link link-primary" onClick={this.handleOpen}>Mot de passe oublié ?</button>
                <div className={"form-lost-overlay " + open} onClick={this.handleClose}></div>
                <div className={"form-lost " + open}>

                    <div className="title">
                        <div>Mot de passe oublié ?</div>
                    </div>

                    <Formulaire 
                        onSubmit={this.handleSubmit}
                        success={success}
                        error={error}
                        inputs={
                            <Input value={email.value} identifiant="email" onChange={this.handleChange} error={email.error}>Email</Input>
                        }
                        btn="Envoyer"
                    />
                </div>
            </>
        );
    }
}

export default FormLost;