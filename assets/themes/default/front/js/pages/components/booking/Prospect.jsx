import React, {Component} from 'react';
import {Step} from './Step';
import {Input} from '../../../components/composants/Fields';

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

        console.log(this.props)
        console.log(this.state)
    }

    addProspect (data) {
        let tmp = this.state.prospects.filter((elem) => elem.id != data.id)
        let arr = tmp.concat([data])
        this.setState({ prospects: arr })     
    }

    /**
        Gestion étape suivante
     */
    handleClickNext (e) {
        console.log(this.state)
    }

    render () {
        const {classStep} = this.props;
        const {added, classAdd} = this.state;

        let arr = [];
        for (let i=0 ; i<added ; i++) {
            arr.push(
                <Prospect key={i} id={i} onDeleteCard={this.handleClickDelete} addProspect={this.addProspect}/>
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
            valide: false,
            firstname: {value: '', error: ''},
            lastname: {value: '', error: ''},
            civility: {value: 'mr', error: ''},
            birthday: {value: '', error: ''},
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
    }

    handleDelete (e) {
        this.setState({renderCompo: false})
        this.props.onDeleteCard();
    }

    handleChange (e) {
        let name = e.target.name;
        this.setState({ [name.substr(0,name.indexOf("-"))]: {value: e.target.value} });
    }

    handleClick (e) {
        let data = {
            id: this.props.id,
            firstname: this.state.firstname.value
        }

        this.props.addProspect(data);
    }

    render () {
        const {firstname, lastname, civility, birthday, phoneDomicile, phoneTravail, email,
            adr, cp, city, isAdh, numAdh, 
            renderCompo} = this.state;
        const {id} = this.props;

        return <>
            {renderCompo ? <ProspectCard id={id} firstname={firstname} lastname={lastname} civility={civility} 
                            birthday={birthday} phoneDomicile={phoneDomicile} phoneTravail={phoneTravail} email={email}
                            adr={adr} cp={cp} city={city} isAdh={isAdh} numAdh={numAdh}
                            onChange={this.handleChange} onDelete={this.handleDelete} onClick={this.handleClick} /> 
                        : null}
        </>
    }
} 


/*

num adh // txt explain

*/
function ProspectCard({id, firstname, lastname, civility, birthday, phoneDomicile, phoneTravail, email, adr, cp, city, isAdh, numAdh,
                        onChange, onDelete, onClick}) 
    {
    return <div className="step-card step-prospect">

        <span className="title"><span className="icon-infos"></span></span>

        <RadioCivility id={id} civility={civility} onChange={onChange}/>
        <div className="line line-2">
            <Input type="text" identifiant={"firstname-" + id} value={firstname.value} onChange={onChange} error={firstname.error}>Prénom</Input>
            <Input type="text" identifiant={"lastname-" + id} value={lastname.value} onChange={onChange} error={lastname.error}>Nom</Input>
        </div>
        <div className="line line-2">
            <Input type="text" identifiant={"email-" + id} value={email.value} onChange={onChange} error={email.error}>Adresse e-mail</Input>
            <Input type="text" identifiant={"birthday-" + id} value={birthday.value} onChange={onChange} placeholder="JJ/MM/AAAA" error={birthday.error}>Date de naissance</Input>
        </div>
        <div className="line line-2">
            <Input type="text" identifiant={"phoneDomicile-" + id} value={phoneDomicile.value} onChange={onChange} error={phoneDomicile.error}>Téléphone domicile</Input>
            <Input type="text" identifiant={"phoneTravail-" + id} value={phoneTravail.value} onChange={onChange} error={phoneTravail.error}>Téléphone travail</Input>
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
    </div>
}

function RadioCivility({id, civility, onChange}) {
    return (
        <div className="form-group form-group-radio">
            <div>
                <input type="radio" id="mr" name={"civility-" + id} value="mr" checked={civility.value === 'mr'} onChange={onChange} />
                <label htmlFor="mr">Mr</label>
            </div>
            <div>
                <input type="radio" id="mme" name={"civility-" + id} value="mme" checked={civility.value === 'mme'} onChange={onChange} />
                <label htmlFor="mme">Mme</label>
            </div>
        </div>
    )
}

function IsAdh({id, isAdh, numAdh, onChange}) {
    return (
        <>
            <div className="form-group">
                <label htmlFor="isAdh">Déjà adhérent ?</label>
                <input type="checkbox" name="isAdh" id="isAdh" checked={isAdh.value} onChange={onChange} />
            </div>
            {isAdh.value ? <Input type="text" identifiant={"numAdh-" + id} value={numAdh.value} onChange={onChange} error={numAdh.error}>Numéro adhérent</Input> 
                : null}
        </>
    )
}