<template>
  <div>
    <h1>I am Deltakere komponent</h1>
  </div>
</template>
<script lang="ts">
import MyObject from '../objects/myObject'

export default {
    props: {
        title: String,
    },
    data() {
        return {

            myObject : new MyObject()
        }
    },
    methods : {
        init(){
            console.log('ChildComponent.vue: init()');
            var myObjMethod = this.myObject.sayHello();
            console.log('From MyObject: ' + myObjMethod);
            

        }
    }
}

</script>