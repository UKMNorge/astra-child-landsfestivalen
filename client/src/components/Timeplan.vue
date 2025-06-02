<template>
    <div>
        <v-tabs v-model="tab" class="tabs-timeplan">
            <v-tab v-for="dayTab in getTabs()" :key="dayTab" :value="dayTab">{{ dayTab }}</v-tab>
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
        

        <v-card-text class="nop-impt as-margin-top-space-4">
            <v-tabs-window v-model="tab">
                <v-tabs-window-item v-for="timeplanItem, key in getTimeplan()" :value="key" :key="key">
                    <div>
                        <div class="timeplan-item tplan-style dag">
                            <h3 class="title">{{ key }}</h3>
                        </div>
                    </div>

                    <div>
                        <div @click="openTimeplanItem(tp)" v-for="tp in timeplanItem" :class="{'item-with-link' : tp.getLink() != null}" class="timeplan-item tplan-style" :key="tp.getId()">
                            <h3 class="title">{{ tp.getTitle() }}</h3>
                            <p class="time">{{ tp.getStartEndTimeHumanReadable() }}</p>
                            <p v-show="tp.getPlace().length > 0" class="place">{{ tp.getPlace() }}</p>
                        </div>
                    </div>
                </v-tabs-window-item>
            </v-tabs-window>
        </v-card-text>
    </div>
</template>
<script lang="ts">
import TimeplanItem from '../objects/TimeplanItem';

export default {
    components: {

    },
    data() {
        return {
            spaInteraction : (<any>window).spaInteraction, // Definert i main.ts
            tab: null as string | null,
            timeplanItems: [] as TimeplanItem[],
            daysOfWeek: ['Søndag', 'Mandag', 'Tirsdag', 'Onsdag', 'Torsdag', 'Fredag', 'Lørdag'],

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
        this.fetchTimeplan();
        if(this.getTabs().length == 0) {
            console.warn('No tabs available, please check your timeplan items.');
            return;
        }
        this.tab = this.getTabs()[0]; // Set initial tab to the first available tab
    },
    methods : {
        openTimeplanItem(tp: TimeplanItem) {
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
            
            for(let tp of this.timeplanItems) {
                if (!retTabs.includes(this._getDateKey(tp.getStartDate()))) {
                    retTabs.push(this._getDateKey(tp.getStartDate()));
                }
            }
            return retTabs;
        },
        
        getTimeplan() : any {
            if(this.timeplanItems == null || this.timeplanItems.length == 0) {
                return [];
            }
            var timeplanMap: { [key: string]: TimeplanItem[] } = {};
            this.timeplanItems.forEach((item : TimeplanItem) => {
                if (!timeplanMap[this._getDateKey(item.getStartDate())]) {
                    timeplanMap[this._getDateKey(item.getStartDate())] = [];
                }
                timeplanMap[this._getDateKey(item.getStartDate())].push(item);
            });

            return timeplanMap;
        },
        async fetchTimeplan() {
            this.timeplanItems.push(
                // Lørdag 21.06
                new TimeplanItem(1, 'Kreativ tid & bli kjent', 'OLAVSHALLEN', '', new Date('2025-06-21T16:00:00'), new Date('2025-06-21T19:30:00'), 'https://ukm.no/festivalen/single-hendelse/?hendelse-id=18846'),
                new TimeplanItem(2, 'Åpning av kunstutstilling', '', '', new Date('2025-06-21T19:30:00'), new Date('2025-06-21T20:00:00'), 'https://ukm.no/festivalen/single-hendelse/?hendelse-id=18258'),
                new TimeplanItem(3, 'Åpningsforestilling', 'OLAVSHALLEN', '', new Date('2025-06-21T20:30:00'), new Date('2025-06-21T21:30:00'), 'https://ukm.no/festivalen/single-hendelse/?hendelse-id=18842'),
                new TimeplanItem(4, 'Bli kjent kveld', 'SPEKTRUM', '', new Date('2025-06-21T23:00:00'), new Date('2025-06-21T00:30:00'), 'https://ukm.no/festivalen/single-hendelse/?hendelse-id=18843'),

                // Søndag 22.06
                new TimeplanItem(5, 'Morgenaktiviteter', 'SPEKTRUM', '', new Date('2025-06-22T07:00:00'), new Date('2025-06-22T08:30:00')),
                new TimeplanItem(6, 'Morgenmøter', 'OLAVSHALLEN', '', new Date('2025-06-22T09:00:00'), new Date('2025-06-22T09:30:00'), 'https://ukm.no/festivalen/deltakerprogram/?filter-dag=S%C3%B8ndag'),
                new TimeplanItem(7, 'Workshops og kreativ tid', 'OLAVSHALLEN', '', new Date('2025-06-22T09:30:00'), new Date('2025-06-22T12:00:00'), 'https://ukm.no/festivalen/single-hendelse/?hendelse-id=18848'),
                new TimeplanItem(8, 'Forestilling 2', 'OLAVSHALLEN', '', new Date('2025-06-22T12:30:00'), new Date('2025-06-22T14:30:00'), 'https://ukm.no/festivalen/single-hendelse/?hendelse-id=18859'),
                new TimeplanItem(9, 'Workshops og kreativ tid', 'OLAVSHALLEN', '', new Date('2025-06-22T15:00:00'), new Date('2025-06-22T17:30:00'), 'https://ukm.no/festivalen/single-hendelse/?hendelse-id=18847'),
                new TimeplanItem(10, 'Forestilling 3', 'OLAVSHALLEN', '', new Date('2025-06-22T18:00:00'), new Date('2025-06-22T19:30:00'), 'https://ukm.no/festivalen/single-hendelse/?hendelse-id=18858'),
                new TimeplanItem(11, 'Waterparty', 'PIRBADET', '', new Date('2025-06-22T20:00:00'), new Date('2025-06-22T22:00:00'), 'https://ukm.no/festivalen/single-hendelse/?hendelse-id=18854'),
                new TimeplanItem(12, 'Spillquest-show', 'OLAVSHALLEN', '', new Date('2025-06-22T20:00:00'), new Date('2025-06-22T21:30:00'), 'https://ukm.no/festivalen/single-hendelse/?hendelse-id=18855'),
                new TimeplanItem(13, 'Nattkino', 'SPEKTRUM', '', new Date('2025-06-22T23:00:00'), new Date('2025-06-22T00:30:00'), 'https://ukm.no/festivalen/single-hendelse/?hendelse-id=18866'),

                // Mandag 23.06
                new TimeplanItem(14, 'Morgenaktiviteter', 'SPEKTRUM', '', new Date('2025-06-23T07:00:00'), new Date('2025-06-23T08:30:00')),
                new TimeplanItem(15, 'Morgenmøter', 'OLAVSHALLEN', '', new Date('2025-06-23T09:00:00'), new Date('2025-06-23T09:30:00'), 'https://ukm.no/festivalen/deltakerprogram/?filter-dag=Mandag'),
                new TimeplanItem(16, 'Workshops og kreativ tid', 'OLAVSHALLEN', '', new Date('2025-06-23T09:30:00'), new Date('2025-06-23T12:00:00'), 'https://ukm.no/festivalen/single-hendelse/?hendelse-id=18863'),
                new TimeplanItem(17, 'Forestilling 4', 'OLAVSHALLEN', '', new Date('2025-06-23T12:30:00'), new Date('2025-06-23T14:30:00'), 'https://ukm.no/festivalen/single-hendelse/?hendelse-id=18853'),
                new TimeplanItem(18, 'Workshops og kreativ tid', 'OLAVSHALLEN', '', new Date('2025-06-23T15:00:00'), new Date('2025-06-23T17:30:00'), 'https://ukm.no/festivalen/single-hendelse/?hendelse-id=18864'),
                new TimeplanItem(19, 'Forestilling 5', 'OLAVSHALLEN', '', new Date('2025-06-23T18:00:00'), new Date('2025-06-23T20:00:00'), 'https://ukm.no/festivalen/single-hendelse/?hendelse-id=18852'),
                new TimeplanItem(20, 'Akustisk konsert', 'VÅR FRUE KIRKE', '', new Date('2025-06-23T21:00:00'), new Date('2025-06-23T21:45:00'), 'https://ukm.no/festivalen/single-hendelse/?hendelse-id=18857'),
                new TimeplanItem(21, 'Karaoke-Cup', 'SPEKTRUM', '', new Date('2025-06-23T23:00:00'), new Date('2025-06-23T00:30:00'), 'https://ukm.no/festivalen/single-hendelse/?hendelse-id=18867'),

                // Tirsdag 24.06
                new TimeplanItem(22, 'Morgenaktiviteter', 'SPEKTRUM', '', new Date('2025-06-24T07:00:00'), new Date('2025-06-24T08:30:00')),
                new TimeplanItem(23, 'Morgenmøter', 'OLAVSHALLEN', '', new Date('2025-06-24T09:00:00'), new Date('2025-06-24T09:30:00'), 'https://ukm.no/festivalen/deltakerprogram/?filter-dag=Tirsdag'),
                new TimeplanItem(24, 'Workshops og kreativ tid', 'OLAVSHALLEN', '', new Date('2025-06-24T09:30:00'), new Date('2025-06-24T12:00:00'), 'https://ukm.no/festivalen/single-hendelse/?hendelse-id=18849'),
                new TimeplanItem(25, 'Forestilling 6', 'OLAVSHALLEN', '', new Date('2025-06-24T12:30:00'), new Date('2025-06-24T14:30:00'), 'https://ukm.no/festivalen/single-hendelse/?hendelse-id=18862'),
                new TimeplanItem(26, 'Fritid og opplevelser', 'Rundt i byen', '', new Date('2025-06-24T15:00:00'), new Date('2025-06-24T17:00:00'), 'https://ukm.no/festivalen/single-hendelse/?hendelse-id=18845'),
                new TimeplanItem(27, 'Forestilling 7', 'OLAVSHALLEN', '', new Date('2025-06-24T17:00:00'), new Date('2025-06-24T18:30:00'), 'https://ukm.no/festivalen/single-hendelse/?hendelse-id=18861'),
                new TimeplanItem(28, 'PETER PAN - Stargate to Neverland', 'NYE HJORTEN TEATER', '', new Date('2025-06-24T19:30:00'), new Date('2025-06-24T22:30:00'), 'https://ukm.no/festivalen/single-hendelse/?hendelse-id=18865'),
                new TimeplanItem(29, 'Avskjedsdisco', 'SPEKTRUM', '', new Date('2025-06-24T23:30:00'), new Date('2025-06-24T01:00:00'), 'https://ukm.no/festivalen/single-hendelse/?hendelse-id=18856'),
            );
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
.tplan-style.dag {
    display: flex;
    background: #FFF056 !important;
}
.tplan-style.dag .title {
    margin: auto;
    color: #00004C !important;
    font-size: 18px !important;
}
.timeplan-item {
    margin-bottom: var(--initial-space-box);
    background-color: #262667;
}
.timeplan-item.item-with-link:hover {
    cursor: pointer;
    opacity: 0.8;
    transition: background-color 0.3s ease;
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
}



</style>