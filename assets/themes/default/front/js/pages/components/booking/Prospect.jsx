import React, {Component} from 'react';
import {Step} from './Step';
import {Input} from '../../../components/composants/Fields';
import Validateur from '../../../components/functions/validate_input';
import Swal from 'sweetalert2'

/**
    Step  : Récupérer les informations de chaque personnes à inscrire
 */
export class StepProspects extends Component {
    constructor(props){
        super(props)
        
        this.state = {
            added: 0,
            deleted: 0,
            classAdd: '',
            prospects: []
        }

        this.handleClickDelete = this.handleClickDelete.bind(this); 
        this.handleClickAdd = this.handleClickAdd.bind(this); 

        this.handleClickNext = this.handleClickNext.bind(this);

        this.addProspect = this.addProspect.bind(this);
        this.removeProspect = this.removeProspect.bind(this);
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

        window.focus();
    }

    addProspect (data) {
        let tmp = this.state.prospects.filter((elem) => elem.id != data.id)
        let arr = tmp.concat([data])
        this.setState({ prospects: arr })     
    }

    removeProspect (data) {
        let arr = this.state.prospects.filter((elem) => elem.id != data.id)
        this.setState({ prospects: arr })     
    }

    /**
        Gestion étape suivante
     */
    handleClickNext (e) {
        console.log(this.state)
        const {added, deleted} = this.state;

        let remaining = parseInt(added) - parseInt(deleted);
        let cards = document.querySelectorAll('.step-prospect');
        let go = false;
        cards.forEach((elem) => {
            go = elem.classList.contains('valide') ? true : false;
        })
        if(remaining > 0 && go){
        }else{
            Swal.fire({
                title: 'Erreur !',
                html: 'Veuillez <b>valider</b> (boutton bleu) les personnes à inscrire avant de pouvoir passer à l\'étape suivante.',
                icon: 'error',
                confirmButtonText: 'Confirmer'
              })
        }
    }

    render () {
        const {classStep} = this.props;
        const {added, classAdd} = this.state;

        let arr = [];
        for (let i=0 ; i<added ; i++) {
            arr.push(
                <Prospect key={i} id={i} onDeleteCard={this.handleClickDelete} addProspect={this.addProspect} removeProspect={this.removeProspect} />
            )
        }
        
        let body = <>
            <div className={"step-prospects-add-static " + classAdd}>
                <button onClick={this.handleClickAdd}>
                    <span className="icon-add"></span>
                    <span>Ajouter une personne</span>
                </button>
            </div>
            <div className="step-prospects">
                {arr}
            </div>
            <div className={"step-prospects-add " + classAdd}>
                <button onClick={this.handleClickAdd}>
                    <span className="icon-add"></span>
                    <span>Ajouter </span>
                    <span className="text"> une personne</span>
                </button>
            </div>
        </>

        return <Step id="1" classStep={classStep} title="Informations des personnes à inscrire" onClickNext={this.handleClickNext} body={body}>
            Les informations recueillies à partir de ce formulaire sont transmises au service de la Cité de la musique dans le but 
            de pré-remplir les inscriptions. Plus d'informations sur le traitement de vos données dans notre 
            politique de confidentialité.
        </Step>
    }
}

class Prospect extends Component {
    constructor(props){
        super(props)

        this.state = {
            renderCompo: true,
            valide: '',
            firstname: {value: '', error: ''},
            lastname: {value: '', error: ''},
            civility: {value: 'Mr', error: ''},
            birthday: {value: '2019-01-01', error: ''},
            phoneDomicile: {value: '', error: ''},
            phoneTravail: {value: '', error: ''},
            email: {value: '', error: ''},
            adr: {value: '', error: ''},
            cp: {value: '', error: ''},
            city: {value: '', error: ''},
            isAdh: {value: false, error: ''},
            numAdh: {value: '', error: ''}
        }

        this.handleChange = this.handleChange.bind(this);
        this.handleDelete = this.handleDelete.bind(this);
        this.handleClick = this.handleClick.bind(this);
        this.handleClickEdit = this.handleClickEdit.bind(this);
    }

    handleDelete (e) {
        this.setState({renderCompo: false})
        this.props.onDeleteCard();
        let data = {...this.state, ...{id: this.props.id}};
        this.props.removeProspect(data);
    }

    handleChange (e) {
        let name = e.target.name;
        name = name.substr(0,name.indexOf("-"))
        let value = name === 'isAdh' ? e.target.checked : e.target.value;
        this.setState({ [name]: {value: value} });

        const {phoneDomicile, phoneTravail, birthday} = this.state;
        if(name === 'phoneDomicile' || name === 'phoneTravail'){
            let valueD = name === 'phoneDomicile' ? value : phoneDomicile.value;
            let valueT = name === 'phoneTravail' ? value : phoneTravail.value;
            this.setState({ phoneDomicile: {value: valueD ,error: ''}, phoneTravail: {value: valueT ,error: ''}  });
        }
    }

    handleClickEdit (e) {
        this.setState({valide: ''})
    }

    handleClick (e) {
        const {firstname, lastname, email, birthday, adr, cp, city, phoneDomicile, phoneTravail, isAdh, numAdh} = this.state;

        let validate = Validateur.validateur([
            {type: "text", id: 'firstname', value: firstname.value},
            {type: "text", id: 'lastname', value: lastname.value},
            {type: "email", id: 'email', value: email.value},
            {type: "date", id: 'birthday', value: birthday.value},
            {type: "text", id: 'adr', value: adr.value},
            {type: "text", id: 'cp', value: cp.value},
            {type: "text", id: 'city', value: city.value}
        ]);

        // phone facultatif
        let validatePhone;
        if((phoneDomicile.value === "" && phoneTravail.value === "") || (phoneDomicile.value !== "" && phoneTravail.value !== "")){
            validatePhone = Validateur.validateur([
                {type: "customPhone", id: 'phoneDomicile', value: phoneDomicile.value},
                {type: "customPhone", id: 'phoneTravail', value: phoneTravail.value}
            ])
        }else if(phoneDomicile.value !== "" && phoneTravail.value === ""){
            validatePhone = Validateur.validateur([
                {type: "customPhone", id: 'phoneDomicile', value: phoneDomicile.value}
            ])
        }else if(phoneDomicile.value === "" && phoneTravail.value !== ""){
            validatePhone = Validateur.validateur([
                {type: "customPhone", id: 'phoneTravail', value: phoneTravail.value}
            ])
        }
        if(!validatePhone.code){
            validate.code = false;
            validate.errors = {...validate.errors, ...validatePhone.errors};
        }

        // if isAdh is checked
        if(isAdh.value){
            let validateAdh = Validateur.validateur([
                {type: "text", id: 'numAdh', value: numAdh.value}
            ])

            if(!validateAdh.code){
                v2 = {...validate.errors, ...validateAdh.errors};
                validate.code = false;
                validate.errors = v2;
            }
        }

        // -------
        if(!validate.code){
            this.setState(validate.errors);
        }else{
            let data = {...this.state, ...{id: this.props.id}};
            this.setState({valide: 'valide'})
            this.props.addProspect(data);
        }
    }

    render () {
        const {firstname, lastname, civility, birthday, phoneDomicile, phoneTravail, email,
            adr, cp, city, isAdh, numAdh, 
            renderCompo, valide} = this.state;
        const {id} = this.props;

        return <>
            {renderCompo ? <ProspectCard id={id} valide={valide} firstname={firstname} lastname={lastname} civility={civility} 
                            birthday={birthday} phoneDomicile={phoneDomicile} phoneTravail={phoneTravail} email={email}
                            adr={adr} cp={cp} city={city} isAdh={isAdh} numAdh={numAdh}
                            onChange={this.handleChange} onDelete={this.handleDelete} onClick={this.handleClick} onClickEdit={this.handleClickEdit}/> 
                        : null}
        </>
    }
} 

function ProspectCard({id, valide, firstname, lastname, civility, birthday, phoneDomicile, phoneTravail, email, adr, cp, city, isAdh, numAdh,
                        onChange, onDelete, onClick, onClickEdit}) 
    {
    return <div className={"step-card step-prospect " + valide}>

        <span className="title"><span className="icon-infos"></span></span>

        <RadioCivility id={id} civility={civility} onChange={onChange}/>
        <div className="line line-2">
            <Input type="text" identifiant={"firstname-" + id} value={firstname.value} onChange={onChange} error={firstname.error}>Prénom</Input>
            <Input type="text" identifiant={"lastname-" + id} value={lastname.value} onChange={onChange} error={lastname.error}>Nom</Input>
        </div>
        <div className="line line-2">
            <Input type="text" identifiant={"email-" + id} value={email.value} onChange={onChange} error={email.error}>Adresse e-mail</Input>
            <Input type="date" identifiant={"birthday-" + id} value={birthday.value} onChange={onChange} placeholder="JJ/MM/AAAA" error={birthday.error}>Date de naissance</Input>
        </div> 
        <div className="line line-2">
            <Input type="number" identifiant={"phoneDomicile-" + id} value={phoneDomicile.value} onChange={onChange} error={phoneDomicile.error}>Téléphone domicile</Input>
            <Input type="number" identifiant={"phoneTravail-" + id} value={phoneTravail.value} onChange={onChange} error={phoneTravail.error}>Téléphone travail</Input>
        </div>
        <Input type="text" identifiant={"adr-" + id} value={adr.value} onChange={onChange} error={adr.error}>Adresse postal</Input>
        <div className="line line-2">
            <Input type="number" identifiant={"cp-" + id} value={cp.value} onChange={onChange} error={cp.error}>Code postal</Input>
            <Input type="text" identifiant={"city-" + id} value={city.value} onChange={onChange} error={city.error}>Ville</Input>
        </div>
        
        <IsAdh id={id} isAdh={isAdh} numAdh={numAdh} onChange={onChange}/>

        <div className="actions">
            <button className="delete" onClick={onDelete}>Supprimer</button>
            <button className="valide" onClick={onClick}>Valider</button>
        </div>

        <div className={"valideDiv " + valide}>
            <div className="infos">
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
        <div className="form-group form-group-radio">
            <div>
                <input type="radio" id={"civility-mr-" + id} name={"civility-" + id} value="Mr" checked={civility.value === 'Mr'} onChange={onChange} />
                <label htmlFor={"civility-mr-" + id}>Mr</label>
            </div>
            <div>
                <input type="radio" id={"civility-mme" + id} name={"civility-" + id} value="Mme" checked={civility.value === 'Mme'} onChange={onChange} />
                <label htmlFor={"civility-mme" + id}>Mme</label>
            </div>
        </div>
    )
}

function IsAdh({id, isAdh, numAdh, onChange}) {
    return (
        <div className="line line-2">
            <div className="form-group">
                <label htmlFor={"isAdh-" + id}>Déjà adhérent ?</label>
                <input type="checkbox" name={"isAdh-" + id} id={"isAdh-" + id} checked={isAdh.value} onChange={onChange} />
            </div>
            {isAdh.value ? <Input type="text" identifiant={"numAdh-" + id} value={numAdh.value} onChange={onChange} error={numAdh.error}>Numéro adhérent</Input> 
                : null}
        </div>
    )
}