import React, {Component} from 'react';
import {Pagination} from '../Pagination';

export class Page extends Component {
    constructor (props) {
        super(props)
    }

    render () {
        const {havePagination, perPage, taille, itemsPagination} = this.props

        return <>
            {havePagination ? <Pagination perPage={perPage} taille={taille} items={itemsPagination} onUpdate={(items) => this.props.onUpdate(items)}/> : null}
        </>
    }
}