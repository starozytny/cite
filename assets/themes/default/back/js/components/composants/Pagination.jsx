import React, {Component} from 'react';
import ReactPaginate from 'react-paginate';

export class Pagination extends Component {
    constructor (props) {
        super(props)

        this.state = {
            offset: 0,
            currentPage: 0,
            perPage: props.perPage != undefined ? props.perPage : 20
        }

        this.handleClick = this.handleClick.bind(this);
    }

    handleClick (e) {
        const selectedPage = e.selected;
        const offset = selectedPage * this.props.perPage;

        this.setState({ currentPage: selectedPage, offset: offset })
        this.props.onUpdate(this.props.items.slice(offset, offset + parseInt(this.props.perPage)))
    }

    render () {
        const {taille} = this.props
        const {perPage} = this.state

        return <>
            <ReactPaginate
                previousLabel={<span className="icon-chevron-left"></span>}
                nextLabel={<span className="icon-chevron-right"></span>}
                breakLabel={'...'}
                breakClassName={'break-me'}
                pageCount={Math.ceil(taille / perPage)}
                marginPagesDisplayed={1}
                pageRangeDisplayed={3}
                onPageChange={this.handleClick}
                containerClassName={'pagination'}
                subContainerClassName={'pages pagination'}
                activeClassName={'active'}
            />
        </>
    }
}