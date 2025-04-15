import { createApp, h, ref } from "vue";
import App from "./App.vue";

// Components
import Liveprogram from './components/Liveprogram.vue';
import Deltakere from "./components/Deltakere.vue";
import ProgramPublikum from "./components/ProgramPublikum.vue";
import ProgramDeltakere from "./components/ProgramDeltakere.vue";
import AktiviteterStart from "./components/AktiviteterStart.vue";

import hljs from "highlight.js/lib/core";
import javascript from "highlight.js/lib/languages/javascript";
import css from "highlight.js/lib/languages/css";
import xml from "highlight.js/lib/languages/xml";
import hljsVuePlugin from "@highlightjs/vue-plugin";
import "highlight.js/styles/base16/dracula.css";

// Vuetify
import 'vuetify/styles'
import { createVuetify } from 'vuetify'
import * as components from 'vuetify/components'
import * as directives from 'vuetify/directives'

import "../node_modules/ukm-components-vue3/dist/style.css";

hljs.registerLanguage("xml", xml);
hljs.registerLanguage("javascript", javascript);
hljs.registerLanguage("css", css);

// Register the Interaction object globally so it can be accessed from other vue components or scripts
// InteractionObject is used to show notifications, confirm dialogs, and other interactions
import { InteractionObject } from 'ukm-components-vue3';
import { SPAInteraction } from 'ukm-components-vue3';

// Programmatically mount the InteractionObject
const interactionObjectInstance = createApp(InteractionObject);

// Ensure Vuetify is used by the interactionObjectInstance
// interactionObjectInstance.use(vuetify);

const interactionInstance = interactionObjectInstance.mount(document.createElement('div'));

// Append the interactionObjectInstance to the body
document.body.appendChild(interactionInstance.$el);

console.log('interactionInstance');
console.log(interactionInstance);

var ajaxurl : string = '/festivalen/wp-content/themes/astra-child-theme/endpoints/';

if(ajaxurl == undefined || ajaxurl == null) {
    throw new Error('ajaxurl is not defined');
}

(<any>window).spaInteraction = new SPAInteraction(interactionInstance, ajaxurl);

const app = createApp(App);

// Make Director global
import { Director } from 'ukm-spa/Director';

var director = new Director();
(<any>window).director = director;

app.use(hljsVuePlugin);
// app.use(vuetify);

document.addEventListener("DOMContentLoaded", () => {
    document.querySelectorAll("[data-vue-component]").forEach(el => {
        const componentName = el.getAttribute("data-vue-component");
        let component = null;
        
        if(componentName == 'liveprogram') {
            component = Liveprogram;
        }
        else if(componentName == 'deltakere') {
            component = Deltakere;
        }
        else if(componentName == 'program-publikum') {
            component = ProgramPublikum;
        }
        else if(componentName == 'program-deltakere') {
            component = ProgramDeltakere;
        }
        else if(componentName == 'aktiviteter') {
            component = AktiviteterStart;
        }
        else {
            throw new Error('Unknown component: ' + componentName);
        }
        
        const vuetify = createVuetify({
            components,
        });
        
        const instance = createApp(component);
        instance.use(hljsVuePlugin);
        instance.use(vuetify);
        instance.mount(el);
    });
});
