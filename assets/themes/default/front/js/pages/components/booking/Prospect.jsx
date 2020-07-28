import React, {Component} from 'react';
import axios from 'axios/dist/axios';
import Routing from '../../../../../../../../public/bundles/fosjsrouting/js/router.min.js';
import AjaxSend from '../../../components/functions/ajax_classique';
import {Step} from './Step';
import {Input} from '../../../components/composants/Fields';
import Validateur from '../../../components/functions/validate_input';
import Swal from 'sweetalert2';
import DatePicker from "react-datepicker";
import { registerLocale, setDefaultLocale } from  "react-datepicker";
import fr from 'date-fns/locale/fr';
registerLocale('fr', fr)
import "react-datepicker/dist/react-datepicker.css";

/**
    Step  : Récupérer les informations de chaque personnes à inscrire
 */
export class StepProspects extends Component {
    constructor(props){
        super(props)
        
        this.state = {
            added: 1,
            deleted: 0,
            classAdd: '',
        }

        this.handleClickDelete = this.handleClickDelete.bind(this); 
        this.handleClickAdd = this.handleClickAdd.bind(this); 

        this.handleClickNext = this.handleClickNext.bind(this);
    }

    /**
        Gestion d'ajout et suppression d'inscrits
     */
    handleClickDelete (e) {
        this.setState({deleted: parseInt(this.state.deleted) + 1, classAdd: ''})
    }
    handleClickAdd (e) {
        let value = parseInt(this.state.added) + 1;
        let valueDeleted = parseInt(this.state.deleted);
        let remaining = value - valueDeleted;
        if(remaining < 10){
            this.setState({added: value});
        }else if (remaining === 10){
            this.setState({added: value, classAdd: 'full'});
        }else{
            this.setState({classAdd: 'full'});
        }
        setTimeout(() => {
            let v = value - 1;
            let element = document.querySelector('.step-prospect-' + v);
            let input0 = document.querySelector('.step-prospect-' + v + ' #numAdh-' + v);
            let input1 = document.querySelector('.step-prospect-' + v + ' #firstname-' + v);
            input0 != null ? input0.focus() : input1.focus();
            const supportsNativeSmoothScroll = 'scrollBehavior' in document.documentElement.style;
            supportsNativeSmoothScroll ? window.scrollTo({ top: element.offsetTop, behavior: 'smooth' }) : window.scrollTo(0, element.offsetTop);
            
        }, 250);
    }

    /**
        Gestion étape suivante
     */
    handleClickNext (e) {
        let go = false; let data = [];
        for(let ref in this.refs){
            let st = this.refs[ref].state;

            if(!st.deleted){
                let r = this.refs[ref].handleClick();
                if(r.code === 1){
                    go = true;
                    
                    let d = {
                        civility: st.civility.value,
                        firstname: st.firstname.value,
                        lastname: st.lastname.value,
                        email: st.email.value,
                        birthday: st.birthday.value,
                        phoneDomicile: st.phoneDomicile.value,
                        phoneMobile: st.phoneMobile.value,
                        adr: st.adr.value,
                        cp: st.cp.value,
                        city: st.city.value,
                        isAdh: st.isAdh.value,
                        numAdh: st.numAdh.value,
                        idReact: st.idReact,
                        registered: false
                    }
                    data.push(d);
                }else{
                    go = false;
                }
            }
        }

        if(go){
            let prospectsNoDoublon = data.filter((thing, index, self) =>
                index === self.findIndex((t) => (
                    t.civility === thing.civility && t.firstname === thing.firstname && t.lastname === thing.lastname &&
                    t.birthday === thing.birthday && t.numAdh === thing.numAdh
                ))
            )
            this.props.onStep3(prospectsNoDoublon);
        }
    }

    render () {
        const {classStep, prospects, dayType, onAnnulation, onClickPrev} = this.props;
        const {added, classAdd} = this.state;
        let arr = [];
        for (let i=0 ; i<added ; i++) {
            let registered = false;
            prospects.forEach(element => {
                if(parseInt(element.idReact) === i && element.registered == true){
                    registered = true;
                }
            });

            arr.push(
                <Prospect key={i} id={i} dayType={dayType} ref={"child" + i} registered={registered} onDeleteCard={this.handleClickDelete} />
            )
        }
        
        let body = <>
            <div className={"step-prospects-add-static " + classAdd}>
                    <button onClick={this.handleClickAdd}>
                        <span className="icon-add"></span>
                        <span>Ajouter un éléve</span>
                    </button>
                </div>
            <div className="step-prospects">
                {arr}
                <div className={"step-prospects-add " + classAdd}>
                    <button onClick={this.handleClickAdd}>
                        <span className="icon-add"></span>
                        <span>Ajouter un élève</span>
                    </button>
                </div>
                <div className="step-prospects-add-anchor"></div>
            </div>
        </>

        return <Step id="2" classStep={classStep} title="Elève(s) à inscrire" specialFull={classAdd} onClickPrev={onClickPrev} onClickNext={this.handleClickNext} body={body}>
            <div className="form-infos">
                Les informations recueillies à partir de ce formulaire sont transmises au service de la Cité de la musique dans le but 
                de pré-remplir les inscriptions. Plus d'informations sur le traitement de vos données dans notre 
                politique de confidentialité.
            </div>
            <div className="annulation">
                <button className="btn" onClick={onAnnulation}>Annuler la réservation</button>
            </div>
        </Step>
    }
}

class Prospect extends Component {
    constructor(props){
        super(props)

        this.state = {
            renderCompo: true,
            valide: '',
            idReact: this.props.id,
            deleted: false,
            firstname: {value: '', error: ''},
            lastname: {value: '', error: ''},
            civility: {value: 'Mr', error: ''},
            phoneDomicile: {value: '', error: ''},
            phoneMobile: {value: '', error: ''},
            email: {value: '', error: ''},
            adr: {value: '', error: ''},
            cp: {value: '', error: ''},
            city: {value: '', error: ''},
            isAdh: {value: this.props.dayType == 0 ? true : false, error: ''},
            numAdh: {value: '', error: ''},
            birthday: {value: '', error: '', inputVal: null},
            disabledInput: false
        }

        this.handleChange = this.handleChange.bind(this);
        this.handleDate = this.handleDate.bind(this);
        this.handleDelete = this.handleDelete.bind(this);
        this.handleClick = this.handleClick.bind(this);
        this.handleClickEdit = this.handleClickEdit.bind(this);

        this.handleBlur = this.handleBlur.bind(this);
    }

    handleDelete (e) {
        let self = this;
        Swal.fire({
            title: 'Etes-vous sur ?',
            text: "La suppression est irréversible.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Oui, je supprime',
            cancelButtonText: "Non",
            }).then((result) => {
            if (result.value) {
                self.setState({renderCompo: false, deleted: true})
                self.props.onDeleteCard();
            }
        })
        
    }

    handleDate (e) {
        this.setState({ birthday: {value: new Date(e).toLocaleDateString(), inputVal: e} });
    }

    handleChange (e) {
        let name = e.target.name;
        name = name.substr(0,name.indexOf("-"))
        let value = name === 'isAdh' ? e.target.checked : e.target.value;
        this.setState({ [name]: {value: value} });

        const {phoneDomicile, phoneMobile} = this.state;
        if(name === 'phoneDomicile' || name === 'phoneMobile'){
            let valueD = name === 'phoneDomicile' ? value : phoneDomicile.value;
            let valueT = name === 'phoneMobile' ? value : phoneMobile.value;
            this.setState({ phoneDomicile: {value: valueD ,error: ''}, phoneMobile: {value: valueT ,error: ''}  });
        }
    }

    handleBlur (e) {
        let value = e.currentTarget.value;
        let self = this;
        if(value !== ""){
            axios({ 
                method: 'post', 
                url: Routing.generate('app_booking_tmp_prospect_preset'),
                data: {numAdh: value}
            }).then(function (response) {
                let data = response.data; let code = data.code; AjaxSend.loader(false);
                if(code === 1){
                    let item = JSON.parse(data.infos);

                    self.setState({
                        firstname: {value: setEmptyIfNull(item.firstname), error: ''},
                        lastname: {value: setEmptyIfNull(item.lastname), error: ''},
                        civility: {value: setEmptyIfNull(item.civility), error: ''},
                        phoneDomicile: {value: setEmptyIfNull(item.phoneDomicile), error: ''},
                        phoneMobile: {value: setEmptyIfNull(item.phoneMobile), error: ''},
                        email: {value: setEmptyIfNull(item.email), error: ''},
                        adr: {value: setEmptyIfNull(item.adr), error: ''},
                        cp: {value: setEmptyIfNull(item.cp), error: ''},
                        city: {value: setEmptyIfNull(item.city), error: ''},
                        birthday: {
                            error: '', 
                            value: item.birthday != null ? new Date(item.birthdayJavascript).toLocaleDateString() : "", 
                            inputVal: item.birthdayJavascript != null ? new Date(item.birthdayJavascript) : null},
                        disabledInput: true
                    })
                }else{
                    self.setState({
                        firstname: {value: '', error: ''},
                        lastname: {value: '', error: ''},
                        civility: {value: '', error: ''},
                        phoneDomicile: {value: '', error: ''},
                        phoneMobile: {value: '', error: ''},
                        email: {value: '', error: ''},
                        adr: {value: '', error: ''},
                        cp: {value: '', error: ''},
                        city: {value: '', error: ''},
                        numAdh: {value: value, error: 'Ce numéro adhérent n\'existe pas.'},
                        birthday: {value: '', error: '', inputVal: null},
                        disabledInput: false
                    })
                }
            });
        }else{
            self.setState({disabledInput: false})
        }
    }

    handleClickEdit (e) {
        this.setState({valide: ''})
    }

    handleClick (e) {
        const {firstname, civility, lastname, email, birthday, phoneMobile, isAdh, numAdh} = this.state;

        let validate = Validateur.validateur([
            {type: "text", id: 'firstname', value: firstname.value},
            {type: "text", id: 'lastname', value: lastname.value},
            {type: "civility", id: 'civility', value: civility.value},
            {type: "text", id: 'birthday', value: birthday.value},
        ]);

        // if isAdh is checked
        if(isAdh.value){
            let validateAdh = Validateur.validateur([
                {type: "text", id: 'numAdh', value: numAdh.value}
            ])

            if(!validateAdh.code){
                validate.code = false;
                validate.errors = {...validate.errors, ...validateAdh.errors};
            }
        }

        // -------
        if(!validate.code || numAdh.error != undefined){
            this.setState(validate.errors);
            return {code: 0};
        }else{
            this.setState({valide: 'valide'})
            return {
                code: 1,
                id: this.props.id
            };
        }
    }

    render () {
        const {firstname, lastname, civility, birthday, phoneMobile, email, isAdh, numAdh, renderCompo, valide, disabledInput} = this.state;
        const {id, registered, dayType} = this.props;

        return <>
            {renderCompo ? <ProspectCard id={id} dayType={dayType} registered={registered} valide={valide} firstname={firstname} lastname={lastname} civility={civility} 
                            birthday={birthday} phoneMobile={phoneMobile} email={email} isAdh={isAdh} numAdh={numAdh} disabledInput={disabledInput}
                            onChange={this.handleChange} onDelete={this.handleDelete} onClickEdit={this.handleClickEdit} onChangeDate={this.handleDate} onBlur={this.handleBlur}/> 
                        : null}
        </>
    }
} 

function ProspectCard({id, dayType, registered, valide, firstname, lastname, civility, birthday, phoneMobile, email, isAdh, numAdh, disabledInput,
                        onChange, onDelete, onClickEdit, onChangeDate, onBlur}) 
    {

    return <div className={"step-card step-prospect-"+ id +" step-prospect " +  registered}>
        <IsAdh id={id} isAdh={isAdh} dayType={dayType} numAdh={numAdh} onChange={onChange} onBlur={onBlur}/>
        <RadioCivility id={id} civility={civility} onChange={onChange}/>
        <div className="line line-2">
            <Input type="text" disabled={disabledInput ? "disabled" : null} identifiant={"firstname-" + id} value={firstname.value} onChange={onChange} error={firstname.error}>Prénom</Input>
            <Input type="text" disabled={disabledInput ? "disabled" : null} identifiant={"lastname-" + id} value={lastname.value} onChange={onChange} error={lastname.error}>Nom</Input>
        </div>
        <div className="line line-2">
            <div className={'form-group-date form-group' + (birthday.error ? " form-group-error" : "")}>
                <label>Date anniversaire</label>
                <DatePicker
                    locale="fr"
                    selected={birthday.inputVal}
                    onChange={onChangeDate}
                    dateFormat="dd/MM/yyyy"
                    peekNextMonth
                    showMonthDropdown
                    showYearDropdown
                    dropdownMode="select"
                    placeholderText="DD/MM/YYYY"
                    />
                <div className='error'>{birthday.error ? birthday.error : null}</div>
            </div>
        </div> 
        <div className="line line-2">
            {/* <Input type="number" identifiant={"phoneDomicile-" + id} value={phoneDomicile.value} onChange={onChange} error={phoneDomicile.error}>Téléphone domicile</Input> */}
            <Input type="text" identifiant={"email-" + id} value={email.value} onChange={onChange} placeholder="(facultatif)" error={email.error}>Adresse e-mail</Input>
            <Input type="number" identifiant={"phoneMobile-" + id} value={phoneMobile.value} onChange={onChange} placeholder="(facultatif)" error={phoneMobile.error}>Téléphone mobile</Input>
        </div>
        
        {/* <Input type="text" identifiant={"adr-" + id} value={adr.value} onChange={onChange} error={adr.error}>Adresse postal</Input>
        <div className="line line-2">
            <Input type="number" identifiant={"cp-" + id} value={cp.value} onChange={onChange} error={cp.error}>Code postal</Input>
            <Input type="text" identifiant={"city-" + id} value={city.value} onChange={onChange} error={city.error}>Ville</Input>
        </div> */}

        <div className="actions">
            <button className="delete" onClick={onDelete}>Supprimer</button>
        </div>

        <div className={"valideDiv " + valide}>
            <div className="infos">
                <div className="registered">Déjà inscrit</div>
                <div>{civility.value}. {lastname.value} {firstname.value}</div>
                <div>{email.value}</div>
                <div>{birthday.value}</div>
            </div>
            <div className="actions">
                <button className="delete" onClick={onDelete}>Supprimer</button>
                <button className="edit" onClick={onClickEdit}>Modifier</button>
            </div>
        </div>
    </div>
}

function RadioCivility({id, civility, onChange}) {
    return (
        <div className={'form-group-radio form-group' + (civility.error ? " form-group-error" : "")}>
            <div className="radio-choices">
                <div>
                    <input type="radio" id={"civility-mr-" + id} name={"civility-" + id} value="Mr" checked={civility.value === 'Mr'} onChange={onChange} />
                    <label htmlFor={"civility-mr-" + id}>Mr</label>
                </div>
                <div>
                    <input type="radio" id={"civility-mme-" + id} name={"civility-" + id} value="Mme" checked={civility.value === 'Mme'} onChange={onChange}/>
                    <label htmlFor={"civility-mme-" + id}>Mme</label>
                </div>
            </div>
            <div className='error'>{civility.error ? civility.error : null}</div>
        </div>
    )
}

function IsAdh({id, isAdh, dayType, numAdh, onChange, onBlur}) {
    let dis = dayType == 0 ? "disabled" : "";
    return (
        <div className="line line-2">
            <div className={"form-group-checkbox form-group " + dis}>
                <label htmlFor={"isAdh-" + id}>Déjà adhérent ?</label>
                <input type="checkbox" name={"isAdh-" + id} id={"isAdh-" + id} checked={isAdh.value} disabled={dis} onChange={onChange} />
            </div>
            {isAdh.value ? <Input type="text" identifiant={"numAdh-" + id} value={numAdh.value} onChange={onChange} error={numAdh.error} onBlur={onBlur}>Numéro adhérent</Input> 
                : null}
        </div>
    )
}

function setEmptyIfNull(value){
    return value != null ? value : "";
}