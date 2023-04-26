// Javascript Document

(() => {
  return {
    props: {
      source: {
        type: Object,
        required: true
      },
      outputType: {
        type: String,
        required: true
      },
      inputType: {
        type: String,
        required: true
      }
    },
    data() {
      return {
        cp: null
      }
    },
    computed: {
      fdate() {
        return bbn.fn.fdate(this.source.creation_date, true)
      }
    },
    methods: {
      componentOptions(type, readonly) {
        let res = {
          readonly: readonly
        };
        if (type === 'bbn-code') {
          res.fill = false;
        }
        return res;
      },
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