import React, {Component} from 'react';
import axios from 'axios/dist/axios';
import Routing from '../../../../../../../../public/bundles/fosjsrouting/js/router.min.js';
import AjaxSend from '../../../components/functions/ajax_classique';
import Validateur from '../../../components/functions/validate_input';
import DatePicker from "react-datepicker";
import { registerLocale, setDefaultLocale } from  "react-datepicker";
import fr from 'date-fns/locale/fr';
registerLocale('fr', fr)
import "react-datepicker/dist/react-datepicker.css";

export class Ticket extends Component {
    constructor (props){
        super(props);

        this.state = {
            staticDate : new Date(this.props.dateOpen),
            dateOpen: {value: '', error: '', inputVal: new Date(this.props.dateOpen)},
            openEdit: '',
            error: ''
        }

        this.handleChange = this.handleChange.bind(this);
        this.handleClick = this.handleClick.bind(this);
        this.handleClose = this.handleClose.bind(this);
        this.handleSubmit = this.handleSubmit.bind(this);
    }

    handleChange (e) {
        this.setState({ dateOpen: {inputVal: e, value: new Date(e).toLocaleDateString()} });
    }

    handleClick (e) {
        this.setState({openEdit: 'active', error: ''});
    }

    handleClose (e) {
        this.setState({openEdit: '', error: ''});
    }

    handleSubmit (e) {
        e.preventDefault();

        const {dateOpen} = this.state;

        let validate = Validateur.validateur([
            {type: "text", id: 'dateOpen', value: dateOpen.value},
        ]);

        if(!validate.code){
            this.setState(validate.errors);
        }else{
            AjaxSend.loader(true);

            let options = { month: 'long'};
            let val =  dateOpen.inputVal;
            let dateValue = val.getDate() + " " + new Intl.DateTimeFormat('en-US', options).format(val) + " " + val.getFullYear() + " " + val.toLocaleTimeString();

            axios({ 
                method: 'post', 
                url: Routing.generate('admin_ticket_ouverture_update'), 
                data: {id: this.props.id, dateOpen: dateValue} 
            }).then(function (response) {
                let data = response.data; let code = data.code; 
                // AjaxSend.loader(false);
                location.reload();
            });
        }
    }

    render () {
        const {type} = this.props;
        const {staticDate, dateOpen, openEdit, error} = this.state;

        let dOpen = staticDate.toLocaleDateString() + " à " + staticDate.toLocaleTimeString().substring(0,staticDate.toLocaleTimeString().length-3);

        let typeString = type == 0 ? 'anciens' : 'nouveaux';

        return <>
            <h2>Journées des {typeString}</h2>
            <div className="toolbar">
                <div className="toolbar-left">
                    <div className="item">
                        Date d'ouverture des tickets :
                    </div>
                    <div className="item">
                        <span className="txt-primary">le {dOpen}</span> <span className="icon-edit" onClick={this.handleClick}></span>
                    </div>
                </div>
            </div>
            <div className="days-aside-edit">
                <div className={"days-edit-overlay " + openEdit} onClick={this.handleClose}></div>
                <div className={"days-edit " + openEdit}>
                    <div className="title">
                        <div>Editer la journée des {typeString}</div>
                        <div><span className="icon-close-circle" onClick={this.handleClose}></span></div>
                    </div>
                    {error != "" ? <div className="alert alert-danger">{error}</div> : null}
                    <div className='minimum'>
                        La date d'ouverture actuelle est du : <span className="txt-primary">{dOpen}</span>
                    </div>
                    <form onSubmit={this.handleSubmit}>
                        <div className={'form-group-date form-group' + (dateOpen.error ? " form-group-error" : "")}>
                            <label>Nouvelle date</label>
                            <DatePicker
                                locale="fr"
                                selected={dateOpen.inputVal}
                                onChange={this.handleChange}
                                showTimeSelect
                                timeFormat="HH:mm"
                                timeIntervals={15}
                                dateFormat="dd/MM/yyyy HH:mm"
                                />
                            <div className='error'>{dateOpen.error ? dateOpen.error : null}</div>
                        </div>
                        <div className="from-group">
                            <button className="btn btn-primary" type="submit">Mettre à jour</button>
                        </div>
                    </form>
                </div>
            </div>
        </>
    }
}