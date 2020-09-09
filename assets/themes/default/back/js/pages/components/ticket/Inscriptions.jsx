import React, {Component} from 'react';
import toastr from 'toastr';
import axios from 'axios/dist/axios';
import Routing from '../../../../../../../../public/bundles/fosjsrouting/js/router.min.js';
import AjaxSend from '../../../components/functions/ajax_classique';
import {Input, Select} from '../../../components/composants/Fields';
import Validateur from '../../../components/functions/validate_input';
import Swal from 'sweetalert2';
import {Page} from '../../../components/composants/page/Page';
import {AsideProspect} from './Details';

function updateProspect(tab, data){
    let arr = [];
    tab.forEach((elem) => {
        if(parseInt(elem.id) === parseInt(data.id)){
            elem = data;
        }
        arr.push(elem);
    })
    return arr;
}

function updateDiff(tab, data, diff){
    let arr = [];
    tab.forEach((elem) => {
        data.forEach((item) => {
            if(parseInt(elem.id) === parseInt(item)){
                elem.isDiff = diff;
            }
        })
        arr.push(elem);
    })
    
    return arr;
}

export class Inscriptions extends Component {
    constructor(props){
        super(props)
       
        let prospects = JSON.parse(JSON.parse(this.props.prospects))
        let prospectsImmuable = prospects;

        let dayTypes = [];
        prospects.forEach((elem, index) => {
            dayTypes.push({
                'value': elem.day.type,
                'libelle': 'Journée des ' + elem.day.typeString
            });
        })
        dayTypes = dayTypes.filter((thing, index, self) =>
            index === self.findIndex((t) => ( t.value === thing.value  ))
        )

        prospects = prospects.filter(function(elem){
            if(elem.day.type == dayTypes[0].value){ return elem; }                
        }); 

        let prospectsList = prospects.slice(0, 20);

        this.state = {
            prospectsImmuable: prospectsImmuable,
            prospects: prospects,
            prospectsList: prospectsList,
            tailleList: prospects.length,
            dayTypes: dayTypes,
            selectDayType: {value: dayTypes[0] != undefined ? dayTypes[0].value : '', error: ''},
            searched: {value: '', error: ''},
            openEdit: '',
            prospectEdit: null,
            responsableIdEdit: null,
            errorEdit: ''
        }

        this.handleChangeStatus = this.handleChangeStatus.bind(this);
        this.handleChange = this.handleChange.bind(this);
        this.handleSelectDayType = this.handleSelectDayType.bind(this);
        this.handleSearch = this.handleSearch.bind(this);
        this.handleUpdateList = this.handleUpdateList.bind(this);
        this.handleOpenEdit = this.handleOpenEdit.bind(this);
        this.handleEditProspect = this.handleEditProspect.bind(this);
        this.handleClose = this.handleClose.bind(this);
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
                let prospect = JSON.parse(data.prospect)
                let prospectEdit = JSON.parse(data.prospectEdit)
                self.setState({
                    prospects: updateProspect(self.state.prospects, prospect),
                    prospectsImmuable: updateProspect(self.state.prospectsImmuable, prospect),
                    prospectsList: updateProspect(self.state.prospectsList, prospect),
                    prospectEdit: prospectEdit
                })
            }else{
                self.setState({ errorEdit: data.message });
            }
        });
    }

    handleChange (e) {
        let value = e.target.value;
        let name = e.target.name;

        if(name === 'searched'){
            this.handleSearch(value)
            this.setState({ [name]: {value: value, error: ''} })
        }else{
            let newR = this.handleSelectDayType(value);
            this.setState({ [name]: {value: value, error: ''}, searched:{value: ''}, prospectsList: newR.slice(0, 20), prospects: newR, tailleList: newR.length });
        }
       
    }

    handleSearch (value) {
        let self = this
        let newItems = this.state.prospectsImmuable.filter(function(elem) {
            let val = value.toLowerCase();
            let lastname = elem.lastname.toLowerCase();
            if(lastname.indexOf(val) > -1 && self.state.selectDayType.value == elem.day.type){ return elem; }
        })
        let newList = newItems.slice(0, 12)
        this.setState({ prospectsList: newList, prospects: newItems, tailleList: newItems.length })
    }

    handleSelectDayType (value) {
        const {prospectsImmuable} = this.state;
        return prospectsImmuable.filter(function(elem){
            if(elem.day.type == value){ return elem; }                
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
                       
            if(code == 1){
                let prospect = JSON.parse(data.prospect)
                self.setState({
                    prospects: updateProspect(self.state.prospects, prospect),
                    prospectsImmuable: updateProspect(self.state.prospectsImmuable, prospect),
                    prospectsList: updateProspect(self.state.prospectsList, prospect),
                })

                let existent = JSON.parse(data.existent)
                let diff = data.diff == 0 ? false : true

                self.setState({
                    prospects: updateDiff(self.state.prospects, existent, diff),
                    prospectsImmuable: updateDiff(self.state.prospectsImmuable, existent, diff),
                    prospectsList: updateDiff(self.state.prospectsList, existent, diff),
                })

                toastr.info('Changement validé')
            }else{
                toastr.error('Erreur')
            }
            
        });
    }

    handleUpdateList (dataList) {
        this.setState({ prospectsList: dataList })
    }    

    render () {
        const {prospects, prospectsList, selectDayType, dayTypes, searched, tailleList, openEdit, prospectEdit, errorEdit, responsableIdEdit} = this.state;

        let eleves = prospectsList.map((elem) => {
            return <div className={"item" + (elem.isDiff != 0 && elem.status != 2 ? ' item-rayer' : '')} key={elem.id}>
                <div className="col-1">
                    <div className="name" data-id={elem.id} onClick={this.handleOpenEdit}><span>{elem.lastname}</span> {elem.firstname}</div>
                    <div className="sexe">{elem.civility == "Mr" ? <span>Homme</span> : <span>Femme</span>}</div>
                    <div className="birthday">{(new Date(elem.birthday)).toLocaleDateString('fr-FR')} ({elem.age})</div>
                </div>
                <div className="col-2">
                    <div className="email">{elem.responsable.civility}. <span>{elem.responsable.lastname}</span> {elem.responsable.firstname}</div>
                    <div className="telephone">{elem.responsable.email}</div>
                </div>
                <div className="col-3">
                    <div className={"status status-" + elem.status} data-id={elem.id} onClick={elem.status == 1 || elem.status == 2 ? this.handleChangeStatus : null}>{elem.statusString}</div>
                </div>
            </div>
        })

        return <>
            <div className="toolbar">
                <div className="toolbar-left">
                    <div className="item item-select">
                        <Select value={selectDayType.value} identifiant="selectDayType" onChange={this.handleChange} error={selectDayType.error} items={dayTypes}></Select>
                    </div>
                </div>
                <div className="toolbar-right">
                    <div className="item item-search">
                        <Input type="text" identifiant="searched" value={searched.value} onChange={this.handleChange} error={searched.error} placeholder="Recherche nom"></Input>
                    </div>
                </div>
            </div>

            <div className="prospects prospects-inscriptions">
                <div className="prospects-header">
                    <div className="col-1">Identifiant</div>
                    <div className="col-2">Responsable</div>
                    <div className="col-3">Est inscrit</div>
                </div>
                <div className="prospects-body">
                    <div className="line-resp">
                        {eleves}
                    </div>
                </div>
                <Page havePagination="true" taille={tailleList} itemsPagination={prospects} perPage="20" onUpdate={this.handleUpdateList} />
                {openEdit == 'active' ? <AsideProspect special={true} error={errorEdit} openEdit={openEdit} onClose={this.handleClose} prospect={prospectEdit} onEdit={this.handleEditProspect} responsableIdEdit={responsableIdEdit} /> : null}
            </div>
        </>
    }
}
