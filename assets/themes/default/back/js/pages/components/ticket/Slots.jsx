import React, {Component} from 'react';
import axios from 'axios/dist/axios';
import Routing from '../../../../../../../../public/bundles/fosjsrouting/js/router.min.js';
import AjaxSend from '../../../components/functions/ajax_classique';
import {Input, Select} from '../../../components/composants/Fields';
import Validateur from '../../../components/functions/validate_input';
import Swal from 'sweetalert2';

export class Slots extends Component {
    constructor(props){
        super(props)

        this.state = {
            dayId: this.props.dayId,
            slots: JSON.parse(JSON.parse(this.props.slots)),
            editId: '',
            editHoraire: '',
            editMax: {value: '', error: ''},
            editRemaining: '',
            editMinim: '',
            openEdit: '',
            error: '',
            addHours: {value: '', error: ''},
            addMinutes: {value: '0', error: ''},
            addMax: {value: '20', error: ''}
        }

        this.handleChange = this.handleChange.bind(this);
        this.handleEdit = this.handleEdit.bind(this);
        this.handleClose = this.handleClose.bind(this);
        this.handleSubmit = this.handleSubmit.bind(this);
        this.handleDelete = this.handleDelete.bind(this);
        this.handleAdd = this.handleAdd.bind(this);
        this.handleSubmitAdd = this.handleSubmitAdd.bind(this);
    }

    handleChange (e) {
        this.setState({ [e.target.name]: {value: e.target.value}, error: '' });
    }

    handleEdit (e) {
        let el = e.currentTarget;
        let min = parseInt(el.dataset.max) - parseInt(el.dataset.remaining);
        this.setState({openEdit: 'active', error: '', editId: el.dataset.id, editHoraire: el.dataset.horaire, 
        editMax: {value: el.dataset.max}, editRemaining: el.dataset.remaining, editMinim: min})
    }

    handleAdd (e) {
        this.setState({openAdd: 'active', error: ''});
    }

    handleClose (e) {
        this.setState({openEdit: '', openAdd: '', editId: '', editHoraire: '', editMax: {value: '', error: ''}, editRemaining: '', addHours: {value: '', error: ''}, addMinutes: {value: '', error: ''}, addMax: {value: '', error: ''}})
    }

    handleSubmit (e) {
        e.preventDefault();
        const {dayId, editId, slots, editMax, editMinim} = this.state;

        let validate = Validateur.validateur([
            {type: "text", id: 'editMax', value: editMax.value},
        ]);

        if(!validate.code){
            this.setState(validate.errors);
        }else{
            if(parseInt(editMax.value) < parseInt(editMinim)){
                this.setState({editMax: {value: editMax.value, error: 'La valeur doit être supérieur à ' + editMinim}})
            }else{
                AjaxSend.loader(true);
                let self = this;
                axios({ 
                    method: 'post', 
                    url: Routing.generate('admin_ticket_slot_update', { 'ticketDay' : dayId }), 
                    data: {slotId: editId, max: editMax.value} 
                }).then(function (response) {
                    let data = response.data; let code = data.code; AjaxSend.loader(false);
                    if(code === 1){
                        let arr = [];
                        slots.forEach((elem) => {
                            if(elem.id == editId){ elem.max = parseInt(editMax.value) ; elem.remaining = parseInt(data.remaining)}
                            arr.push(elem);
                        })
                        self.setState({error: '', slots: arr, openEdit: ''})
                    }else{
                        self.setState({error: data.message})
                    }
                });
            }
        }
    }

    handleDelete (e) {
        e.preventDefault()

        const {dayId, slots} = this.state;

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
                    url: Routing.generate('admin_ticket_slot_delete', { 'ticketDay' : dayId, 'slot' : id})
                }).then(function (response) {
                    let data = response.data; let code = data.code; AjaxSend.loader(false);

                    if(code === 1){
                        let arr = slots.filter((elem) => { return elem.id != id })
                        self.setState({slots: arr})
                        Swal.fire( 'Supprimé !',data.message,'success' );
                    }else{
                        Swal.fire( 'Erreur !', data.message, 'error' );
                    }
                    
                });
            }
        })
    }

    handleSubmitAdd (e) {
        e.preventDefault();

        const {dayId, addHours, addMinutes, addMax, slots} = this.state;

        let validate = Validateur.validateur([
            {type: "text", id: 'addHours', value: addHours.value},
            {type: "text", id: 'addMax', value: addMax.value},
        ]);

        if(!validate.code){
            this.setState(validate.errors);
        }else{
            AjaxSend.loader(true);
            let self = this;
            axios({ 
                method: 'post', 
                url: Routing.generate('admin_ticket_slot_add', { 'ticketDay' : dayId }), 
                data: {hours: addHours.value, minutes: addMinutes.value, max: addMax.value} 
            }).then(function (response) {
                let data = response.data; let code = data.code; AjaxSend.loader(false);
                if(code === 1){
                    self.setState({slots: JSON.parse(data.slots)});
                }else{
                    self.setState({error: data.message})
                }                
            });
        }
    }

    render () {
        const {slots, editMax, editHoraire, editMinim, openEdit, error, openAdd, addHours, addMinutes, addMax} = this.state;

        let items = slots.map((elem, index) => {
            return <div className="slot-card" key={elem.id}>
                {elem.remaining == 0 ? <div className="slot-card-full">Complet</div> : null }
                <div className="slot-card-date">
                    <div>{elem.horaireString}</div>
                </div>
                <div className="slot-card-numbers">
                    <div className="slot-card-max">
                        <div>max : {elem.max}</div>
                        <div>reste : {elem.remaining}</div>
                    </div>
                    <div className="slot-card-remaining">participants : {elem.max - elem.remaining}</div>
                </div>
                <div className="slot-card-actions">
                    {elem.remaining === elem.max ? <button className="btn-delete" onClick={this.handleDelete} data-id={elem.id}>Supprimer</button> : null}
                    <button className="btn-edit" onClick={this.handleEdit} data-id={elem.id} data-horaire={elem.horaireString} data-max={elem.max} data-remaining={elem.remaining}>Editer</button>
                </div>
            </div>
        })

        const itemsMinutes = [
            {'value': 0, 'libelle': "00"},
            {'value': 15, 'libelle': "15"},
            {'value': 30, 'libelle': "30"},
            {'value': 45, 'libelle': "45"}
        ]

        return <>
            <div className="toolbar">
                <div className="toolbar-left">
                    <div className="item">
                        <button className="btn btn-primary" onClick={this.handleAdd}>Ajouter un créneau</button>
                    </div>
                </div>
            </div>
            <div className="slots">
                <div className="slots-cards">
                    {items}
                </div>
                <div className="slots-aside">
                    <div className="slots-aside-edit">
                        <div className={"slots-edit-overlay " + openEdit} onClick={this.handleClose}></div>
                        <div className={"slots-edit " + openEdit}>
                            <div className="title">
                                <div>Editer {editHoraire}</div>
                                <div><span className="icon-close-circle" onClick={this.handleClose}></span></div>
                            </div>
                            {error != "" ? <div className="alert alert-danger">{error}</div> : null}
                            <div className='minimum'>
                                Valeur min acceptée : {parseInt(editMinim)}
                            </div>
                            <form onSubmit={this.handleSubmit}>
                                <Input type="number" identifiant="editMax" value={editMax.value} onChange={this.handleChange} error={editMax.error}>Max</Input>
                                <div className="from-group">
                                    <button className="btn btn-primary" type="submit">Mettre à jour</button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div className="slots-aside-add">
                        <div className={"slots-add-overlay " + openAdd} onClick={this.handleClose}></div>
                        <div className={"slots-add " + openAdd}>
                            <div className="title">
                                <div>Ajouter un créneau</div>
                                <div><span className="icon-close-circle" onClick={this.handleClose}></span></div>
                            </div>
                            {error != "" ? <div className="alert alert-danger">{error}</div> : null}
                            <form onSubmit={this.handleSubmitAdd}>
                                <div className="inputSlot">
                                    <Input type="number" identifiant="addHours" value={addHours.value} onChange={this.handleChange} error={addHours.error}>Heure</Input>
                                    <div className="separator-minutes">
                                        <span> : </span>
                                    </div>
                                    <Select value={addMinutes.value} identifiant="addMinutes" onChange={this.handleChange} error={addMinutes.error} items={itemsMinutes}>Minutes</Select>
                                </div>
                                <Input type="number" identifiant="addMax" value={addMax.value} onChange={this.handleChange} error={addMax.error}>Max</Input>
                                <div className="from-group">
                                    <button className="btn btn-primary" type="submit">Ajouter</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </>
    }
}