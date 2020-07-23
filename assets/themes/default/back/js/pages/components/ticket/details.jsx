import React, {Component} from 'react';
import axios from 'axios/dist/axios';
import Routing from '../../../../../../../../public/bundles/fosjsrouting/js/router.min.js';
import AjaxSend from '../../../components/functions/ajax_classique';
import {Input, Select} from '../../../components/composants/Fields';
import Validateur from '../../../components/functions/validate_input';
import Swal from 'sweetalert2';

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

export class Details extends Component {
    constructor(props){
        super(props)

        let creneaux = [{
            'value': 999,
            'libelle': 'Tous'
        }];
        JSON.parse(JSON.parse(this.props.prospects)).forEach((elem, index) => {
            creneaux.push({
                'value': elem.creneau.id,
                'libelle': elem.creneau.horaireString
            });
        })
        creneaux = creneaux.filter((thing, index, self) =>
            index === self.findIndex((t) => ( t.value === thing.value  ))
        )
        
        this.state = {
            prospects: JSON.parse(JSON.parse(this.props.prospects)),
            saveProspects: JSON.parse(JSON.parse(this.props.prospects)),
            saveCreneaux: creneaux,
            searched: {value: '', error: ''},
            selectHoraire: {value: '999', error: ''},
            selection: []
        }

        this.handleChangeStatus = this.handleChangeStatus.bind(this);
        this.handleChangeStatusSelection = this.handleChangeStatusSelection.bind(this);
        this.handleDelete = this.handleDelete.bind(this);
        this.handleDeleteSelection = this.handleDeleteSelection.bind(this);
        this.handleChange = this.handleChange.bind(this);

        this.handleSearch = this.handleSearch.bind(this);
        this.handleSelectHoraire = this.handleSelectHoraire.bind(this);
    }

    handleChange (e) {
        const {selection} = this.state;

        let value = e.target.value;
        let name = e.target.name;
        if(name === 'searched'){
            document.querySelectorAll("input[name='check-prospect']").forEach((el => el.checked = false))
            this.setState({ [name]: {value: value}, error: '', prospects: this.handleSearch(value), selection: [] });
        }else if(name === 'check-prospect'){
            let tmp = [{ id: value, check: e.target.checked }]
            let arr = selection;
            if(selection.length > 0){ arr = arr.filter(function(elem) { return elem.id != value }) }
            this.setState({selection: [...arr, ...tmp]})
        }else{
            document.querySelectorAll("input[name='check-prospect']").forEach((el => el.checked = false))
            this.setState({ [name]: {value: value}, error: '', searched:{value: ''}, prospects: this.handleSelectHoraire(value), selection: [] });
        }
    }

    handleSearch (value) {
        const {prospects, saveProspects, selectHoraire} = this.state;
        if(value != ""){
            return prospects.filter(function(elem){
                let val = value.toLowerCase();
                let firstname = elem.firstname.toLowerCase();
                let lastname = elem.lastname.toLowerCase();
                let numAdh = elem.numAdh ? elem.numAdh.toLowerCase() : "";
                if(firstname.indexOf(val) > -1 || lastname.indexOf(val) > -1 || numAdh.indexOf(val) > -1){ return elem; }                
            });
        }else{
            if(selectHoraire.value === "999"){
                return saveProspects;
            }else{
                return this.handleSelectHoraire(selectHoraire.value);
            }
        }        
    }

    handleSelectHoraire (value) {
        const {saveProspects} = this.state;
        if(value != "999"){
            return saveProspects.filter(function(elem){
                if(elem.creneau.id == value){ return elem; }                
            });
        }else{
            return saveProspects;       
        }
        
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

    render () {
        const {dayId} = this.props;
        const {prospects, searched, selectHoraire, saveCreneaux} = this.state;

        let items = prospects.map((elem, index) => {
            return <div className="item" key={elem.id}>
                <div className="col-0">
                    <input type="checkbox" name="check-prospect" value={elem.id} onChange={this.handleChange} />
                </div>
                <div className="col-1">
                    {elem.numAdh != null ? <div>#{elem.numAdh}</div> : null}
                    <div className="name">{elem.civility} {elem.firstname} <span>{elem.lastname}</span></div>
                    <div className="birthday">{(new Date(elem.birthday)).toLocaleDateString('fr-FR')} ({elem.age})</div>
                </div>
                <div className="col-2">
                    <div className="email">{elem.email}</div>
                    <div className="telephone">{formattedPhone(elem.phoneDomicile)}</div>
                    <div className="telephone">{formattedPhone(elem.phoneMobile)}</div>
                </div>
                <div className="col-3">
                    <div className="adresse">
                        <div>{elem.adr}, </div>
                        <div>{elem.cp} {elem.city}</div>
                    </div>
                </div>
                <div className="col-4">
                    <div className="horaire">{elem.creneau.horaireString}</div>
                </div>
                <div className="col-5">
                    <div className={"status status-" + elem.status} data-id={elem.id} onClick={elem.status == 1 || elem.status == 2 ? this.handleChangeStatus : null}>{elem.statusString}</div>
                </div>
                <div className="col-6">
                    <button className="btn-delete" data-id={elem.id} onClick={this.handleDelete}>Supprimer</button>
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
                        <a href={Routing.generate('admin_ticket_export', {'ticketDay': dayId})} download={"liste-" + dayId + ".csv"} className="btn btn-primary">Exporter pour Weezevent</a>
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
                    <div className="col-0"></div>
                    <div className="col-1">Identifiant</div>
                    <div className="col-2">Contact</div>
                    <div className="col-3">Adresse</div>
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
                            <button className="btn">Exporter EXCEL</button>
                        </div>
                    </div>
                </div>
            </div>
        </>
    }
}

function formattedPhone(elem){
    if(elem != "" && elem != undefined){
        let a = elem.substr(0,2);
        let b = elem.substr(2,2);
        let c = elem.substr(4,2);
        let d = elem.substr(6,2);
        let e = elem.substr(8,2);

        elem = a + " " + b + " " + c + " " + d + " " + e;
    }

    return elem;
}