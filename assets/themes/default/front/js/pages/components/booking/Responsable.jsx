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
            adr: {value: '', error: ''},
            complement: {value: '', error: ''},
            cp: {value: '', error: ''},
            city: {value: '', error: ''},
            phoneDomicile: {value: '', error: ''},
            phoneMobile: {value: '', error: ''},
            radioResp: {value: '999', error: ''}
        }

        this.reset = this.reset.bind(this);

        this.handleChange = this.handleChange.bind(this);
        this.handleClickPrev = this.handleClickPrev.bind(this);
    }

    reset (){
        this.setState({
            civility: {value: 'Mr', error: ''},
            firstname: {value: '', error: ''},
            lastname: {value: '', error: ''},
            email: {value: '', error: ''},
            adr: {value: '', error: ''},
            complement: {value: '', error: ''},
            cp: {value: '', error: ''},
            city: {value: '', error: ''},
            phoneDomicile: {value: '', error: ''},
            phoneMobile: {value: '', error: ''},
            radioResp: {value: '999', error: ''}
        })
    }

    handleClickPrev (e) {
        this.reset();
        this.props.onClickPrev();
    }

    handleChange (e) {
        let name = e.target.name;
        let value = e.target.value;
        this.setState({ [name]: {value: value} });

        if(name === "radioResp" && value !== "999"){
            this.props.prospects.map((elem, index) => {
                if(index === parseInt(value)){
                    this.setState({
                        civility: {value: elem.civility, error: ''},
                        firstname: {value: elem.firstname, error: ''},
                        lastname: {value: elem.lastname, error: ''},
                        email: {value: elem.email, error: ''},
                        adr: {value: elem.adr, error: ''},
                        complement: {value: '', error: ''},
                        cp: {value: elem.cp, error: ''},
                        city: {value: elem.city, error: ''},
                        phoneDomicile: {value: elem.phoneDomicile, error: ''},
                        phoneMobile: {value: elem.phoneMobile, error: ''}
                    })
                }
            })
        }else if(name === "radioResp" && value === "999"){
            this.reset();
        }
    }

    render () {
        const {classStep, prospects, onClickPrev} = this.props;
        const {firstname, lastname, civility, email, adr, complement, cp, city, phoneDomicile, phoneMobile, radioResp} = this.state;

        let body = <>
            <div className="step-card">
                <div className="int-responsable">
                    <div className="title">Pré-remplir les informations avec : </div>
                    <RadioResponsable items={prospects} radioResp={radioResp} onChange={this.handleChange} />
                </div>
                <div className="ext-responsable">
                    <div className="title">Informations du responsable</div>
                    <div className="formulaire">
                        <RadioCivility civility={civility} onChange={this.handleChange}/>
                        <div className="line line-2">
                            <Input type="text" auto="none" identifiant={"firstname"} value={firstname.value} onChange={this.handleChange} error={firstname.error}>Prénom</Input>
                            <Input type="text" auto="none" identifiant={"lastname"} value={lastname.value} onChange={this.handleChange} error={lastname.error}>Nom</Input>
                        </div>
                        <div className="line line-2">
                            <Input type="text" auto="none" identifiant={"email"} value={email.value} onChange={this.handleChange} error={email.error}>Adresse e-mail</Input>
                        </div>
                        <div className="line line-2">
                            <Input type="text" auto="none" identifiant={"phoneDomicile"} value={phoneDomicile.value} onChange={this.handleChange} error={phoneDomicile.error}>Téléphone domicile</Input>
                            <Input type="text" auto="none" identifiant={"phoneMobile"} value={phoneMobile.value} onChange={this.handleChange} error={phoneMobile.error}>Téléphone mobile</Input>
                        </div>
                        <div className="line line-2">
                            <Input type="text" auto="none" identifiant={"adr"} value={adr.value} onChange={this.handleChange} error={adr.error}>Adresse postale</Input>
                            <Input type="text" auto="none" identifiant={"complement"} value={complement.value} onChange={this.handleChange} error={complement.error}>Complément d'adresse</Input>
                        </div>
                        <div className="line line-2">
                            <Input type="text" auto="none" identifiant={"cp"} value={cp.value} onChange={this.handleChange} error={cp.error}>Code postale</Input>
                            <Input type="text" auto="none" identifiant={"city"} value={city.value} onChange={this.handleChange} error={city.error}>Ville</Input>
                        </div>
                    </div>
                </div>
            </div>
        </>

        return <Step id="2" classStep={classStep} title="Responsable" onClickPrev={this.handleClickPrev} body={body}>
            <span className="text-regular">
                Le responsable designe celui qui effectuera le paiement de l'inscription à la cité de la musique. <br/>
                Il n'est pas forcément un adhérent ou futur adhérent.
            </span>
            Les informations recueillies à partir de ce formulaire sont transmises au service de la Cité de la musique dans le but 
            de pré-remplir les inscriptions. Plus d'informations sur le traitement de vos données dans notre 
            politique de confidentialité.
        </Step>
    }
}

function RadioResponsable({items, radioResp, onChange}){
    let liste = items.map((elem, index) =>{
        return <div key={index}>
            <input type="radio" autoComplete="off" id={"resp-" + index} name="radioResp" value={index} checked={parseInt(radioResp.value) === index} onChange={onChange} />
            <label htmlFor={"resp-" + index}>
                <span className="icon-infos"></span>
                <span>{elem.firstname} {elem.lastname}</span>
            </label>
        </div>
    })

    return (
        <div className="form-group form-group-radio">
            <div>
                <input type="radio" autoComplete="off" id="autre" name="radioResp" value="999" checked={radioResp.value === '999'} onChange={onChange} />
                <label htmlFor="autre">
                    <span>Autre</span>
                </label>
            </div>
            {liste}
        </div>
    )
}

function RadioCivility({civility, onChange}) {
    return (
        <div className="form-group form-group-radio">
            <div>
                <input type="radio" autoComplete="off" id="civility-mr" name="civility" value="Mr" checked={civility.value === 'Mr'} onChange={onChange} />
                <label htmlFor="civility-mr">Mr</label>
            </div>
            <div>
                <input type="radio" autoComplete="off" id="civility-mme" name="civility" value="Mme" checked={civility.value === 'Mme'} onChange={onChange} />
                <label htmlFor="civility-mme">Mme</label>
            </div>
        </div>
    )
}