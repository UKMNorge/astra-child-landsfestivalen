<template> 
    <div>
        <div v-show="active == true">
            <h1>First Tab (FirstTab.vue)</h1>
            <!-- <ChildComponent ref="childArrangementer" :title="'Alfa'" />
            <ChildComponent ref="childArrangementer2" :title="'Beta'" /> -->
            <h2>Fra UKM Komponenter</h2>

        </div>
        <p>active: {{ active }}</p>
        <button @click="setActive()">show</button>
        <button @click="setDeactive()">hide</button>

    </div>
</template>
  
<script lang="ts">
// import ChildComponent from '../components/ChildComponent.vue';        

export default {
    data() {
        return {
            active: false
        }
    },

    components : {
        // ChildComponent : ChildComponent,

    },

    methods : {
        setActive() {
            this.active = true;
            // console.log(this.$refs);
            // (<typeof ChildComponent>this.$refs.childArrangementer).init();
        },
        setDeactive() {
            this.active = false;
        }
    }
}
</script>
