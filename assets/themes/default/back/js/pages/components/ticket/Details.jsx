import React, {Component} from 'react';
import axios from 'axios/dist/axios';
import Routing from '../../../../../../../../public/bundles/fosjsrouting/js/router.min.js';
import AjaxSend from '../../../components/functions/ajax_classique';
import {Input, Select} from '../../../components/composants/Fields';
import Validateur from '../../../components/functions/validate_input';
import Swal from 'sweetalert2';
import DatePicker from "react-datepicker";
import { registerLocale, setDefaultLocale } from  "react-datepicker";
import fr from 'date-fns/locale/fr';
registerLocale('fr', fr)
import "react-datepicker/dist/react-datepicker.css";
import {ResendTicket} from './Responsable.jsx';

function getSelectionChecked(selection){
    let oneChecked = false;
    let arr = [];
    selection.forEach(element => { 
        if(element.check){
            arr.push(element.id);
            return oneChecked = true;
        } 
    });
    
    return oneChecked ? arr : false;
}

function formattedPhone(elem){
    if(elem != "" && elem != undefined){
        let arr = elem.match(/[0-9-+]/g);
        if(arr != null){
            elem = arr.join('');
            if(!(/^((\+)33|0)[1-9](\d{2}){4}$/.test(elem))){
                return elem;
            }else{
                let a = elem.substr(0,2);
                let b = elem.substr(2,2);
                let c = elem.substr(4,2);
                let d = elem.substr(6,2);
                let e = elem.substr(8,2);
        
                return a + " " + b + " " + c + " " + d + " " + e;
            }
        }
    }else{
        return "";
    }
}

export class Details extends Component {
    constructor(props){
        super(props)

        let creneaux = [];
        JSON.parse(JSON.parse(this.props.prospects)).forEach((elem, index) => {
            creneaux.push({
                'value': elem.creneau.id,
                'libelle': elem.creneau.horaireString
            });
        })
        creneaux = creneaux.filter((thing, index, self) =>
            index === self.findIndex((t) => ( t.value === thing.value  ))
        )

        let oriProspects = JSON.parse(JSON.parse(this.props.prospects));
        let horaireProspects = oriProspects.filter(function(elem){
            if(elem.creneau.id == creneaux[0].value){ return elem; }                
        });     
        
        this.state = {
            prospects: horaireProspects,
            saveProspects: oriProspects,
            horaireProspects: horaireProspects,
            saveCreneaux: creneaux,
            searched: {value: '', error: ''},
            selectHoraire: {value: creneaux[0] != undefined ? creneaux[0].value : '', error: ''},
            selection: [],
            openEdit: '',
            prospectEdit: null,
            responsableIdEdit: null,
            idEdit: null,
            errorEdit: ''
        }

        this.handleChangeStatus = this.handleChangeStatus.bind(this);
        this.handleChangeStatusSelection = this.handleChangeStatusSelection.bind(this);
        this.handleDelete = this.handleDelete.bind(this);
        this.handleDeleteSelection = this.handleDeleteSelection.bind(this);
        this.handleChange = this.handleChange.bind(this);

        this.handleSearch = this.handleSearch.bind(this);
        this.handleSelectHoraire = this.handleSelectHoraire.bind(this);

        this.handleOpenEdit = this.handleOpenEdit.bind(this);
        this.handleClose = this.handleClose.bind(this);
        this.handleEditProspect = this.handleEditProspect.bind(this);
    }

    handleChange (e) {
        const {selection} = this.state;

        let value = e.target.value;
        let name = e.target.name;
        let allCheck = document.querySelectorAll("input[name='check-prospect']");
        let arr = selection;

        if(name === 'searched') {
            allCheck.forEach((el => el.checked = false))
            this.setState({ [name]: {value: value}, error: '', prospects: this.handleSearch(value), selection: [] });
        }else if(name === 'check-prospect') {
            let tmp = [{ id: value, check: e.target.checked }]
            let arr = selection;
            if(selection.length > 0){ arr = arr.filter(function(elem) { return elem.id != value }) }
            this.setState({selection: [...arr, ...tmp]})
        }else if(name === 'check-prospect-all') {
            if(e.target.checked){
                let fill = [];
                allCheck.forEach(function(el) {
                    el.checked = true;
                    fill.push({ id: el.value, check: true })
                })
                this.setState({selection: fill})
            }else{
                allCheck.forEach(function(el) { el.checked = false })
                this.setState({selection: []})
            }
            
        }else{
            allCheck.forEach((el => el.checked = false))
            let newP = this.handleSelectHoraire(value);
            this.setState({ [name]: {value: value}, error: '', searched:{value: ''}, prospects: newP, horaireProspects: newP, selection: [] });
        }
    }

    handleSearch (value) {
        const {horaireProspects} = this.state;
        if(value != ""){
            return horaireProspects.filter(function(elem){
                let val = value.toLowerCase();
                let firstname = elem.firstname.toLowerCase();
                let lastname = elem.lastname.toLowerCase();
                let numAdh = elem.numAdh ? elem.numAdh.toLowerCase() : "";
                if(firstname.indexOf(val) > -1 || lastname.indexOf(val) > -1 || numAdh.indexOf(val) > -1){ return elem; }                
            });
        }else{
            return this.handleSelectHoraire(selectHoraire.value);
        }        
    }

    handleSelectHoraire (value) {
        const {saveProspects} = this.state;
        return saveProspects.filter(function(elem){
            if(elem.creneau.id == value){ return elem; }                
        });        
    }

    handleChangeStatus (e) {
        let id = parseInt(e.currentTarget.dataset.id);

        AjaxSend.loader(true);
        let self = this;
        axios({ 
            method: 'post', 
            url: Routing.generate('admin_prospect_update_status', { 'id' : id })
        }).then(function (response) {
            let data = response.data; let code = data.code; AjaxSend.loader(false);
            
            let arr = [];
            self.state.prospects.forEach((elem) => {
                if(parseInt(elem.id) === parseInt(id)){
                    elem.status = data.status;
                    elem.statusString = data.statusString;
                }
                arr.push(elem);
            })

            self.setState({prospects: arr});
        });
    }

    handleDelete (e) {
        let id = e.currentTarget.dataset.id;

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
                AjaxSend.loader(true);
                let self = this;
                axios({ 
                    method: 'post', 
                    url: Routing.generate('admin_prospect_delete', { 'id' : id })
                }).then(function (response) {
                    let data = response.data; let code = data.code; AjaxSend.loader(false);
                    
                    let arr = self.state.prospects.filter((elem, index) => {
                        return parseInt(elem.id) != parseInt(id)
                    })

                    self.setState({prospects: arr});
                });
            }
          })
    }

    handleChangeStatusSelection (e) {
        const {selection} = this.state;

        if(selection.length > 0){
            let arr = getSelectionChecked(selection);

            if(arr != false){
                AjaxSend.loader(true);
                let self = this;
                axios({ 
                    method: 'post', 
                    url: Routing.generate('admin_prospect_update_status_selection'),
                    data: { selection: arr }
                }).then(function (response) {
                    let data = response.data; let code = data.code; AjaxSend.loader(false);

                    let arr = [];
                    self.state.prospects.forEach((elem) => {
                        data.prospects.forEach((element) => {
                            if(parseInt(elem.id) === parseInt(element.id)){
                                elem.status = element.status;
                                elem.statusString = element.statusString;
                            }
                        })
                        arr.push(elem);
                    })
                    self.setState({prospects: arr});
                });
            }
        }
    }

    handleDeleteSelection (e) {
        const {selection} = this.state;

        if(selection.length > 0){
            let arr = getSelectionChecked(selection);

            if(arr != false){


                Swal.fire({
                    title: 'Etes-vous sur ?',
                    text: "La suppression des élèves sélectionnés est irréversible.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Oui, je supprime',
                    cancelButtonText: "Non",
                  }).then((result) => {
                    if (result.value) {

                        AjaxSend.loader(true);
                        let self = this;
                        axios({ 
                            method: 'post', 
                            url: Routing.generate('admin_prospect_delete_selection'),
                            data: { selection: arr }
                        }).then(function (response) {
                            let data = response.data; let code = data.code; AjaxSend.loader(false);
                            let newArr = self.state.prospects;
                            arr.forEach((element) => {
                                newArr.forEach((elem, index) => {
                                    if(elem.id == parseInt(element)){
                                        newArr.splice(index,1)
                                    }
                                })
                            })
        
                            self.setState({prospects: newArr});
                        });
                    }
                  })
            }
        }
    }

    handleOpenEdit (e) {
        let id = parseInt(e.currentTarget.dataset.id);

        AjaxSend.loader(true);
        let self = this;
        axios({ 
            method: 'post', 
            url: Routing.generate('admin_prospect_get_infos', { 'id' : id })
        }).then(function (response) {
            let data = response.data; let code = data.code; AjaxSend.loader(false);
            self.setState({openEdit: 'active', prospectEdit: JSON.parse(data.prospect), idEdit: id, responsableIdEdit: JSON.parse(data.prospect).responsable.id})
        });

    }

    handleClose (e) {
        this.setState({openEdit: ''})
    }

    handleEditProspect (data) {

        const {responsableIdEdit, idEdit} = this.state;

        AjaxSend.loader(true);
        let self = this;
        axios({ 
            method: 'post', 
            url: Routing.generate('admin_prospect_set_infos', { 'id' : idEdit }),
            data: {prospect: data}
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
                            method: 'post',  url: Routing.generate('admin_ticket_send', {'id': responsableIdEdit}) 
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

    render () {
        const {dayId} = this.props;
        const {prospects, searched, selectHoraire, saveCreneaux, openEdit, prospectEdit, errorEdit, responsableIdEdit} = this.state;

        let items = prospects.map((elem, index) => {
            return <div className="item" key={elem.id}>
                <div className="col-0">
                    <input type="checkbox" name="check-prospect" value={elem.id} onChange={this.handleChange} />
                </div>
                <div className="col-1">
                    {elem.numAdh != null ? 
                        <div className="haveNumAdh">
                            <div className="numAdh">{elem.numAdh}</div>
                        </div>
                        : null}
                    <div className="name" onClick={this.handleOpenEdit} data-id={elem.id}>{elem.civility}. {elem.firstname} <span>{elem.lastname}</span></div>
                    <div className="birthday">{(new Date(elem.birthday)).toLocaleDateString('fr-FR')} ({elem.age})</div>
                </div>
                <div className="col-2">
                    <div className="email">{elem.email}</div>
                    <div className="telephone">{formattedPhone(elem.phoneDomicile)}</div>
                    <div className="telephone">{formattedPhone(elem.phoneMobile)}</div>
                </div>
                <div className="col-3">
                    <div className="adresse">
                        <a href={Routing.generate('admin_responsable_edit', {'responsable': elem.responsable.id})}>{elem.responsable.civility}. {elem.responsable.firstname} {elem.responsable.lastname}</a>
                        {/* {elem.numAdh != null ? 
                        <div className="haveNumAdh">
                            <div className="haveNumAdh-status">
                                {elem.isDiff ? <span className="icon-warning"></span> : null}
                            </div>
                        </div>
                        : null} */}
                    </div>
                </div>
                <div className="col-4">
                    <div className="horaire">{elem.creneau.horaireString}</div>
                </div>
                <div className="col-5">
                    <div className={"status status-" + elem.status} data-id={elem.id} onClick={elem.status == 1 || elem.status == 2 ? this.handleChangeStatus : null}>{elem.statusString}</div>
                </div>
                <div className="col-6">
                    <button className="btn-edit" onClick={this.handleOpenEdit} data-id={elem.id}>
                        <span className="icon-edit"></span>
                    </button>
                    <button className="btn-delete" data-id={elem.id} onClick={this.handleDelete}>
                        <span className="icon-trash"></span>
                    </button>
                </div>
            </div>
        })
        
        return <>
            <div className="toolbar">
                <div className="toolbar-left">
                    <div className="item">
                        <a href={Routing.generate('admin_ticket_index')} className="btn">Retour à la liste</a>
                    </div>
                    <div className="item">
                        <a href={Routing.generate('admin_ticket_history', {'ticketDay': dayId})} className="btn">Historique</a>
                    </div>
                </div>
                <div className="toolbar-right">
                    <div className="item">
                        <a href={Routing.generate('admin_ticket_export_weezevent', {'ticketDay': dayId})} download={"liste-" + dayId + ".csv"} className="btn btn-secondary">Weezevent</a>
                    </div>
                </div>
            </div>

            <div className="toolbar">
                    <div className="toolbar-left">
                        <div className="item item-select">
                            <Select value={selectHoraire.value} identifiant="selectHoraire" onChange={this.handleChange} error={selectHoraire.error} items={saveCreneaux}></Select>
                        </div>
                    </div>
                    <div className="toolbar-right">
                        <div className="item item-search">
                            <Input type="text" identifiant="searched" value={searched.value} onChange={this.handleChange} error={searched.error} placeholder="Recherche"></Input>
                        </div>
                    </div>
                </div>
            
            <div className="prospects">
                {items.length <= 0 ? <div>Aucun enregistrement.</div> : <div className="prospects-header">
                    <div className="col-0"><input type="checkbox" name="check-prospect-all" onChange={this.handleChange} /></div>
                    <div className="col-1">Identifiant</div>
                    <div className="col-2">Contact</div>
                    <div className="col-3">Responsable</div>
                    <div className="col-4">Horaire</div>
                    <div className="col-5">Status</div>
                    <div className="col-6"></div>
                </div>}
                <div className="prospects-body">
                    {items}
                </div>
                <div className="prospects-footer">
                    <div className="prospects-footer-left">
                        <div className="item item-action">
                            <button className="action" onClick={this.handleChangeStatusSelection}><span>Changer status</span></button>
                            <button className="action" onClick={this.handleDeleteSelection}><span>Supprimer</span></button>
                        </div>
                    </div>
                    <div className="prospects-footer-right">
                        <div className="item">
                        <a href={Routing.generate('admin_ticket_export_eleves', {'ticketDay': dayId})} download={"eleves-" + dayId + ".xlsx"} className="btn">Exporter EXCEL</a>
                        </div>
                    </div>
                </div>
            </div>
            
            {openEdit == 'active' ? <AsideProspect error={errorEdit} openEdit={openEdit} onClose={this.handleClose} prospect={prospectEdit} onEdit={this.handleEditProspect} responsableIdEdit={responsableIdEdit} /> : null}
            
        </>
    }
}

export class AsideProspect extends Component {
    constructor (props){
        super(props);

        this.state = {
            error: '',
            civility: {value: props.prospect.civility, error: ''},
            firstname: {value: props.prospect.firstname, error: ''},
            lastname: {value: props.prospect.lastname, error: ''},
            birthday: {value: props.prospect.birthdayString, error: '', inputVal: new Date(props.prospect.birthdayJavascript)},
            numAdh: {value: props.prospect.numAdh == null ? '' : props.prospect.numAdh, error: ''},
            email: {value: props.prospect.email == null ? '' : props.prospect.email, error: ''},
            phoneMobile: {value: props.prospect.phoneMobile == null ? '' : props.prospect.phoneMobile, error: ''},
        }

        this.handleSubmit = this.handleSubmit.bind(this);
        this.handleChange = this.handleChange.bind(this);
        this.handleDate = this.handleDate.bind(this);
    }

    handleDate (e) {
        this.setState({ birthday: {inputVal: e, value: new Date(e).toLocaleDateString()} });
    }

    handleChange (e) {
        let name = e.target.name;
        let value = e.target.value;
        this.setState({ [name]: {value: value} });
    }

    handleSubmit (e) {
        e.preventDefault();

        const {civility, firstname, lastname, birthday, numAdh, email, phoneMobile} = this.state;

        let validate = Validateur.validateur([
            {type: "text", id: 'firstname', value: firstname.value},
            {type: "text", id: 'lastname', value: lastname.value},
            {type: "text", id: 'birthday', value: birthday.value},
            {type: "email", id: 'email', value: email.value},
            {type: "phone", id: 'phoneMobile', value: phoneMobile.value},
        ]);

        if(!validate.code){
            this.setState(validate.errors);
        }else{
            let data = {
                civility: civility.value,
                firstname: firstname.value,
                lastname: lastname.value,
                email: email.value,
                birthday: birthday.value,
                phoneMobile: phoneMobile.value,
                numAdh: numAdh.value,
            }
            this.props.onEdit(data);
        }
    }


    render () {
        const {openEdit, onClose, prospect, error, responsableIdEdit} = this.props;
        const {civility, firstname, lastname, birthday, numAdh, email, phoneMobile} = this.state;

        return <div className="prospect-aside">
        <div className="prospect-aside-edit">
            <div className={"prospect-edit-overlay " + openEdit} onClick={onClose}></div>
            <div className={"prospect-edit " + openEdit}>
                <div className="title">
                    <div>{prospect.civility}. {prospect.firstname} {prospect.lastname}</div>
                    <div><span className="icon-close-circle" onClick={onClose}></span></div>
                </div>

                <div className="informations">
                    <div>Ticket : <b>{prospect.responsable.ticket}</b></div>
                    <div>Créé le : {prospect.responsable.createAtString}</div>
                    <br/>
                    <div>Responsable : 
                        <ul>
                            <li>{prospect.responsable.civility}. {prospect.responsable.firstname} {prospect.responsable.lastname}</li>
                            <li>{prospect.responsable.email}</li>
                            <li>{formattedPhone(prospect.responsable.phoneMobile)} {formattedPhone(prospect.responsable.phoneDomicile)}</li>
                            <li>{prospect.responsable.adresseString}</li>
                        </ul>
                    </div>
                    <a href={Routing.generate('admin_responsable_edit', {'responsable': prospect.responsable.id})} className="edit-resp">Modifier le responsable</a>
                    <ResendTicket responsableId={responsableIdEdit}/>
                </div>

                <hr/>
                
                <form onSubmit={this.handleSubmit}>
                    <h3>Modifier l'élève</h3>

                    {error != "" ? <div className="alert alert-danger">{error}</div> : null}

                    <div className="line">
                        <RadioCivility civility={civility} onChange={this.handleChange}/>
                    </div>

                    <div className="line line-2">
                        <Input type="text" identifiant="firstname" value={firstname.value} onChange={this.handleChange} error={firstname.error}>Prénom</Input>
                        <Input type="text" identifiant="lastname" value={lastname.value} onChange={this.handleChange} error={lastname.error}>Nom</Input>
                    </div>

                    <div className="line line-2">
                        <div className={'form-group-date form-group' + (birthday.error ? " form-group-error" : "")}>
                            <label>Date anniversaire</label>
                            <DatePicker
                                locale="fr"
                                selected={birthday.inputVal}
                                onChange={this.handleDate}
                                dateFormat="dd/MM/yyyy"
                                peekNextMonth
                                showMonthDropdown
                                showYearDropdown
                                dropdownMode="select"
                                placeholderText="DD/MM/YYYY"
                                />
                            <div className='error'>{birthday.error ? birthday.error : null}</div>
                        </div>
                        <Input type="text" identifiant="numAdh" value={numAdh.value} onChange={this.handleChange} placeholder="(facultatif)" error={numAdh.error}>Numéro adhérent</Input>
                    </div> 

                    <div className="line line-2">
                        <Input type="text" identifiant="email" value={email.value} onChange={this.handleChange} error={email.error}>Adresse e-mail</Input>
                        <Input type="number" identifiant="phoneMobile" value={phoneMobile.value} onChange={this.handleChange} error={phoneMobile.error}>Téléphone mobile</Input>
                    </div>

                    <div className="alert alert-infos">
                        Après modification, assurez-vous de <b>renvoyer</b> le ticket au responsable.
                    </div>

                    <div className="from-group">
                        <button className="btn btn-primary" type="submit">Mettre à jour</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
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