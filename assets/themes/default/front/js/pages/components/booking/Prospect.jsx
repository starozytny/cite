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
            <div className="step-prospects">
                {arr}
            </div>
            <div className={"step-prospects-add " + classAdd}>
                <button onClick={this.handleClickAdd}>Ajouter</button>
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
        let data = {
            id: this.props.id,
            firstname: this.state.firstname.value
        }

        this.props.addProspect(data);
    }

    render () {
        const {firstname, lastname, renderCompo} = this.state;
        const {id} = this.props;

        return <>
            {renderCompo ? <ProspectCard id={id} firstname={firstname} lastname={lastname} onChange={this.handleChange} onDelete={this.handleDelete} onClick={this.handleClick} /> : null}
        </>
    }
} 


/*

title // checkbox
gender // checkbox - switcher
birthday // date
telephone domicile // formatted on change
telephone travail // formatted on change 
adresse email // regex
adr // libre
cp + ville // onchange on ville autocomplete
num adh // txt explain

*/
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