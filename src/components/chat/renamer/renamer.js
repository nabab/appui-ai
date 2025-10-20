// Javascript Document

(() => {
  return {
    data() {
      return {
        root: appui.plugins['appui-ai'] + '/'
      }
    },
    methods: {
      onSuccess(d) {
        this.$emit('success', d);
        appui.success("Renaming ok")
      }
    },
    mounted() {
      bbn.fn.log("RENAMER MOUNTED");
      this.$nextTick(() => this.getRef('input').selectText())
    }
  }
})();
