<template>
    <div v-if="dataFetched == true" class="program-container">
        <div class="ls-program-meny">
            <div class="ls-meny-items">
                <v-combobox
                    variant="outlined"
                    class="v-autocomplete-arr-sys"
                    clearable
                    chips
                    multiple
                    label="Sted"
                    :items="availableSteder"
                    v-model="selectedSteder"
                />
                
            </div>
        </div>
        <!-- <pre>{{ hendelser }}</pre> -->
        <template v-for="hendelse in hendelser" :key="hendelse.id">
            <div class="hendelse">
                <div @click="hendelse.isOpen = !hendelse.isOpen" class="first-width">
                    <div class="inner-content">
                        <div class="hendelse-bilde">
                            <img :src="hendelse.img" alt="Bilde av hendelse">
                        </div>
                        <div class="hendelse-content">
                            <h2 class="hendelse-title">{{ hendelse.title }}</h2>
                        </div>
                        <div class="hendelse-sted">
                            <span class="sted">{{ hendelse.sted }}</span>
                            <span class="tid" v-show="hendelse.isOpen">{{ hendelse.getStart() }}</span>
                        </div>
                        <div class="open-indicator">
                            <div :class="{'open': hendelse.isOpen}" class="svg">
                                <svg width="30" height="17" viewBox="0 0 30 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M28 1.17822L15 14.1782L2 1.17822" stroke="white" stroke-width="3"/>
                                </svg>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="collapse-wrapper">
                    <transition name="collapse">

                        <div v-if="hendelse.isOpen" class="hendelse-under collapsible-content">
                            <div class="hendelse-bilde">
        
                            </div>
                            <div class="under-content">
                                <div class="deltakere">
                                    <div class="deltaker">
                                        <div class="svg">
                                            <svg width="18" height="19" viewBox="0 0 18 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <g id="user-head">
                                            <path id="Shape" d="M5.75 5.17822C5.75 3.3833 7.20508 1.92822 9 1.92822C10.7949 1.92822 12.25 3.3833 12.25 5.17822C12.25 6.97315 10.7949 8.42822 9 8.42822C7.20508 8.42822 5.75 6.97315 5.75 5.17822ZM4.53836 11.747C5.94349 11.1171 7.67103 10.9282 9 10.9282C10.329 10.9282 12.0565 11.1171 13.4616 11.747C14.8773 12.3816 16 13.485 16 15.3032V16.0532C16 16.802 15.3936 17.4282 14.6387 17.4282H3.36126C2.60644 17.4282 2 16.802 2 16.0532V15.3032C2 13.485 3.12267 12.3816 4.53836 11.747Z" stroke="white" stroke-linecap="round" stroke-linejoin="round"/>
                                            </g>
                                            </svg>
                                        </div>
                                        <div class="deltaker-info">
                                            <span>Deltaker 1</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="hendelse-beskrivelse">
                                    <span class="beskrivelse">{{ hendelse.beskrivelse }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- <div v-if="hendelse.isOpen" class="collapsible-content">
                            <v-card>
                                <v-card-title>Collapsible Content</v-card-title>
                                <v-card-text>
                                    This content will collapse when the button is clicked.
                                </v-card-text>
                            </v-card>
                        </div> -->
                    </transition>
                </div>
                <!-- <div class="ls-program-item">
                    <h3>{{ hendelse.tittel }}</h3>
                    <p>{{ hendelse.beskrivelse }}</p>
                    <p>{{ hendelse.sted }}</p>
                    <p>{{ hendelse.dato }}</p>
                </div> -->
            </div>
        </template>
    </div>
</template>

<script lang="ts">
import Hendelse from '../objects/Hendelse';

export default {
    mounted() {
        this.fetchProgramData();
    },
    data() {
        return {
            spaInteraction : (<any>window).spaInteraction, // Definert i main.ts
            dataFetched: false,
            fetchingStarted: false,
            hendelser: [] as Hendelse[],
            availableSteder: ['Tana', 'Norafjell'] as string[],
            selectedSteder : [] as string[],
        };
    },
    methods: {
        async fetchProgramData() {
            this.fetchingStarted = true;
            this.dataFetched = false;

            var data = {
                unike: true
            };

            var results = await this.spaInteraction.runAjaxCall('getProgram.ajax.php', 'GET', data);

            for (let h of results.hendelser) {
                var newHendelse = new Hendelse(h.id, h.navn, 'https://placehold.co/155', h.start, 0, h.sted, h.context.type, h.beskrivelse);
                this.hendelser.push(newHendelse);
            }

            this.dataFetched = true;

            return results;
        },
    },
};
</script>


<style>
.entry-content {
    background: #00004C !important;
}
</style>
<style scoped>
.program-container {
    color: #fff;
}
.hendelse {
    border-top: solid 1px #fff;
    padding: 24px 0;
}

.hendelse .first-width {
    display: flex;
}
.hendelse .first-width .inner-content{
    width: 100%;
    display: flex
}
.under-content {
    display: inline;
}
.under-content { 
    width: 70%;
}
.open-indicator {
    margin: auto;
    margin-right: 0;
    margin-top: 10px;
}
.open-indicator .svg {
    transition: transform 0.3s;
    transform: rotate(-90deg);
}
.open-indicator .svg.open {
    transition: transform 0.3s;
    transform: rotate(0);
}
.hendelse-bilde {
    margin-right: 24px;
    min-width: 150px;
}
.hendelse-bilde img {
    border-radius: 22px;
}
.hendelse-content {
    width: 50%;
    margin-right: 12px;
}
.hendelse-title {
    overflow: hidden;
    color: #fff;
    letter-spacing: -0.45px;
    font-size: 45px;
}
.hendelse-sted {
    display: grid;
    margin: auto;
    margin-top: 0;
    margin-left: 0;
}
.hendelse-sted .sted {
    font-size: 20px;
    font-weight: 700;
}
.hendelse-sted .tid {
    font-size: 20px;
    font-weight: 400;
}
.program-container {
    background: #00004C;
}
.hendelse-under {
    display: flex;
    margin-top: 24px;
}
.hendelse-under .deltakere {
    display: flex;
    margin-bottom: 32px;
}
.deltakere .deltaker {
    display: flex;
    padding: 8px 18px;
    border: solid 1px #fff;
    border-radius: 100px;
    margin-right: 8px;
}
.deltakere .deltaker .svg {
    display: flex;
    margin-right: 12px;
}
.hendelse-beskrivelse .beskrivelse {
    font-size: 24px;
}


/* COLLAPSE TRANSITION */
/* Wrapper to prevent content from jumping */
.collapse-wrapper {
    overflow: hidden;
}

/* Transition for smooth collapse */
.collapse-enter-active, .collapse-leave-active {
    transition: max-height 0.3s ease-in-out, opacity 0.3s ease-in-out;
    overflow: hidden;
}

/* Initial hidden state */
.collapse-enter-from, .collapse-leave-to {
    max-height: 0;
    opacity: 0;
}

/* Fully visible state */
.collapse-enter-to, .collapse-leave-from {
    max-height: 200px; /* Adjust as needed */
    opacity: 1;
}

/* Collapsible content styling */
.collapsible-content {
    overflow: hidden;
}
</style>