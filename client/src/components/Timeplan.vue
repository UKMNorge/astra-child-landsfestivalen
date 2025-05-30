<template>
    <div>
        <h1 style="color: red">{{ tab }}</h1>

        <pre>
            {{ getTimeplan() }}
        </pre>

        <v-card>
            <v-tabs v-model="tab">
                <v-tab v-for="dayTab in getTabs()" :key="dayTab" :value="dayTab">{{ dayTab }}</v-tab>
            </v-tabs>

            <v-card-text>
                <v-tabs-window v-model="tab">
                    <v-tabs-window-item v-for="tp, key in getTimeplan()" :value="key">
                        <pre>{{ key }}</pre>
                        <pre>{{ tp }}</pre>
                    </v-tabs-window-item>

                    <!-- <v-tabs-window-item value="one">
                        <div>
                            <div class="timeplan-item tplan-style dag">
                                <h3 class="title">Kreativ tid og bli kjent</h3>
                            </div>
                        </div>

                        <div>
                            <div class="timeplan-item tplan-style">
                                <h3 class="title">Kreativ tid og bli kjent</h3>
                                <p class="time">10:00 - 11:00</p>
                                <p class="place">Storsalen</p>
                            </div>

                            <div class="timeplan-item tplan-style">
                                <h3 class="title">Kreativ tid og bli kjent</h3>
                                <p class="time">10:00 - 11:00</p>
                                <p class="place">Storsalen</p>
                            </div>

                            <div class="timeplan-item tplan-style">
                                <h3 class="title">Kreativ tid og bli kjent</h3>
                                <p class="time">10:00 - 11:00</p>
                                <p class="place">Storsalen</p>
                            </div>

                            <div class="timeplan-item tplan-style">
                                <h3 class="title">Kreativ tid og bli kjent</h3>
                                <p class="time">10:00 - 11:00</p>
                                <p class="place">Storsalen</p>
                            </div>
                        </div>

                    </v-tabs-window-item>

                    <v-tabs-window-item value="two">
                    Two
                    </v-tabs-window-item>

                    <v-tabs-window-item value="three">
                    Three
                    </v-tabs-window-item> -->
                </v-tabs-window>
            </v-card-text>
        </v-card>
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
    mounted() {
        this.fetchTimeplan();
        if(this.getTabs().length == 0) {
            console.warn('No tabs available, please check your timeplan items.');
            return;
        }
        this.tab = this.getTabs()[0]; // Set initial tab to the first available tab
    },
    methods : {
        getTabs() : string[] {
            let retTabs : string[] = [];
            
            for(let tp of this.timeplanItems) {
                const dag = this.daysOfWeek[tp.getStartDate().getDay()];
                const date = tp.getStartDate();
                const dateDDMM = `${date.getDate().toString().padStart(2, '0')}.${(date.getMonth()+1).toString().padStart(2, '0')}`;
                if (!retTabs.includes(dag+ ' ' +dateDDMM)) {
                    retTabs.push(dag+ ' ' +dateDDMM);
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
                // Use day as key DD format
                // const dateKey = item.getStartDate().getDay();
                const dag = this.daysOfWeek[item.getStartDate().getDay()];
                const date = item.getStartDate();
                const dateDDMM = `${date.getDate().toString().padStart(2, '0')}.${(date.getMonth()+1).toString().padStart(2, '0')}`;

                if (!timeplanMap[dag+ ' ' +dateDDMM]) {
                    timeplanMap[dag+ ' ' +dateDDMM] = [];
                }
                timeplanMap[dag+ ' ' +dateDDMM].push(item);
            });

            return timeplanMap;
        },
        async fetchTimeplan() {
            this.timeplanItems.push(new TimeplanItem(
                1,
                'Kreativ tid og bli kjent',
                'Olavssalen',
                '',
                new Date('2023-10-01T10:00:00'),
                new Date('2023-10-01T11:00:00'),
            ));
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
}
.timeplan-item .place {
    text-transform: uppercase;
}

</style>