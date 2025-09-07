// Javascript Document

(() => {

  return {
    props: {
      source: {
        type: Object,
        required: true
      },
      ai: {
        type: Boolean
      },
      format: {
        type: String,
        required: true
      },
      date: {
        type: Number,
        required: true
      },
      cfg: {
        type: Object
      }
    },
    data() {
      return {
        formats: appui.getRegistered('appui-ai-ui').source.formats.options,
        cp: null
      }
    },
    computed: {
      content() {
        return this.source.content.replaceAll(/<think.*?<\/think>/gis, '').trim();
      },
      formatComponent() {
        return bbn.fn.getRow(this.formats, {code: this.format}).component;
      },
      formatReader() {
        return bbn.fn.getRow(this.formats, {code: this.format}).reader;
      },
      fdate() {
        return bbn.fn.fdate(this.date, true)
      }
    },
    methods: {
      seeRequest() {
        if (this.cfg) {
          this.closest('bbn-container').getPopup({
            label: false,
            content: '<pre class="bbn-padding">' + JSON.stringify(this.cfg, null, 2) + '</pre>'
          })
        }
      },
      componentOptions(type, readonly) {
        const o = {
          readonly: readonly
        };
        if (type) {
          switch (type) {
            case 'textarea':
              o.autosize = true;
              break;
            case 'code-php':
              o.autosize = true;
              o.theme = 'dracula';
              o.mode = 'php';
              o.fill = false
              break;
            case 'code-js':
              o.autosize = true;
              o.theme = 'dracula';
              o.mode = 'js'
              o.fill = false
              break;
          }
        }
        return o;
      },
      copy() {
        bbn.fn.copy(this.source.content);
        appui.success(bbn._("Copied"));
      }
    },
    mounted() {
      this.cp = this.closest('appui-ai-chat');
    }
  }
})();