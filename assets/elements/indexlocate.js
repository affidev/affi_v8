import { h, render, Component } from "preact";
import register from 'preact-custom-element';
import {SearchLocate} from "./search/SearchLocate";

class Locate extends Component {
    constructor(props) {
        super(props);
        this.city=props.city;
        this.lclass=props.lclass;
    }
    static tagName = 'x-locate';
    static observedAttributes = ['city','lclass'];

    render() {
        return  <SearchLocate city={this.city} lclass={this.lclass}/>
    }
}

register(Locate, 'x-locate', ['city','lclass']);
