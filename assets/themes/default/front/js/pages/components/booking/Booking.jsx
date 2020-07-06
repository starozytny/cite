import React, {Component} from 'react';
import {Input} from '../../../components/composants/Fields';

export class Booking extends Component {
    constructor(props){
        super(props);

        this.state = {
            classStart: '',
            classStep1: ''
        }

        this.handleClickStart = this.handleClickStart.bind(this)
    }

    /**
    * Fonction pour commencer le processus de demande de ticket.
    */
    handleClickStart (e) {
        this.setState({classStart: 'hide', classStep1: 'active'})
    }

    render () {

        const {classStart, classStep1, nbPersonne} = this.state;

        return <>
            <section className={"section-infos " + classStart}>
                <Infos />
                <Starter onClick={this.handleClickStart}/>
            </section>
            <section className="section-steps">
                <div className="steps">
                    <Step1 classStep={classStep1}/>
                </div>
            </section>
        </>
    }
}

/**
    Step  : Récupérer les informations de chaque personnes à inscrire
 */
export class Step1 extends Component {
    constructor(props){
        super(props)
        
        this.state = {
            added: 0,
            deleted: 0
        }

        this.handleClickDelete = this.handleClickDelete.bind(this); 
        this.handleClickAdd = this.handleClickAdd.bind(this); 

        this.handleClickNext = this.handleClickNext.bind(this);
    }

    /**
        Gestion d'ajout et suppression d'inscrits
     */
    handleClickDelete (e) {
        this.setState({deleted: parseInt(this.state.deleted) + 1})
    }
    handleClickAdd (e) {
        this.setState({added: parseInt(this.state.added) + 1})
    }

    /**
        Gestion étape suivante
     */
    handleClickNext (e) {
    }

    render () {
        const {classStep} = this.props;
        const {added} = this.state;

        let arr = [];
        for (let i=0 ; i<added ; i++) {
            arr.push(
                <Prospect key={i} id={i} onDeleteCard={this.handleClickDelete}/>
            )
        }
        
        let body = <>
            <div className="step-prospects">
                {arr}
            </div>
            <div>
                <button onClick={this.handleClickAdd}>Add</button>
            </div>
        </>

        return <Step id="1" classStep={classStep} title="Informations" onClickNext={this.handleClickNext} body={body}>
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

    }

    render () {
        const {firstname, lastname, renderCompo} = this.state;
        const {id} = this.props;

        return <>
            {renderCompo ? <ProspectCard id={id} firstname={firstname} lastname={lastname} onChange={this.handleChange} onDelete={this.handleDelete} onClick={this.handleClick} /> : null}
        </>
    }
} 

function ProspectCard({id, firstname, lastname, 
                        onChange, onDelete, onClick}) 
    {
    return <div className="step-card step-prospect">
        <Input type="text" identifiant={"firstname-" + id} value={firstname.value} onChange={onChange} error={firstname.error}>Prénom</Input>
        <Input type="text" identifiant={"lastname-" + id} value={lastname.value} onChange={onChange} error={lastname.error}>Nom</Input>

        <button onClick={onDelete}>Supprimer</button>
        <button onClick={onClick}>Valider</button>
    </div>
}

function Step({id, classStep, title, body, onClickNext, onClickPrev, children}) {
    return (
        <div className={"step step-" + id + " " + classStep}>
            <div className="step-title">
                <h2>{title}</h2>
                {children ? <p>{children}</p> : null}
            </div>
            <div className="step-content">
                {body}
            </div>
            <div className="step-actions">
                <button className="btn btn-back" onClick={onClickPrev}>Retour</button>
                <button className="btn btn-primary" onClick={onClickNext}>Suivant</button>
            </div>
        </div>
    )
}

function Starter({onClick}) {
    return (
        <div className="starter">
            <div className="starter-card">
                <div className="starter-infos">
                    <p>
                        Déroulement : 
                    </p>
                    <ul>
                        <li>Faire sa demande de ticket pour X personnes.</li>
                        <li>Récupérer son ticket et sa plage horaire grâce au mail envoyé.</li>
                        <li>Se rendre à la journée d'inscription à l'horaire indiqué.</li>
                    </ul>
                    <div className="alert alert-danger">
                        A la journée d'inscription veuillez prendre avec vous le document suivant : Avis d'impôts
                    </div>
                </div>
                <div className="starter-btn">
                    <button className="btn btn-primary" onClick={onClick}>Prendre de ticket</button>
                </div>
            </div>
        </div>
    )
}

function Infos() {
    return (
        <div className="informations">
            <h1>Réservation d'un ticket</h1>
            <p className="subtitle">Journée d'inscription du mardi 8 septembre 2020</p>
                    
            <p>
                Pour obtenir votre ticket d’entrée à la journée d’inscription de la Cité de la musique, complétez le formulaire suivant.
                <br /><br />
                Votre ticket et l’heure à laquelle vous devez vous présenter vous seront envoyés par email.
                <br /><br /><br /><br />
                <b className="txt-danger">Important :</b> Compte-tenu du nombre important de demandes, nous ne pouvons délivrer qu’un ticket par famille. Merci pour votre compréhension.
            </p>
            <p className="informations-complementaire">
                Pour toute information concernant le déroulement de cette journée : 
                <br />
                04 91 39 28 28
            </p>
        </div>
    )
}