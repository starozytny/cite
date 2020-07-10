import React, {Component} from 'react';

export class Compteur extends Component {
    constructor(props) {
        super(props);

        console.log(props.max)

        this.state ={
            max: props.max,
            compteur: 0,
            timer: props.timer
        }
    }

    componentDidMount() {
        this.timerID = setInterval(() => this.tick(),
            this.state.timer
        );
    }

    componentWillUnmount() {
        clearInterval(this.timerID);
    }

    tick(){
        if(this.state.compteur < this.state.max){
            this.setState(state => ({
                compteur: parseInt(state.compteur)+1
            }));
        }
    }

    render() {
        return (
        <span className="compteur">{this.state.compteur}</span>
        );
    }
}

export default Compteur;