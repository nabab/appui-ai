// Javascript Document

(() => {
  return {
    props: {
      source: {
      	type: Object,
        required: true
	    },
      cp: null
    },
    computed: {
      fdate() {
        return bbn.fn.fdate(this.source.creation_date, true)
      }
    },
    methods: {
      copy() {
        bbn.fn.copy(this.source.text);
        appui.success(bbn._("Copied"));
      }
    },
    mounted() {
      this.cp = this.closest('appui-ai-chat');
    }
  }
})();