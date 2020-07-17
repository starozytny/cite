import React, {Component} from 'react';
import axios from 'axios/dist/axios';
import Routing from '../../../../../../../../public/bundles/fosjsrouting/js/router.min.js';
import AjaxSend from '../../../components/functions/ajax_classique';
import {Input} from '../../../components/composants/Fields';
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
            error: ''
        }

        this.handleChange = this.handleChange.bind(this);
        this.handleEdit = this.handleEdit.bind(this);
        this.handleClose = this.handleClose.bind(this);
        this.handleSubmit = this.handleSubmit.bind(this);
    }

    handleChange (e) {
        this.setState({ [e.target.name]: {value: e.target.value} });
    }

    handleEdit (e) {
        let el = e.currentTarget;
        let min = parseInt(el.dataset.max) - parseInt(el.dataset.remaining);
        this.setState({openEdit: 'active', error: '', editId: el.dataset.id, editHoraire: el.dataset.horaire, 
        editMax: {value: el.dataset.max}, editRemaining: el.dataset.remaining, editMinim: min})
    }

    handleClose (e) {
        this.setState({openEdit: '', editId: '', editHoraire: '', editMax: {value: '', error: ''}, editRemaining: ''})
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
                    console.log(response)
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

    render () {
        const {slots, editMax, editHoraire, editMinim, openEdit, error} = this.state;

        let items = slots.map((elem, index) => {
            return <div className="slot-card" key={elem.id}>
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
                    {elem.remaining === elem.max ? <button className="btn-delete">Supprimer</button> : null}
                    <button className="btn-edit" onClick={this.handleEdit} data-id={elem.id} data-horaire={elem.horaireString} data-max={elem.max} data-remaining={elem.remaining}>Editer</button>
                </div>
            </div>
        })

        return <div className="slots">
            <div className="slots-cards">
                {items}
            </div>
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
    }
}