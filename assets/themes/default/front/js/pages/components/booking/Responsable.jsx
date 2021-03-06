import React, {Component} from 'react';
import {Step} from './Step';
import {Input} from '../../../components/composants/Fields';
import Validateur from '../../../components/functions/validate_input';

export class StepResponsable extends Component {

    constructor(props){
        super(props);

        this.state = {
            civility: {value: 'Mr', error: ''},
            firstname: {value: '', error: ''},
            lastname: {value: '', error: ''},
            email: {value: '', error: ''},
            confirmeEmail: {value: '', error: ''},
            adr: {value: '', error: ''},
            complement: {value: '', error: ''},
            cp: {value: '', error: ''},
            city: {value: '', error: ''},
            phoneDomicile: {value: '', error: ''},
            phoneMobile: {value: '', error: ''}
        }

        this.handleChange = this.handleChange.bind(this);
        this.handleClickNext = this.handleClickNext.bind(this);
    }

    handleChange (e) {
        let name = e.target.name;
        let value = e.target.value;
        this.setState({ [name]: {value: value} });

        const {phoneDomicile, phoneMobile} = this.state;

        if(name === "phoneMobile"){
            this.setState({ phoneDomicile: {value: phoneDomicile.value, error: ''} });
        }
        if(name === "phoneDomicile"){
            this.setState({ phoneMobile: {value: phoneMobile.value, error: ''} });
        }

        // if(name === "cp"){
        //     if(value === ""){
        //         this.setState({ city: {value: '', error: ''} });
        //     }else{
        //         if(value.length >= 5){
        //             let ville = this.props.cps.filter(obj => Object.keys(obj).some(key => obj[key].includes(value)));        

        //             if(ville.length > 0){
        //                 this.setState({ city: {value: ville[0].nomCommune, error: ''} });
        //             }
        //         }
        //     }
        // }
    }

    handleClickNext (e) {
        const {civility, firstname, lastname, email, confirmeEmail, adr, complement, cp, city, phoneDomicile, phoneMobile} = this.state;

        let validate = Validateur.validateur([
            {type: "text", id: 'firstname', value: firstname.value},
            {type: "text", id: 'lastname', value: lastname.value},
            {type: "email", id: 'email', value: email.value},
            {type: "confirmeEmail", id: 'confirmeEmail', value: confirmeEmail.value, value2: email.value},
            {type: "text", id: 'adr', value: adr.value},
            {type: "cp", id: 'cp', value: cp.value},
            {type: "textAlpha", id: 'city', value: city.value}
        ]);

        // phone facultatif
        let validatePhone;
        if((phoneDomicile.value === "" && phoneMobile.value === "") || (phoneDomicile.value !== "" && phoneMobile.value !== "")){
            validatePhone = Validateur.validateur([
                {type: "customPhone", id: 'phoneDomicile', value: phoneDomicile.value},
                {type: "customPhone", id: 'phoneMobile', value: phoneMobile.value}
            ])
        }else if(phoneDomicile.value !== "" && phoneMobile.value === ""){
            validatePhone = Validateur.validateur([
                {type: "customPhone", id: 'phoneDomicile', value: phoneDomicile.value}
            ])
        }else if(phoneDomicile.value === "" && phoneMobile.value !== ""){
            validatePhone = Validateur.validateur([
                {type: "customPhone", id: 'phoneMobile', value: phoneMobile.value}
            ])
        }
        if(!validatePhone.code){
            validate.code = false;
            validate.errors = {...validate.errors, ...validatePhone.errors};
        }

        // -------
        if(!validate.code){
            this.setState(validate.errors);
        }else{
            let data = {
                civility: civility.value,
                firstname: firstname.value,
                lastname: lastname.value,
                email: email.value,
                phoneDomicile: phoneDomicile.value,
                phoneMobile: phoneMobile.value,
                adr: adr.value,
                complement: complement.value,
                cp: cp.value,
                city: city.value,
            }
            this.props.onToStep2(data);
        }
    }

    render () {
        const {classStep, onAnnulation} = this.props;
        const {firstname, lastname, civility, email, confirmeEmail, adr, complement, cp, city, phoneDomicile, phoneMobile} = this.state;

        let body = <>
            <div className="step-card">
                <div className="ext-responsable">
                    <div className="title">Informations du responsable</div>
                    <div className="formulaire">
                        <RadioCivility civility={civility} onChange={this.handleChange}/>
                        <div className="line line-2">
                            <Input type="text" auto="none" identifiant={"firstname"} value={firstname.value} onChange={this.handleChange} error={firstname.error}>Prénom</Input>
                            <Input type="text" auto="none" identifiant={"lastname"} value={lastname.value} onChange={this.handleChange} error={lastname.error}>Nom</Input>
                        </div>
                        <div className="line">
                            <p className="txt-info">
                               Le <b>ticket</b> sera envoyé à cette adresse e-mail.
                            </p>
                        </div>
                        <div className="line line-2">
                            <Input type="text" auto="none" identifiant={"email"} value={email.value} onChange={this.handleChange} error={email.error}>Adresse e-mail</Input>
                            <Input type="text" auto="none" identifiant={"confirmeEmail"} value={confirmeEmail.value} onChange={this.handleChange} error={confirmeEmail.error}>Confirmer e-mail</Input>
                        </div>
                        <div className="line">
                            <p className="txt-discret">
                               Veuillez renseigner au moins 1 téléphone.
                            </p>
                        </div>
                        <div className="line line-2">
                            <Input type="text" auto="none" identifiant={"phoneMobile"} value={phoneMobile.value} onChange={this.handleChange} error={phoneMobile.error}>Téléphone mobile</Input>
                            <Input type="text" auto="none" identifiant={"phoneDomicile"} value={phoneDomicile.value} onChange={this.handleChange} error={phoneDomicile.error}>Téléphone domicile</Input>
                        </div>
                        <div className="line line-2">
                            <Input type="text" auto="none" identifiant={"adr"} value={adr.value} onChange={this.handleChange} error={adr.error}>Adresse</Input>
                            <Input type="text" auto="none" identifiant={"complement"} value={complement.value} placeholder="(facultatif)" onChange={this.handleChange} error={complement.error}>Complément d'adresse</Input>
                        </div>
                        <div className="line line-2">
                            <Input type="number" auto="none" identifiant={"cp"} value={cp.value} onChange={this.handleChange} error={cp.error}>Code postal</Input>
                            <Input type="text" auto="none" identifiant={"city"} value={city.value} onChange={this.handleChange} error={city.error}>Ville</Input>
                        </div>
                    </div>
                </div>
            </div>
        </>

        return <Step id="1" classStep={classStep} title="Responsable" onClickPrev={onAnnulation} onClickNext={this.handleClickNext} body={body}>
            <div className="text-regular">
                Responsable du/des élève(s) à inscrire, qui effectuera le paiement à l'inscription à la cité de la musique.
            </div>
            <div className="form-infos">
                Les informations recueillies à partir de ce formulaire sont transmises au service de la Cité de la musique dans le but 
                de pré-remplir les inscriptions. Plus d'informations sur le traitement de vos données dans notre 
                politique de confidentialité.
            </div>
        </Step>
    }
}

function RadioCivility({civility, onChange}) {
    return (
        <div className="form-group form-group-radio">
            <div className="radio-choices">
                <div>
                    <input type="radio" autoComplete="off" id="civility-mr" name="civility" value="Mr" checked={civility.value === 'Mr'} onChange={onChange} />
                    <label htmlFor="civility-mr">Mr</label>
                </div>
                <div>
                    <input type="radio" autoComplete="off" id="civility-mme" name="civility" value="Mme" checked={civility.value === 'Mme'} onChange={onChange} />
                    <label htmlFor="civility-mme">Mme</label>
                </div>
            </div>
        </div>
    )
}