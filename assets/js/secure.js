import { h, render, Component } from "preact";
import register from 'preact-custom-element';
import {Testmailform} from "../formulaires/testmailform";

class TestMail extends Component {
    constructor(props) {
        super(props);
    }

    render() {
        return  <Testmailform />
    }
}
register(TestMail, 'test-mail');

/* old version with presinscript
import PreInscriptform from "../formulaires/preInscript";

class PreInscript extends Component {
    constructor(props) {
        super(props);
    }

    render() {
        return  <PreInscriptform />
    }
}

register(PreInscript, 'pre-incript');
*/






