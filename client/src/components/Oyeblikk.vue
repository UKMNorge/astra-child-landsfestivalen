<template>
    <div>
        <v-tabs v-model="tab" class="tabs-timeplan">
            <v-tab v-for="dayTab in getTabs()" :key="dayTab" :value="dayTab">{{ getHendelseNameById(dayTab) }}</v-tab>
        </v-tabs>


        <!-- XS scroller for tabs -->
        <div class="xs-scroller as-margin-top-space-2">
            <v-btn 
                icon
                @click="() => {if(getTabPosition < 1) return; tab = getTabs()[getTabPosition - 1]}"
                class="btn-scroller left"
                size="x-small"
                variant="text"
            >
                <v-icon>
                    <svg xmlns="http://www.w3.org/2000/svg" width="5" height="9" viewBox="0 0 5 9" fill="none">
                        <path d="M1.53005 0.965113L4.5301 3.96512C4.6761 4.11112 4.75 4.30312 4.75 4.49512C4.75 4.68712 4.6771 4.87912 4.5301 5.02512L1.53005 8.02512C1.23705 8.31812 0.762018 8.31812 0.469018 8.02512C0.176018 7.73212 0.176018 7.25712 0.469018 6.96412L2.93899 4.49412L0.469017 2.02411C0.176017 1.73111 0.176017 1.25611 0.469017 0.963113C0.762017 0.670113 1.23705 0.670113 1.53005 0.963113L1.53005 0.965113Z" fill="#00004C"/>
                    </svg>
                </v-icon>
            </v-btn>

            <div class="scroller-lines">
                <div @click="moveToTab(dayTab)" class="scroller-line" :class="{'active' : dayTab == tab}" :style="'width:' + (100/getTabs().length) + '%'" v-for="dayTab in getTabs()" :key="dayTab">
                </div>
            </div>

        
            <v-btn 
                icon
                @click="() => {if(getTabPosition > (getTabs().length-2)) return; tab = getTabs()[getTabPosition + 1]}"
                class="btn-scroller right"
                size="x-small"
                variant="text"
            >
                <v-icon>
                    <svg xmlns="http://www.w3.org/2000/svg" width="5" height="9" viewBox="0 0 5 9" fill="none">
                        <path d="M1.53005 0.965113L4.5301 3.96512C4.6761 4.11112 4.75 4.30312 4.75 4.49512C4.75 4.68712 4.6771 4.87912 4.5301 5.02512L1.53005 8.02512C1.23705 8.31812 0.762018 8.31812 0.469018 8.02512C0.176018 7.73212 0.176018 7.25712 0.469018 6.96412L2.93899 4.49412L0.469017 2.02411C0.176017 1.73111 0.176017 1.25611 0.469017 0.963113C0.762017 0.670113 1.23705 0.670113 1.53005 0.963113L1.53005 0.965113Z" fill="#00004C"/>
                    </svg>
                </v-icon>
            </v-btn>
        </div>
        

        <v-card-text v-if="dataFetched" class="nop-impt as-margin-top-space-4">
            <v-tabs-window v-model="tab">
                <v-tabs-window-item v-for="timeplanItem, key in getHendelserTimeplan()" :value="key" :key="key">
                    <div>
                        <div class="timeplan-item tplan-style direktesending">
                            <h3 class="title">Direktesending</h3>
                            <div v-html="getDirektesending(key).embed"></div>
                        </div>
                    </div>

                    <div>
                        <div @click="toggleTimeplanItem(tp)" v-for="tp in timeplanItem" :class="{'item-with-link' : tp.getLink() != null, 'item-expanded': tp.isOpen}" class="timeplan-item tplan-style" :key="tp.getId()">
                            <div class="item-header">
                                <h3 class="title">{{ tp.getTitle() }}</h3>
                                <p class="time">{{ tp.getStartEndTimeHumanReadable() }}</p>
                                <p v-show="tp.getPlace().length > 0" class="place">{{ tp.getPlace() }}</p>
                                <div class="expand-indicator" :class="{'expanded': tp.isOpen}">
                                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                                        <path d="M4 6L8 10L12 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </div>
                            </div>
                            
                            <div v-if="tp.isOpen" class="item-content">
                                <div class="expanded-info">
                                    <p v-if="tp.getDescription()" class="description">{{ tp.getDescription() }}</p>
                                    
                                    <!-- Show list of persons/deltakere -->
                                    <div v-if="getPersonsForInnslag(tp).length > 0" class="persons-section">
                                        <div class="persons-chips">
                                            <v-chip 
                                                v-for="person in getPersonsForInnslag(tp)" 
                                                :key="person.id" 
                                                class="person-chip"
                                                color="#FFF056"
                                                text-color="#00004C"
                                                size="small"
                                            >
                                                <v-icon left size="small" color="#00004C">
                                                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                                                        <path d="M8 8C10.21 8 12 6.21 12 4C12 1.79 10.21 0 8 0C5.79 0 4 1.79 4 4C4 6.21 5.79 8 8 8ZM8 10C5.33 10 0 11.34 0 14V15C0 15.55 0.45 16 1 16H15C15.55 16 16 15.55 16 15V14C16 11.34 10.67 10 8 10Z" fill="currentColor"/>
                                                    </svg>
                                                </v-icon>
                                                <span class="person-name">{{ person.fornavn }} {{ person.etternavn }}</span>
                                                <span v-if="person.fylke" class="person-location"> ({{ person.fylke.navn }})</span>
                                            </v-chip>
                                        </div>
                                    </div>
                                    
                                    <!-- Show list of titles (songs, etc.) -->
                                    <div v-if="getTitlesForInnslag(tp).length > 0" class="titles-section">
                                        <h4 class="titles-title">Tittel:</h4>
                                        <div class="titles-list">
                                            <div 
                                                v-for="title in getTitlesForInnslag(tp)" 
                                                :key="title.id" 
                                                class="title-item"
                                            >
                                                <span class="title-name">{{ title.tittel }}</span>
                                                <span v-if="title.varighet && title.varighet !== '0'" class="title-duration">({{ title.varighet }})</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </v-tabs-window-item>
            </v-tabs-window>
        </v-card-text>
    </div>
</template>
<script lang="ts">
import HendelseItem from '../objects/HendelseItem';
import Hendelse from '../objects/Hendelse';

export default {
    props: {
        programType: {
            type: String,
            default: 'publikum'
        }
    },
    components: {

    },
    data() {
        return {
            spaInteraction : (<any>window).spaInteraction, // Definert i main.ts
            tab: null as string | null,
            hendelseItems: [] as HendelseItem[],
            daysOfWeek: ['Søndag', 'Mandag', 'Tirsdag', 'Onsdag', 'Torsdag', 'Fredag', 'Lørdag'],
            hendelser: [] as Hendelse[],
            deltakere: {} as { [key: string]: any },
            alleInnslag: {} as { [key: string]: any },
            innslagTitler: {} as { [key: string]: any },
            dataFetched: false as boolean,
            fetchingStarted: false as boolean,
            direktesendinger: {} as { [key: string]: any },
        }
    },
    computed: {
        getTabPosition() : number {
            if(this.tab == null) {
                return 0;
            }
            return this.getTabs().indexOf(this.tab);
        }
    },
    mounted() {
        this.fetchProgramData();
        this.testFetch();
    },
    methods : {
        getDirektesending(hendelseId: number): any {
            return this.direktesendinger[hendelseId] || null;
        },
        toggleTimeplanItem(tp: HendelseItem) {
            tp.toggleOpen();
        },
        openTimeplanItem(tp: HendelseItem) {
            // Only if the link is defined and not empty
            if(tp.getLink() != null && tp.getLink().length > 0) {
                window.open(tp.getLink(), '_blank');
            }
        },
        moveToTab(dayTab: string) {
            if(this.tab == dayTab) {
                return;
            }
            this.tab = dayTab;
        },
        _getDateKey(date: Date) : string {
            const day = this.daysOfWeek[date.getDay()];
            const dateDDMM = `${date.getDate().toString().padStart(2, '0')}.${(date.getMonth()+1).toString().padStart(2, '0')}`;
            return `${day} ${dateDDMM}`;
        },
        getTabs() : string[] {
            let retTabs : string[] = [];
            
            for(let tp of this.hendelseItems) {
                const hendelseIdStr = tp.getHendelseId().toString();
                if (!retTabs.includes(hendelseIdStr)) {
                    retTabs.push(hendelseIdStr);
                }
            }
            return retTabs;
        },
        
        getHendelseNameById(hendelseId: string): string {
            const hendelse = this.hendelser.find(h => h.id.toString() === hendelseId);
            return hendelse ? hendelse.title : `Hendelse ${hendelseId}`;
        },
        
        getPersonsForInnslag(hendelseItem: HendelseItem): any[] {
            const persons: any[] = [];
            
            // Find the innslag that corresponds to this HendelseItem
            for (let innslagId in this.alleInnslag) {
                const innslag = this.alleInnslag[innslagId];
                
                // Check if this innslag belongs to the same hendelse and has the same name as the HendelseItem
                if (innslag.hendelseId === hendelseItem.getHendelseId() && 
                    innslag.navn === hendelseItem.getTitle()) {
                    
                    // Find all persons for this innslag
                    for (let deltakerKey in this.deltakere) {
                        const deltaker = this.deltakere[deltakerKey];
                        if (deltaker.innslagId == innslag.id) {
                            persons.push(deltaker);
                        }
                    }
                    break;
                }
            }
            
            return persons;
        },
        
        getTitlesForInnslag(hendelseItem: HendelseItem): any[] {
            const titles: any[] = [];
            
            // Find the innslag that corresponds to this HendelseItem
            for (let innslagId in this.alleInnslag) {
                const innslag = this.alleInnslag[innslagId];
                
                // Check if this innslag belongs to the same hendelse and has the same name as the HendelseItem
                if (innslag.hendelseId === hendelseItem.getHendelseId() && 
                    innslag.navn === hendelseItem.getTitle()) {
                    
                    // Find all titles for this innslag
                    if (this.innslagTitler[innslag.id]) {
                        titles.push(...this.innslagTitler[innslag.id]);
                    }
                    break;
                }
            }
            
            return titles;
        },
        
        getHendelserTimeplan() : any {
            if(this.hendelseItems == null || this.hendelseItems.length == 0) {
                return [];
            }
            var timeplanMap: { [key: string]: HendelseItem[] } = {};
            this.hendelseItems.forEach((item : HendelseItem) => {
                const hendelseIdStr = item.getHendelseId().toString();
                if (!timeplanMap[hendelseIdStr]) {
                    timeplanMap[hendelseIdStr] = [];
                }
                timeplanMap[hendelseIdStr].push(item);
            });

            return timeplanMap;
        },
        generateTimeplan() {
            // Clear existing items
            this.hendelseItems = [];
            
            // Generate HendelseItem objects from alleInnslag which contains the actual innslag data
            let itemId = 1;
            
            for (let innslagId in this.alleInnslag) {
                let innslag = this.alleInnslag[innslagId];
                
                // Find the corresponding hendelse for this innslag
                let hendelse = this.hendelser.find(h => h.id === innslag.hendelseId);
                if (!hendelse) {
                    continue;
                }
                
                // Convert timestamps to Date objects
                let startDate: Date;
                let endDate: Date;
                
                if (innslag.start && innslag.start * 1000) {
                    startDate = new Date(innslag.start * 1000);
                } else {
                    // Fallback to hendelse start time
                    startDate = new Date(hendelse.start * 1000);
                }
                
                if (innslag.slutt && innslag.slutt * 1000) {
                    endDate = new Date(innslag.slutt * 1000);
                } else {
                    // If no end time, add 1 hour to start time
                    endDate = new Date(startDate.getTime() + 60 * 60 * 1000);
                }
                
                // Generate link to hendelse page
                const hendelseLink = `https://ukm.no/festivalen/single-hendelse/?hendelse-id=${hendelse.id}`;
                
                // Create the HendelseItem
                const hendelseItem = new HendelseItem(
                    itemId++,
                    innslag.navn || 'Uten navn',
                    hendelse.sted || '',
                    innslag.beskrivelse || hendelse.beskrivelse || '',
                    startDate,
                    endDate,
                    hendelseLink,
                    hendelse.id
                );
                
                this.hendelseItems.push(hendelseItem);
            }
            
            // Sort items by start time
            this.hendelseItems.sort((a, b) => {
                return a.getStartDate().getTime() - b.getStartDate().getTime();
            });
            
            console.log('Generated timeplan items:', this.hendelseItems);
            
            // Set the first tab as selected after data is loaded
            const tabs = this.getTabs();
            if (tabs.length > 0) {
                this.tab = tabs[0];
            } else {
                console.warn('No tabs available, please check your timeplan items.');
            }
        },
        async testFetch() {
            // let data = {};
            // var results = await this.spaInteraction.runAjaxCall('getLivestreaming.ajax.php', 'POST', data);
            // console.log('Test fetch results:', results);
        },
        async fetchProgramData() {
            this.fetchingStarted = true;
            this.dataFetched = false;

            var data = {
                erDeltakerProgram: this.programType == 'deltakere'
            };

            var results = await this.spaInteraction.runAjaxCall('getProgram.ajax.php', 'POST', data);
            
            this.hendelseMedAktiviteter = results.hendelseMedAktiviteter;
            this.innslagTitler = results.innslagTitler;
            this.direktesendinger = results.direktesendinger;

            for (let h of results.hendelser) {
                let alleInnslag = h.innslag;

                // Innslag og deltakere
                let innslagArrObj : {name : string, antallDeltakere : number, url : string}[] = [];
                let antallDeltakere = 0;

                let fylker : string[] = [];
                let deltakereNavn : string[] = [];

                for(let innslag of alleInnslag.innslag) {
                    antallDeltakere += results.innslagPersoner[innslag.id] ? results.innslagPersoner[innslag.id].length : 0;

                    if(innslag.fylke) {
                        fylker.push(innslag.fylke.navn);
                    }

                    innslagArrObj.push({
                        name: innslag.navn,
                        antallDeltakere: antallDeltakere,
                        url: 'inPerson.url'
                    });

                    if(results.innslagPersoner[innslag.id]) {
                        for(let person of results.innslagPersoner[innslag.id]) {
                            person.hendelse = h;
                            // Legger til innslagId i deltakeren
                            person.innslagId = innslag.id;
                            this.deltakere[person.id +'_'+ person.context.forestilling.id+ '_' + person.innslagId] = person;
                            deltakereNavn.push(person.fornavn + ' ' + person.etternavn);
                        }
                    }
                    innslag.hendelseId = h.id;
                    this.alleInnslag[innslag.id] = innslag;
                }

                var newHendelse = new Hendelse(
                    h.id, 
                    h.navn, 
                    h.bilde ?? 'http://ukm.no/wp-content/uploads/2025/04/40ukm.png', 
                    h.start, 
                    0, 
                    h.sted, 
                    h.tag, 
                    h.beskrivelse,
                    innslagArrObj,
                    fylker,
                    deltakereNavn,
                    h.visning,
                );
                this.hendelser.push(newHendelse);
            }
            this.dataFetched = true;
            this.fetchingStarted = false;
            
            // Generate timeplan items after data is fetched
            this.generateTimeplan();
            
            console.log('Fetched hendelser:', this.hendelser);
        },
    }
}

</script>

<style scoped>
.tplan-style {
    padding: calc(3*(var(--initial-space-box)));
    border-radius: var(--initial-space-box);
    text-align: center;
    min-height: 93px;
}
.tplan-style * {
    color: #fff;
}
.tplan-style.direktesending {
    display: block;
    background: #FFF056 !important;
}
.tplan-style.direktesending .title {
    margin: auto;
    margin-bottom: 12px;
    color: #00004C !important;
    font-size: 18px !important;
}
.timeplan-item {
    margin-bottom: var(--initial-space-box);
    background-color: #262667;
    cursor: pointer;
    transition: all 0.3s ease;
}
.timeplan-item.item-expanded {
    background-color: #47477E;
}
.item-header {
    position: relative;
}
.expand-indicator {
    position: absolute;
    top: 50%;
    right: 20px;
    transform: translateY(-50%);
    transition: transform 0.3s ease;
    color: #fff;
}
.expand-indicator.expanded {
    transform: translateY(-50%) rotate(180deg);
}
.item-content {
    margin-top: 16px;
    padding-top: 16px;
    border-top: 1px solid rgba(255, 255, 255, 0.2);
    animation: slideDown 0.3s ease;
}
.expanded-info .description {
    margin: 0 0 12px 0;
    font-size: 14px;
    text-align: left;
    color: #fff;

}
.persons-section {
    margin: 16px 0;
}
.titles-section {
    margin: 16px 0;
}
.titles-title {
    margin: 0 0 12px 0;
    font-size: 16px;
    color: #FFF056;
    font-weight: 600;
}
.titles-list {
    display: flex;
    flex-direction: column;
    gap: 8px;
}
.title-item {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 8px 12px;
    background-color: rgba(255, 255, 255, 0.1);
    border-radius: 6px;
    border-left: 3px solid #FFF056;
}
.title-name {
    font-weight: 500;
    color: #fff;
    flex: 1;
}
.title-duration {
    font-size: 12px;
    color: rgba(255, 255, 255, 0.7);
    font-weight: normal;
}
@media (max-width: 767px) {
    .title-item {
        padding: 6px 10px;
    }
    .title-name {
        font-size: 14px;
    }
    .title-duration {
        font-size: 11px;
    }
}
.persons-title {
    margin: 0 0 12px 0;
    font-size: 16px;
    color: #FFF056;
    font-weight: 600;
}
.persons-chips {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    align-items: center;
}
.person-chip {
    font-size: 12px !important;
    height: auto !important;
    padding: 8px 12px !important;
    background-color: #00004C !important;
    color: #fff !important;
}
.person-chip :deep(.v-chip__content) {
    display: flex;
    align-items: center;
    gap: 4px;
    font-weight: 500;
}
.person-name {
    font-weight: 500;
    padding-left: 4px;
}
.person-location {
    font-size: 11px;
    opacity: 0.8;
    font-weight: normal;
}
@media (max-width: 767px) {
    .persons-chips {
        gap: 6px;
    }
    .person-chip {
        font-size: 11px !important;
    }
}
.link-section {
    margin-top: 12px;
}
.external-link {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    color: #FFF056;
    text-decoration: none;
    font-size: 14px;
    transition: color 0.3s ease;
}
.external-link:hover {
    color: #fff;
}
.external-link svg {
    flex-shrink: 0;
}

@keyframes slideDown {
    from {
        opacity: 0;
        max-height: 0;
    }
    to {
        opacity: 1;
        max-height: 200px;
    }
}
.tabs-timeplan :deep(.v-btn__content) {
    font-weight: 700;
}
.timeplan-item:nth-child(odd) {
    background-color: #47477E;
}
.timeplan-item .title {
    margin: 0;
    font-size: calc(2*(var(--initial-space-box)));
}
.timeplan-item .time,
.timeplan-item .place {
    margin: 0;
    font-size: 14px;
}
.timeplan-item .time {
    margin: 12px 0;
    margin-bottom: 0;
}
.timeplan-item .place {
    margin-top: 12px;
    text-transform: uppercase;
}
.tabs-timeplan :deep(button.v-btn) {
    background: transparent !important;
    color: rgba(255, 255, 255, .6);
}
.tabs-timeplan :deep(button.v-tab-item--selected) {
    color: #FFF056 !important;
}
.tabs-timeplan :deep(button.v-tab-item--selected .v-tab__slider) {
    background: #FFF056 !important;
}
.tabs-timeplan :deep(.v-slide-group__content) {
    border-bottom: solid 1px rgba(255, 255, 255, .6);
}
.scroller-lines {
    display: flex;
    margin: auto 8px;
    background: #bebebe;
    width: 100%;
    height: 4px;
    border-radius: 2px;
}
.scroller-line.active {
    background: #FFF056;
}
.xs-scroller {
    display: none;
    position: relative;
    width: 100%;
}
.btn-scroller {
    background: rgba(255, 255, 255, .6);
    width: 16px !important;
    height: 16px !important;
    
}
.btn-scroller.left svg {
    transform: rotate(180deg);
    margin-left: -2px;
    margin-top: 0px;
}
@media (max-width: 767px) {
    .xs-scroller {
        display: flex;
    }
    .timeplan-item {
        padding: 10px;
    }
}



</style>