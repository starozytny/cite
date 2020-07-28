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

        if(name === "cp"){
            let mars = ['13000', '13001', '13002', '13003', '13004', '13005', '13006', '13007', '13008', '13009', '13010', '13011', '13012', '13013', '13014', '13015', '13016']
            if(mars.includes(value)){
                this.setState({ city: {value: 'Marseille', error: ''} });
            }
            let othersCp = ['03000', '05000', '04130', '04190', '06100', '06800', '07320', '13100', '13090', '13105', '13109', '13110', '13111', '13112',
                            '13116', '13119', '13120', '13122', '13124', '13127', '13130', '13140', '13150', '13170', '13180', '13190', '13200', '13220',
                            '13240', '13250', '13260', '13250', '13270', '13300', '13320', '13330', '13340', '13360', '13370', '13380', '13390', '13400',
                            '13410', '13420', '13470', '13480', '13500', '13510', '13530', '13540', '13560', '13580', '13590', '13600', '13610', '13620',
                            '13640', '13650', '13700', '13720', '13740', '13770', '13780', '13820', '13821', '83270'];
            let othersCity = ['Montilly', 'Gap', 'Volx', 'Les Mees', 'Nice', 'Cagnes-sur-mer', 'Devesset', 'Aix-en-Provence', 'Aix-en-Provence', 'Mimet', 'Siminiane-Collongue', 'Port-de-Bouc',
                              'Coudoux', 'La Destrousse', 'Vernegues', 'Saint-Savournin', 'Gardanne', 'Ventabren', 'Peypin', 'Vitrolles', 'Berre-l\'Etang', 'Miramas', 'Tarascon', 
                              'Les Pennes-Mirabeau', 'Gignac-la-Nerthe', 'Allauch', 'Arles', 'Chateauneuf-les-Martigues', 'Septemes-les-Vallons', 'Saint-Chamas','Cassis',
                              'Cornillon-Confoux', 'Fos-sur-Mer', 'Salon-de-Provence', 'Bouc-Bel-Air', 'Pelissanne', 'Rognac', 'Roquevaire', 'Mallemort', 'Plan-de-Cuques',
                              'Auriol', 'Aubagne', 'Lambesc', 'Gemenos', 'Carnoux-en-Provence', 'Cabries', 'Martigues', 'Eguilles', 'Trets', 'Puyricard', 'Senas', 'La Fare-les-Oliviers',
                              'Meyreuil', 'La Ciotat', 'Le Puy-Sainte-Réparade', 'Carry-le-Rouet', 'La Roque-d\'Antheron', 'Meyrargues', 'Marignane', 'La Bouilladisse', 'Le Rove',
                              'Venelles', 'Cuges-les-Pins', 'Ensues-la-Redonne', 'La Penne-sur-Huveaune', 'Saint-Cyr-sur-Mer'];
            if(othersCp.includes(value)){
                this.setState({ city: {value: othersCity[othersCp.indexOf(value)], error: ''} });
            }
        }
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