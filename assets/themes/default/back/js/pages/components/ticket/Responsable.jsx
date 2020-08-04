import React, {Component} from 'react';
import axios from 'axios/dist/axios';
import Routing from '../../../../../../../../public/bundles/fosjsrouting/js/router.min.js';
import AjaxSend from '../../../components/functions/ajax_classique';
import {Input, Select} from '../../../components/composants/Fields';
import Validateur from '../../../components/functions/validate_input';
import Swal from 'sweetalert2';

export class ResendTicket extends Component {
    constructor (props){
        super(props);

        this.handleSendTicket = this.handleSendTicket.bind(this);
    }

    handleSendTicket (e) {
        Swal.fire({
            title: 'Souhaitez-vous renvoyer le ticket ?',
            text: "Le ticket sera envoyé à l\'adresse email du responsable.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Confirmer',
            cancelButtonText: "Non",
          }).then((result) => {
            if (result.value) {
                AjaxSend.loader(true);
                axios({ 
                    method: 'post', 
                    url: Routing.generate('admin_ticket_send', {'id': this.props.responsableId}),
                }).then(function (response) {
                    AjaxSend.loader(false);
                    Swal.fire('Ticket envoyé!', '', 'success' )
                });
            }
          })
    }

    render () {
        return <div>
            <button className="btn btn-secondary" onClick={this.handleSendTicket}>Renvoyer le ticket</button>
        </div>
    }
}

export class EditResponsable extends Component {
    constructor (props){
        super(props);

        let resp = JSON.parse(JSON.parse(props.resp));
        this.state = {
            error: '',
            id: resp.id,
            civility: {value: resp.civility, error: ''},
            firstname: {value: resp.firstname, error: ''},
            lastname: {value: resp.lastname, error: ''},
            email: {value: resp.email, error: ''},
            adr: {value: resp.adr, error: ''},
            complement: {value: (resp.complement == null ? '' : resp.complement), error: ''},
            cp: {value: resp.cp, error: ''},
            city: {value: resp.city, error: ''},
            phoneDomicile: {value: (resp.phoneDomicile == null ? '' : resp.phoneDomicile), error: ''},
            phoneMobile: {value: (resp.phoneMobile == null ? '' : resp.phoneMobile), error: ''}
        }

        this.handleSubmit = this.handleSubmit.bind(this);
        this.handleChange = this.handleChange.bind(this);
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

        if(name === "cp"){
            if(value === ""){
                this.setState({ city: {value: '', error: ''} });
            }else{
                let ville = JSON.parse(JSON.parse(this.props.cps)).filter(obj => Object.keys(obj).some(key => obj[key].includes(value)));        

                if(ville.length > 0){
                    this.setState({ city: {value: ville[0].nomCommune, error: ''} });
                }
            }
        }
    }

    handleSubmit (e) {
        e.preventDefault();

        const {id, civility, firstname, lastname, email, adr, complement, cp, city, phoneDomicile, phoneMobile} = this.state;

        let validate = Validateur.validateur([
            {type: "text", id: 'firstname', value: firstname.value},
            {type: "text", id: 'lastname', value: lastname.value},
            {type: "email", id: 'email', value: email.value},
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
            AjaxSend.loader(true);
            let self = this;
            axios({ 
                method: 'post', 
                url: Routing.generate('admin_responsable_update', { 'responsable' : id }),
                data: {responsable: data}
            }).then(function (response) {
                let data = response.data; let code = data.code; AjaxSend.loader(false);

                if (code === 1){
                    Swal.fire({
                        title: 'Souhaitez-vous renvoyer le ticket ?',
                        text: "Le ticket sera envoyé à l\'adresse email du responsable.",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Confirmer',
                        cancelButtonText: "Non",
                    }).then((result) => {
                        AjaxSend.loader(true);

                        if (result.value) {
                            axios({  
                                method: 'post',  url: Routing.generate('admin_ticket_send', {'id': id}) 
                            }).then(function (response) {
                                location.reload();
                            });
                        }else{
                            location.reload();
                        }
                    })
                }else{
                    self.setState({ errorEdit: data.message });
                }
            });
        }
    }

    render () {
        const {error, civility, firstname, lastname, email, phoneMobile, phoneDomicile, adr, complement, cp, city} = this.state;

        return <>
            <div className="edit-responsable">
            <div className="alert alert-infos">
                    Toute modification affectera le/les élèves attaché à ce responsable.
                </div>
                <div className="title">Modification du responsable</div>
                <form className="formulaire" onSubmit={this.handleSubmit}>

                    {error != "" ? <div className="alert alert-danger">{error}</div> : null}

                    <RadioCivility civility={civility} onChange={this.handleChange}/>
                    <div className="line line-2">
                        <Input type="text" auto="none" identifiant={"firstname"} value={firstname.value} onChange={this.handleChange} error={firstname.error}>Prénom</Input>
                        <Input type="text" auto="none" identifiant={"lastname"} value={lastname.value} onChange={this.handleChange} error={lastname.error}>Nom</Input>
                    </div>
                    <div className="line line-2">
                        <Input type="text" auto="none" identifiant={"email"} value={email.value} onChange={this.handleChange} error={email.error}>Adresse e-mail</Input>
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
                    <div className="alert alert-infos">
                        Après modification, assurez-vous de <b>renvoyer</b> le ticket au responsable.
                    </div>

                    <div className="from-group">
                        <button className="btn btn-primary" type="submit">Mettre à jour</button>
                    </div>
                </form>
            </div>
        </>
    }
}

function RadioCivility({civility, onChange}) {
    return (
        <div className="form-group form-group-radio">
            <div>
                <input type="radio" id="civility-mr" name="civility" value="Mr" checked={civility.value === 'Mr'} onChange={onChange} />
                <label htmlFor="civility-mr">Mr</label>
            </div>
            <div>
                <input type="radio" id="civility-mme" name="civility" value="Mme" checked={civility.value === 'Mme'} onChange={onChange} />
                <label htmlFor="civility-mme">Mme</label>
            </div>
        </div>
    )
}