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
            openEdit: ''
        }

        this.handleChange = this.handleChange.bind(this);
        this.handleEdit = this.handleEdit.bind(this);
        this.handleClose = this.handleClose.bind(this);
    }

    handleChange (e) {
        const {editMax} = this.state
        let value = e.target.value
        let name = e.target.name

        this.setState({ [name]: {value: value} });
    }

    handleEdit (e) {
        let el = e.currentTarget;
        this.setState({openEdit: 'active', editId: el.dataset.id, editHoraire: el.dataset.horaire, editMax: {value: el.dataset.max}, editRemaining: el.dataset.remaining})
    }

    handleClose (e) {
        this.setState({openEdit: '', editId: '', editHoraire: '', editMax: {value: '', error: ''}, editRemaining: ''})
    }

    render () {
        const {slots, editMax, editHoraire, editRemaining, openEdit} = this.state;

        let items = slots.map((elem, index) => {
            return <div className="slot-card" key={elem.id}>
                <div className="slot-card-date">
                    <div>{elem.horaireString}</div>
                </div>
                <div className="slot-card-numbers">
                    <div className="slot-card-max">max : {elem.max}</div>
                    <div className="slot-card-remaining">reste : {elem.remaining}</div>
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
                    Editer {editHoraire}
                </div>
                <div className='minimum'>
                    Valeur minimum : {editRemaining}
                </div>
                <Input type="text" identifiant="editMax-" value={editMax.value} onChange={this.handleChange} error={editMax.error}>Max</Input>
                <div className="from-group">
                    <button className="btn btn-primary">Mettre à jour</button>
                </div>
            </div>
        </div>
    }
}