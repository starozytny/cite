import React, {Component} from 'react';
import axios from 'axios/dist/axios';
import Routing from '../../../../../../../../public/bundles/fosjsrouting/js/router.min.js';
import AjaxSend from '../../../components/functions/ajax_classique';
import {Input, Select} from '../../../components/composants/Fields';
import Validateur from '../../../components/functions/validate_input';
import DatePicker from "react-datepicker";
import { registerLocale, setDefaultLocale } from  "react-datepicker";
import fr from 'date-fns/locale/fr';
registerLocale('fr', fr)
import "react-datepicker/dist/react-datepicker.css";

export class ToolbarTicket extends Component {
    constructor (props) {
        super(props)

        this.state = {
            error: '',
            open: '',
            dateDay: {value: '', error: '', inputVal: null},
            type: {value: '', error: ''}
        }

        this.handleClick = this.handleClick.bind(this)
        this.handleClose = this.handleClose.bind(this)
        this.handleChange = this.handleChange.bind(this)
        this.handleChangeDate = this.handleChangeDate.bind(this)
        this.handleSubmit = this.handleSubmit.bind(this)
    }

    handleChange (e) { this.setState({ [e.target.name]: {value: e.target.value} }); }

    handleChangeDate (e) {  this.setState({ dateDay: {inputVal: e, value: new Date(e).toLocaleDateString()} }); }

    handleClick (e) { this.setState({open: 'active', error: ''}) }

    handleClose (e) { this.setState({open: '', error: ''}) }

    handleSubmit (e) {
        e.preventDefault();

        const {dateDay, type} = this.state;

        let validate = Validateur.validateur([
            {type: "text", id: 'dateDay', value: dateDay.value},
            {type: "text", id: 'type', value: type.value},
        ]);

        if(!validate.code){
            this.setState(validate.errors);
        }else{
            AjaxSend.loader(true);

            let options = { month: 'long'};
            let val =  dateDay.inputVal;
            let dateValue = val.getDate() + " " + new Intl.DateTimeFormat('en-US', options).format(val) + " " + val.getFullYear() + " " + val.toLocaleTimeString();

            let self = this

            axios({ 
                method: 'post', 
                url: Routing.generate('admin_ticket_add_day'), 
                data: {dateDay: dateValue, type: type.value} 
            }).then(function (response) {
                let data = response.data; let code = data.code; 
                if(code !== 1){
                    AjaxSend.loader(false);
                    self.setState({error: data.message})
                }else{
                    location.reload();
                }
            });
        }
    }

    render () {
        const {error, open, dateDay, type} = this.state

        return <>
            <div className="toolbar">
                <div className="toolbar-left">
                    <div className="toolbar-item">
                        <button className="btn btn-primary" onClick={this.handleClick}>Ajouter une journée</button>
                    </div>
                </div>
            </div>

            <div className="days-aside-add">
                <div className={"days-add-overlay " + open} onClick={this.handleClose}></div>
                <div className={"days-add " + open}>
                    <div className="title">
                        <div>Ajouter une journée</div>
                        <div><span className="icon-close-circle" onClick={this.handleClose}></span></div>
                    </div>
                    {error != "" ? <div className="alert alert-danger">{error}</div> : null}
                    <form onSubmit={this.handleSubmit}>
                        <div className={'form-group-date form-group' + (dateDay.error ? " form-group-error" : "")}>
                            <label>Date</label>
                            <DatePicker
                                locale="fr"
                                selected={dateDay.inputVal}
                                onChange={this.handleChangeDate}
                                dateFormat="dd/MM/yyyy"
                                minDate={new Date()}
                                />
                            <div className='error'>{dateDay.error ? dateDay.error : null}</div>
                        </div>

                        <RadioType type={type} onChange={this.handleChange}/>

                        <div className="from-group">
                            <button className="btn btn-primary" type="submit">Ajouter</button>
                        </div>
                    </form>
                </div>
            </div>
        </>
    }
}

function RadioType({type, onChange}) {
    return (
        <div className={'form-group' + (type.error ? " form-group-error" : "")}>
            <div className="form-group-radio">
                <div>
                    <input type="radio" id="type-ancien" name="type" value="0" checked={type.value === '0'} onChange={onChange} />
                    <label htmlFor="type-ancien">Ancien</label>
                </div>
                <div>
                    <input type="radio" id="type-nouveau" name="type" value="1" checked={type.value === '1'} onChange={onChange} />
                    <label htmlFor="type-nouveau">Nouveau</label>
                </div>
            </div>
            <div className='error'>{type.error ? type.error : null}</div>
        </div>
    )
}