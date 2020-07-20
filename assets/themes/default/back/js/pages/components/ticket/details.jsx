import React, {Component} from 'react';
import axios from 'axios/dist/axios';
import Routing from '../../../../../../../../public/bundles/fosjsrouting/js/router.min.js';
import AjaxSend from '../../../components/functions/ajax_classique';
import {Input} from '../../../components/composants/Fields';
import Validateur from '../../../components/functions/validate_input';
import Swal from 'sweetalert2';

export class Details extends Component {
    constructor(props){
        super(props)
        
        this.state = {
            prospects: JSON.parse(JSON.parse(this.props.prospects)),
            saveProspects: JSON.parse(JSON.parse(this.props.prospects)),
            searched: {value: '', error: ''}
        }

        this.handleChangeStatus = this.handleChangeStatus.bind(this);
        this.handleDelete = this.handleDelete.bind(this);
        this.handleChange = this.handleChange.bind(this);
    }

    handleChange (e) {
        const {prospects, saveProspects} = this.state;

        let value = e.target.value;

        

        if(value != ""){
           
            let arr = prospects.filter(function(elem){
                let val = value.toLowerCase();
                let firstname = elem.firstname.toLowerCase();
                let lastname = elem.lastname.toLowerCase();
                let numAdh = elem.numAdh ? elem.numAdh.toLowerCase() : "";
                if(firstname.indexOf(val) > -1 || lastname.indexOf(val) > -1 || numAdh.indexOf(val) > -1){
                    return elem;
                }                
            });
            this.setState({ [e.target.name]: {value: value}, error: '', prospects: arr});
        }else{
            this.setState({ [e.target.name]: {value: value}, error: '', prospects: saveProspects});
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
            confirmButtonText: 'Oui, je supprime'
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

                    Swal.fire(
                        'Supprimé !',
                        'Cet élément a été supprimé.',
                        'success'
                    );
                });
            }
          })
    }

    render () {
        const {dayId} = this.props;
        const {prospects, searched} = this.state;

        let items = prospects.map((elem, index) => {
            return <div className="item" key={elem.id}>
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
                        <Input type="text" identifiant="searched" value={searched.value} onChange={this.handleChange} error={searched.error} placeholder="Recherche"></Input>
                    </div>
                    <div className="item">
                        <a href={Routing.generate('admin_ticket_export', {'ticketDay': dayId})} download={"liste-" + dayId + ".csv"} className="btn btn-primary">Exporter pour Weezevent</a>
                    </div>
                </div>
            </div>
            
            <div className="prospects">
                {items.length <= 0 ? <div>Aucun enregistrement.</div> : <div className="prospects-header">
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