<template>
  <div>
    <VueMultiselect
  v-model="taggingSelected"
  :options="taggingOptions"
  :multiple="true"
  :taggable="true"
  @tag="addTag"
  tag-placeholder="Add this as new tag"
  placeholder="Type to search or add tag"
  label="name"
  track-by="code"
/>
  </div>
</template>

<script>
import VueMultiselect from 'vue-multiselect';

export default {
  components: {
    VueMultiselect
    
  },
  
  data () {
    return {
      taggingSelected: [
      ],
      taggingOptions: [
        {name: 'Vue.js', code: 'vu'},
        {name: 'Javascript', code: 'js'},
        {name: 'Open Source', code: 'os'}
      ]
    }
  },
  methods: {
    addTag (newTag) {
      const tag = {
        name: newTag,
        code: newTag.substring(0, 2) + Math.floor((Math.random() * 10000000))
      }
      this.taggingOptions.push(tag);
      this.taggingSelected.push(tag);
    }
  }
}
</script>

<style src="vue-multiselect/dist/vue-multiselect.css"></style>

<style>

</style>