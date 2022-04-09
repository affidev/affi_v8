/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.scss';
import './styles/app_mb.scss';
import './styles/form.css';
import './styles/semcss.scss';
import './styles/outstyle.css';
//import "react-datepicker/dist/react-datepicker.css";
//import 'react-datepicker/dist/react-datepicker-cssmodules.css';

import 'bootstrap';
import bsCustomFileInput from 'bs-custom-file-input';
import 'jquery';
const $ = require('jquery');
global.$ = global.jQuery = $;

bsCustomFileInput.init();
