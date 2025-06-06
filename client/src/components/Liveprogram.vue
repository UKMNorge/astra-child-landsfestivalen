<template>
    <div v-if="dataFetched == true" class="program-container">
        <div class="row program-header">
            <template v-for="(events, dateKey) in programData.hendelserDato" :key="dateKey">
                <template v-for="(event) in events" :key="event.id">
                    <template v-if="event.synlig_i_rammeprogram">
                        <div class="point col-8 col-xs-10">
                            <div :class="['box-innslag', isPast(event.start) ? 'past' : '']">
                                <div v-if="isNow(event.start)" class="timeline-point now">
                                    <div class="blinking"></div>
                                </div>
                                <div v-else class="timeline-point">
                                    <div class="blinking"></div>
                                </div>
                                <div class="time">
                                    {{ isNow(event.start) ? "Nå" : formatTime(event.start) }}
                                </div>
                                <h4 class="title center">{{ event.navn }}</h4>
                                <p>{{ event.beskrivelse }}</p>
                                <div class="sted center">
                                    <div v-show="!expandedLocations.includes(event.id)" class="sted-btn collapsed" @click="toggleLocation(event.id)">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24">
                                            <path d="M12 2C7.589 2 4 5.589 4 9.995 3.971 16.44 11.696 21.784 12 22c0 0 8.029-5.56 8-12 0-4.411-3.589-8-8-8zm0 12c-2.21 0-4-1.79-4-4s1.79-4 4-4 4 1.79 4 4-1.79 4-4 4z"></path>
                                        </svg>
                                        {{ event.sted || "Ukjent sted" }}
                                    </div>
                                    <div v-if="expandedLocations.includes(event.id)" class="button-live-style as-margin-top-space-2">
                                        <a target="_blank" :href="getMapLink(event.sted)">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22">
                                                <path d="m2.295 12.707 8.978 9c.389.39 1.025.391 1.414.002l9.021-9a1 1 0 0 0 0-1.416l-9.021-9a.999.999 0 0 0-1.414.002l-8.978 9a.998.998 0 0 0 0 1.412zm6.707-2.706h5v-2l3 3-3 3v-2h-3v4h-2v-6z"></path>
                                            </svg>
                                            <span>Veibeskrivelse</span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="empty col-4 col-xs-2"></div>
                    </template>
                </template>
            </template>
        </div>
    </div>
</template>

<script lang="ts">
export default {
    mounted() {
        this.fetchProgramData();
    },
    data() {
        return {
            spaInteraction : (<any>window).spaInteraction, // Definert i main.ts
            dataFetched: false,
            fetchingStarted: false,
            programData: {} as any,
            expandedParticipants: [] as string[],
            expandedLocations: [] as string[],
            timeNow: Date.now() / 1000,
        };
    },
    methods: {
        async fetchProgramData() {
            this.fetchingStarted = true;
            this.dataFetched = false;

            var data = {
                unike: true
            };

            var results = await this.spaInteraction.runAjaxCall('getLiveprogram.ajax.php', 'GET', data);

            this.programData = results;
            this.dataFetched = true;

            return results;
        },
        isNow(start: string) {
            const startTime = Number(start);
            return this.timeNow > startTime && this.timeNow < startTime + 7200;
        },
        isPast(start: string) {
            const startTime = Number(start);
            return this.timeNow > startTime + 7200;
        },
        formatTime(start: string) {
            const eventTime = new Date(Number(start) * 1000);

            // Extract and format day, month, hour, and minute
            const day = eventTime.getDate().toString().padStart(2, "0");
            const month = (eventTime.getMonth() + 1).toString().padStart(2, "0");
            const hour = eventTime.getHours().toString().padStart(2, "0");
            const minute = eventTime.getMinutes().toString().padStart(2, "0");

            return `${day}.${month} ${hour}:${minute}`;
        },
        toggleParticipants(id: string) {
            const index = this.expandedParticipants.indexOf(id);
            if (index === -1) {
                this.expandedParticipants.push(id);
            } else {
                this.expandedParticipants.splice(index, 1);
            }
        },
        toggleLocation(id: string) {
            const index = this.expandedLocations.indexOf(id);
            if (index === -1) {
                this.expandedLocations.push(id);
            } else {
                this.expandedLocations.splice(index, 1);
            }
        },
        getMapLink(location: string) {
            return `https://www.google.com/maps/search/?api=1&query=${encodeURIComponent(location)}+Trondheim`;
        },
    },
};
</script>



<style scoped>
.live-program-info {
    visibility: none;
    display: none;
}
.program-enkel-visning-header {
    background: #00004B;
    padding: 100px 0;
    margin: 0;
    overflow: hidden;
    box-shadow: 0px 0px 17px 4px #00004b36;
    border-top-left-radius: 20px;
    border-top-right-radius: 20px;
}
.point {
    height: auto;
    min-height: 300px;
    padding: 30px;
    display: flex;
}
.point.dag {
    border: none !important;
    width: 10%;
    padding: 0;
    min-height: 30px !important;
}
.point.dag span {
    color: #fff !important;
    width: 100%;
    position: absolute;
    left: 0;
    right: 0;
    text-align: center;
    box-shadow: 1px 1px 19px 30px #00004B;
    z-index: 1;
    background: #00004B;
}
.point.dag:nth-child(odd) {
    margin-left: 40%;
    text-align: right;
}
.point.dag:nth-child(even) {
    margin-right: 40%;
}
.point.empty {
    min-height: 70px;
}
.point:nth-child(odd) {
    border-right: 4px solid #4b546f8f;
    padding-left: 15px;
}
.point:nth-child(even) {
    border-left: 1px dashed #4b546f;
    padding-right: 15px;
}
.point .box-innslag.tidligere {
    opacity: .4;
    transition: .2s opacity;
}
.point .box-innslag.tidligere:hover,
.point .box-innslag.tidligere:active {
    opacity: 1;
    transition: .2s opacity;
}
.point .box-innslag {
    position: relative;
    height: auto;
    width: 100%;
    max-width: 500px;
    min-height: 100px;
    margin: auto;
    margin-top: 40px;
    margin-bottom: 40px;
    border-radius: 5px;
    background: #fff;
    color: #00004B;
    padding: 30px 20px;
}
.point .box-innslag a {
    text-decoration: none;
    color: #00004B;
    box-shadow: 0px 0px 3px #00000038;
    padding: 10px;
}
.point .box-innslag:hover {
    box-shadow: 0px 0px 7px 1px #ffffff94;
}
.point .box-innslag::after {
    position: absolute;
    content: url("data:image/svg+xml; utf8, <svg xmlns='http://www.w3.org/2000/svg' width='24' height='24' style='fill: rgb(255, 255, 255); transform: ;msFilter:;'><path d='m4.431 12.822 13 9A1 1 0 0 0 19 21V3a1 1 0 0 0-1.569-.823l-13 9a1.003 1.003 0 0 0 0 1.645z'></path></svg>");
    height: 10px;
    width: 10px;
    top: 13px;
}
.point:nth-child(even) .box-innslag::after {
    left: -15px;
}
.point:nth-child(odd) .box-innslag::after {
    left: auto;
    right: 0;
    margin-right: -15px;
    transform: rotate(180deg);
    top: 25px;
}
.point:nth-child(even) .box-innslag {
    margin-left: 0;
}
.point:nth-child(odd) .box-innslag {
    margin-right: 0;
}
.point .box-innslag .timeline-point {
    height: 25px;
    width: 25px;
    background: #00004B !important;
    position: absolute;
    top: 10px;
    left: -44px;
    border-radius: 50%;
    border: solid 4px #4b546f;
    z-index: 1;
    overflow: hidden;
    box-shadow: 0px 0px 0px 6px #00004b;
}
.point .box-innslag .title {
    font-size: 30px;
    margin-top: 0;
}
.point .box-innslag .sted {
    font-size: 12px;
    padding-bottom: 40px !important;

}
.point .box-innslag .timeline-point.now .blinking {
    background: #008000 !important;
    animation: blinker 2s cubic-bezier(0, 1.98, 0.32, 1.28) infinite;
    height: 80%;
    width: 80%;
    border-radius: 50%;
    margin: 2px;
    box-shadow: 0px 0px 6px 1px green;
    border: solid 2px #006300;
}
.point .box-innslag .beskrivelse {

}
.alle-deltakere-div {
    padding-top: 30px;
    display: flex;
}
.sm-size-icon {
    display: none;
}

@keyframes blinker {
    50% {
        opacity: .3;
    }
}
.har-sending-div {

}

.button-live-style {
    margin-top: 15px;
}
.button-live-style a {
    padding: 4px 8px;
    border-radius: 5px;
}
.button-live-style a:hover {
    /* cursor: pointer;
    background: linear-gradient(45deg, hsl(var(--color-primary-blue-800, var(--color-primary-orange-900))), hsl(var(--color-primary-blue-600, var(--color-primary-orange-900)))) !important;
    color: #fff; */
}
.button-live-style a svg {
    margin-right: 10px;
    margin-bottom: 5px;
}
.button-live-style .icon {
    margin-right: 5px;
    vertical-align: text-bottom;
}
.innslag-navn {
    margin-top: 20px;
    font-size: 18px;
}
.innslag-navn:nth-child(1) {
    margin-top: 0;
}

.point:nth-child(odd) .box-innslag .timeline-point {
    left: auto;
    right: -44px;
}
.vis-alle-deltakere {
    background: #fff;
    color: #00004B !important;
    margin: auto;
    width: 50px;
    transition: .2s !important;
    padding: 10px;
    border: none;
    display: flex;
    border: solid 2px #00004B;
}
.vis-alle-deltakere .icon-chevron {
    margin: auto;
    padding: 0 !important;
}
.vis-alle-deltakere .icon-chevron svg {
    margin-top: -5px;
}
.vis-alle-deltakere.collapsed .icon-chevron svg {
    transform: rotate(180deg);
}
.vis-alle-deltakere:hover .icon-chevron svg {
    fill: #fff !important;
}

.point .box-innslag .time {
    position: absolute;
    right: -260px;
    top: 12px;
    width: 200px;
    color: #fff;
}
.point:nth-child(even) .box-innslag .time {
    right: auto;
    left: -260px;
    text-align: right;
}
.deltakere {
    margin: 0 !important;
}
.deltakere .deltaker {
    padding-left: 20px;
    font-size: 14px;
}
.deltakere .deltaker span {
    vertical-align: middle;
}
.deltakere .wshop-href {
    display: block;
    margin-top: 20px;
    border: solid 2px transparent;
    padding: 8px;
    transition: .1s;
    border-radius: 5px;
    transition: .1s;
}
.deltakere .wshop-href:hover {
    border-color: #00004B;
    transition: .1s;
}
.innslag-type {
    padding: 5px 10px;
    width: 100%;
    background: #fff;
    color: #00004B;
    border-radius: 10px;
}
.alle-innslag {
    list-style-type: disc;
    padding-left: 25px;
}
.sted-btn {
    height: 0;
    visibility: hidden;
    transition: .5s height;
}
.sted-btn.collapsed {
    height: auto;
    visibility: visible;
    transition: .5s height;
}
.sted-btn:hover {
    cursor: pointer;
}

@media (max-width: 992px) {
    .vis-alle-deltakere .icon-chevron svg {
        margin-top: 0;
    }
    .program-header {
        padding: 0px 20px;
    }
}

@media (max-width: 720px) {
    .point .box-innslag .title {
        font-size: 20px;
        margin-top: 10px;
    }
    .point .box-innslag {
        padding: 20px;
    }
    .point .box-innslag .beskrivelse {
        font-size: 14px;
    }
    .innslag-navn {
        font-size: 13px;
    }
    .deltakere .deltaker {
        padding-left: 0px;
        font-size: 11px;
    }
    .deltakere .wshop-href {
        padding: 8px 8px;
    }
    .deltakere .deltaker .sm-size-icon {
        display: inline;
    }
    .deltakere .deltaker .icon {
        display: none;
    }
    .deltakere.workshops .title-workshops {
        font-size: 15px;
    }
    a.wshop-href svg {
        width: 20px !important;
    }
    .point {
        flex: 0 0 75% !important;
        max-width: 75% !important;
    }
}

@media (max-width: 540px) {
    .point .box-innslag {
        padding: 20px 10px;
    }
    .point .box-innslag .title {
        font-size: 15px;
        margin-top: 5px;
    }
    .point .box-innslag .beskrivelse {
        font-size: 12px;
    }
    .point:nth-child(odd) {
        padding-right: 15px;
    }
    .point:nth-child(even) {
        padding-left: 15px;
    }
    .point .box-innslag .timeline-point {
        height: 17px;
        width: 17px;
        top: 14px;
    }
    .point .box-innslag .sted {
        margin-bottom: 0;
    }
    .point:nth-child(odd) .box-innslag .timeline-point {
        right: -25px;
    }
    .point .box-innslag .timeline-point {
        left: -25px;
    }
    .point:nth-child(odd) .box-innslag::after {
        margin-right: -8px;
    }
    .point:nth-child(even) .box-innslag::after {
        left: -8px;
        top: 12px;
    }
    .point .box-innslag .timeline-point.now .blinking {
        margin: 1px;
    }
    .point .box-innslag .time {
        right: -240px;
        font-size: 12px;
    }
    .point:nth-child(even) .box-innslag .time {
        left: -240px;
    }
    .button-live-style {
        font-size: 14px !important;
        margin: 7px 0;
    }
    a.wshop-href svg {
        width: 17px !important;
    }


}
.program-container {
background: #00004b;
color: white;
}
.past {
opacity: 0.5;
}
</style>