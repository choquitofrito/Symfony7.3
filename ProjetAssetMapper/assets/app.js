import './bootstrap.js';
/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */

// importer le .css de bootstrap et fontawesome
import 'bootstrap/dist/css/bootstrap.min.css';
import '@fortawesome/fontawesome-free/css/all.css';

import './styles/app.css';

console.log('This log comes from assets/app.js - welcome to AssetMapper! 🎉');


// jquery config!
import $ from 'jquery';
// things on "window" become global variables
window.$ = $;

console.log ($);  