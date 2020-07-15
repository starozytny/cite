import React, {Component} from 'react';

export class Details extends Component {
    constructor(props){
        super(props)
        
        this.state = {
            prospects: JSON.parse(JSON.parse(this.props.prospects))
        }

        this.handleChangeStatus = this.handleChangeStatus.bind(this);
    }

    handleChangeStatus (e) {
        let id = parseInt(e.currentTarget.dataset.id);

        // this.setState({})
    }

    render () {
        const {prospects} = this.state;

        let items = prospects.map((elem, index) => {
            return <div className="item" key={elem.id}>
                <div className="col-1">
                    {elem.numAdh != null ? <div>#{elem.numAdh}</div> : null}
                    <div className="name">{elem.civility} {elem.firstname} <span>{elem.lastname}</span></div>
                    <div className="birthday">{(new Date(elem.birthday)).toLocaleDateString('fr-FR')}</div>
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
            </div>
        })

        return <div className="prospects">
            <div className="prospects-header">
                <div className="col-1">Identifiant</div>
                <div className="col-2">Contact</div>
                <div className="col-3">Adresse</div>
                <div className="col-4">Horaire</div>
                <div className="col-5">Status</div>
            </div>
            <div className="prospects-body">
                {items}
            </div>
        </div>
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