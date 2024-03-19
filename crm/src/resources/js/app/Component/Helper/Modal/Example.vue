<template>
   <div>
       <a-modal
           v-model="showModal"
           label="This is title"
           @saveClicked="saveFunction"
           :hide-header="true"
           :hide-footer="false"
       >
           <div class="form-group">
               <label>Input</label>
               <input type="text" class="form-control" placeholder="Input" />
           </div>
       </a-modal>
       <button @click="showModal = true">Open modal</button>
   </div>
</template>

<script>
    export default {
        name: "Example",
        data() {
            return {
                showModal: false,
            }
        },
        components: {
            'a-modal': require("../../Helper/Modal/ModalComponent").default
        },
        methods: {
            saveFunction(){
                alert("Clicked");
            }
        }
    }
</script>

<style scoped>

</style>
