/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you require will output into a single css file (app.css in this case)
require('../scss/app.scss');

// Need jQuery? Install it with "yarn add jquery", then uncomment to require it.
// const $ = require('jquery');

let testString: string = 'Typescript loaded';
console.log(testString);

import Vue from 'vue';
import Example from './components/Example';

new Vue({
    el: '#app',
    render: h => h(Example)
});
