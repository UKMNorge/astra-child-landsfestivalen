<template>
    <div v-if="dataFetched == true" class="program-container">
        <div class="ls-program-meny">
            <div class="ls-inner-meny-beholder">
                <div class="ls-meny-item">                 
                    <SelectProgramStyle
                        background="#fff"
                        :label="'Dag'"
                        :availableItems="availableTider" 
                        v-model:selectedItems="selectedTider" 
                    />
                </div>
                <div class="ls-meny-item">                 
                    <SelectProgramStyle 
                        :label="'Sted'" 
                        :availableItems="availableSteder" 
                        v-model:selectedItems="selectedSteder" 
                    />
                </div>
                <div class="ls-meny-item">                 
                    <SelectProgramStyle 
                        :label="'Type'"
                        :availableItems="availableTyper" 
                        v-model:selectedItems="selectedTyper" 
                    />
                </div>
            </div>
        </div>

        <template v-for="hendelse in getFilteredHendelser()" :key="hendelse.id">
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
                            <span v-show="!hendelse.isOpen" class="sted">{{ hendelse.sted }}</span>
                            <!-- <v-chip class="tid" v-show="hendelse.isOpen">{{ hendelse.getStart() }}</v-chip> -->
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
                            <div class="hendelse-info-extendable">
                                <div class="hendelse-bilde">
            
                                </div>
                                <div class="under-content">
                                    <div class="sted-tid-after-open as-margin-bottom-space-2">
                                        <!-- <h5 class="sted-after-open">Sted: <b>{{ hendelse.sted }}</b></h5> -->
                                        <v-chip class="tid-after-open as-margin-right-space-1">Sted: {{ hendelse.sted }}</v-chip>
                                        <v-chip class="tid-after-open">Tid: {{ hendelse.getStart() }}</v-chip>
                                    </div>
                                    <!-- <div class="deltakere">
                                        <div v-for="innslag in hendelse.innslag" class="deltaker">
                                            <div class="svg">
                                                <svg width="18" height="19" viewBox="0 0 18 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <g id="user-head">
                                                <path id="Shape" d="M5.75 5.17822C5.75 3.3833 7.20508 1.92822 9 1.92822C10.7949 1.92822 12.25 3.3833 12.25 5.17822C12.25 6.97315 10.7949 8.42822 9 8.42822C7.20508 8.42822 5.75 6.97315 5.75 5.17822ZM4.53836 11.747C5.94349 11.1171 7.67103 10.9282 9 10.9282C10.329 10.9282 12.0565 11.1171 13.4616 11.747C14.8773 12.3816 16 13.485 16 15.3032V16.0532C16 16.802 15.3936 17.4282 14.6387 17.4282H3.36126C2.60644 17.4282 2 16.802 2 16.0532V15.3032C2 13.485 3.12267 12.3816 4.53836 11.747Z" stroke="white" stroke-linecap="round" stroke-linejoin="round"/>
                                                </g>
                                                </svg>
                                                <span>{{ innslag.antallDeltakere }}</span>
                                            </div>
                                            <div class="deltaker-info">
                                                <span>{{ innslag.name }}</span>
                                            </div>
                                        </div>
    
                                    </div> -->
                                    <div class="hendelse-beskrivelse">
                                        <span class="beskrivelse">{{ hendelse.beskrivelse }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="hendelse-info-extendable">
                                <div class="hendelse-bilde">
            
                                </div>
                                <div v-if="hasShowMore(hendelse)" class="as-margin-top-space-2">
                                    <v-dialog width="95%" max-width="800px">
                                        <template v-slot:activator="{ props: activatorProps }">
                                            <v-btn
                                                class="v-btn-as v-btn-hvit"
                                                v-bind="activatorProps"
                                                color="#000"
                                                size="large"
                                                rounded="large"
                                                variant="outlined" >
                                                Vis mer
                                            </v-btn>
                                        </template>

                                        <template v-slot:default="{ isActive }">
                                            <v-card :title="hendelse.title">
                                           <HendelseContentComponent :hendelse="hendelse" />
                                            <v-card-actions>
                                                <v-spacer></v-spacer>

                                                <v-btn
                                                    icon
                                                    class="close-btn"
                                                    @click="isActive.value = false"
                                                    size="40"
                                                >
                                                    <v-icon style="color: #fff;">mdi-close</v-icon>
                                                </v-btn>
                                                <v-btn
                                                    class="close-btn-down"
                                                    text="Lukk"
                                                    @click="isActive.value = false"
                                                ></v-btn>
                                            </v-card-actions>
                                            </v-card>
                                        </template>
                                    </v-dialog>


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
import SelectProgramStyle from './utils/SelectProgramStyle.vue';
import HendelseContentComponent from './utils/HendelseContent.vue';

export default {
    props: {
        programType: {
            type: String,
            validator: (value: string) => {
                return ['publikum', 'deltakere'].includes(value)
            },
            required: true
        },
    },
    mounted() {
        this.fetchProgramData();
    },
    components: {
        SelectProgramStyle : SelectProgramStyle,
        HendelseContentComponent : HendelseContentComponent,
    },
    data() {
        return {
            isActive : false,
            spaInteraction : (<any>window).spaInteraction, // Definert i main.ts
            dataFetched: false,
            fetchingStarted: false,
            hendelser: [] as Hendelse[],
            availableSteder: [] as {id: number|string, title: string}[],
            availableTider: [] as {id: number|string, title: string}[],
            availableTyper: [] as {id: number|string, title: string}[],

            selectedSteder : [] as {id: number|string, title: string}[],
            selectedTider : [] as {id: number|string, title: string}[],
            selectedTyper : [] as {id: number|string, title: string}[],

            hendelseMedAktiviteter : {} as any,

        };
    },
    methods: {
        // Sjekker om hendelsen har aktiviteter eller innslag for å vise "Vis mer" knappen
        hasShowMore(hendelse : Hendelse) : boolean {
            if(this.hendelseMedAktiviteter[hendelse.id] && this.hendelseMedAktiviteter[hendelse.id] == true) {
                return true;
            }

            if(hendelse.innslag && hendelse.innslag.length > 0) {
                return true;
            }

            return false;
        },
        getFilteredHendelser() : Hendelse[] {
            if(this.selectedSteder.length == 0 && this.selectedTider.length == 0 && this.selectedTyper.length == 0) {
                return this.hendelser;
            }

            return this.hendelser.filter(h => {
                if(this.selectedSteder.length > 0 && !this.selectedSteder.find(sted => String(sted) == h.sted)) {
                    return false;
                }
                if(this.selectedTider.length > 0 && !this.selectedTider.find(t => (<any>t) == h.getStartDag())) {
                    return false;
                }

                if(this.selectedTyper.length > 0 && !this.selectedTyper.find(t => String(t) == h.type)) {
                    return false;
                }

                return true;
            });
        },
        async fetchProgramData() {
            this.fetchingStarted = true;
            this.dataFetched = false;

            var data = {
                erDeltakerProgram: this.programType == 'deltakere'
            };

            var results = await this.spaInteraction.runAjaxCall('getProgram.ajax.php', 'POST', data);
            
            this.hendelseMedAktiviteter = results.hendelseMedAktiviteter;

            for (let h of results.hendelser) {
                const startDate = new Date(h.start * 1000);
                let dataInnslag = h.innslag && h.innslag.innslag ? h.innslag.innslag[0] : null;

                console.log(h);
                console.log(h.innslag);
                console.log(h.innslag.innslag);
                
                console.warn(results.innslagPersoner);

                // Innslag og deltakere
                let innslag : {name : string, antallDeltakere : number, url : string}[] = [];
                let antallDeltakere = 0;

                if(dataInnslag != null) {
                    antallDeltakere += results.innslagPersoner[dataInnslag.id].length;
    
                    innslag.push({
                        name: dataInnslag.navn,
                        antallDeltakere: antallDeltakere,
                        url: 'inPerson.url'
                    });
                    
                }

                var newHendelse = new Hendelse(
                    h.id, 
                    h.navn, 
                    h.bilde ?? 'http://ukm.no/wp-content/uploads/2025/04/40ukm.png', 
                    h.start, 
                    0, 
                    h.sted, 
                    h.context.type, 
                    h.beskrivelse,
                    innslag
                );
                this.hendelser.push(newHendelse);
                
                if(this.availableTider.find(t => t.id == newHendelse.getStartDag()) == undefined && h.start && h.start.trim() != '') {
                    this.availableTider.push({'id' : newHendelse.getStartDag(), 'title' : newHendelse.getStartDag()});
                }

                if(this.availableSteder.find(s => s.id == h.sted) == undefined && h.sted && h.sted.trim() != '') {
                    this.availableSteder.push({'id' : h.sted, 'title' : h.sted});
                }

                if(this.availableTyper.find(t => t.id == h.context.type) == undefined && h.context && h.context.type.trim() != '') {
                    this.availableTyper.push({'id' : h.context.type, 'title' : h.context.type});
                }
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
.v-autocomplete-arr-sys div .v-field,
.v-text-field-arr-sys div .v-field {
    border-radius: var(--radius-normal) !important;
}
.v-field__input input {
    background: transparent !important;
    border: none !important;
}
</style>
<style scoped>
.ls-program-meny {
    display: flex;
}
.ls-inner-meny-beholder {
    margin: auto;
    display: flex;
}
.ls-meny-item {
    width: auto;
    margin: auto 10px;
    min-width: 110px;
}
.program-container {
    color: #fff;
    padding: 20px 0;
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
    max-width: 250px;
    background: transparent;
}
.hendelse-bilde img {
    border-radius: 22px;
    width: 100%;
    height: 100%;
    object-fit: cover;
    background: #fff;
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
    margin: auto;
}
.hendelse-sted .tid {
    font-size: 20px;
    font-weight: 400;
    margin-top: 8px;
}
.program-container {
    background: #00004C;
}
.hendelse-info-extendable {
    display: flex;
}
.hendelse-under {
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
.v-select-program-meny :deep(.v-label) {
    font-size: 20px;
    font-weight: 400;
    margin-top: 10px;
    color: #fff;
    opacity: 1;
}
.v-select-program-meny :deep(.v-label.v-field-label.v-field-label--floating) {
    color: #fff !important;
    padding-bottom: 5px;
}
.v-select-program-meny.v-select--active-menu :deep(.v-label) {
    color: #fff !important;
}
.v-select-program-meny :deep(.v-input__control .v-field) {
    background: #00004c !important;
}
/* .v-select-program-meny.v-select--active-menu .v-input__control { */
.v-select-program-meny.v-select--active-menu :deep(.v-input__control) {
    background: red !important;
}
.v-select-program-meny :deep(.mdi-menu-down) {
    color: #fff !important;
    opacity: 1;
}
.v-select-program-meny.v-select--active-menu :deep(.mdi-menu-down) {
    color: #000 !important;
}
.v-select-program-meny :deep(.v-field__append-inner) {
    display: none !important;
}
.v-select-program-meny :deep(.v-input__control) {
    border: solid 2px #fff;
    border-radius: 10px !important;
    overflow: hidden;
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
.sted-tid-after-open {
    display: flex;
}
.sted-after-open {
    margin: 0;
    color: #fff !important;
}
.close-btn {
    background: #00004c;
    border-radius: 50% !important;
    width: 40px;
    height: 40px;
    min-width: 0; /* remove Vuetify default min-width */
    position: absolute;
    top: 16px;
    right: 16px;
}
.close-btn-down {
    background: #00004c;
    color: #fff;
    margin-right: 10px;
    margin-bottom: 10px;
}

@media (max-width: 767px) {
    .hendelse-title {
        font-size: 30px !important;
    }
    .hendelse-sted {
        text-align: right;
        padding-right: 16px !important;
        min-width: 115px;
    }
    .hendelse-sted .sted {
        font-size: 16px;
    }
    .hendelse-sted .tid {
        font-size: 11px;
        text-align: center;
    }
    .hendelse-bilde {
        margin-right: 24px;
        width: 120px;
        height: 90px;

    }
    .dialog-program-popup {
        padding-left: 8px;
        padding-right: 8px;
    }
    .v-select-program-meny :deep(.v-label) {
        font-size: 16px;
    }
    .sted-tid-after-open {
        overflow-x: auto;
        white-space: nowrap;
        -webkit-overflow-scrolling: touch;
    }
    .sted-tid-after-open .v-chip {
        flex-shrink: 0;
    }
}
@media (max-width: 520px) {
    .hendelse-content {
        width: 100%;
    }
    .hendelse-info-extendable .hendelse-bilde {
        display: none;
    }
    .hendelse-sted {
        display: none;
    }
    .under-content { 
        width: 100%;
    }
    .hendelse-title {
        font-size: 5vw !important;
    }
    .hendelse-beskrivelse .beskrivelse {
        font-size: 18px;
    }
    .hendelse-sted .sted {
        font-size: 14px;
    }
    .v-select-program-meny {
        width: 90px;
        height: 70px;
    }
    .ls-meny-item {
        min-width: 50px;
    }
    .v-select-program-meny :deep(.v-label) {
        top: 3px !important;
    }

}
@media (max-width: 400px) {
    /* .hendelse-bilde {
        display: none;
    } */
    .hendelse-title {
        font-size: 18px !important;
    }
}
</style>