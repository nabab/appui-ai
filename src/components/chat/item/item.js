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
        formats: [
          {
            value: "rte",
            text: "Rich Text Editor",
            prompt: "Your response needs to be in rich text format",
            component: "div",
          },
          {
            value: "markdown",
            text: "Markdown",
            prompt: "Your response needs to be in Markdown format",
            component: "bbn-markdown"
          },
          {
            value: "textarea",
            text: "Text Multiline",
            prompt: "Your response needs to be entered as multiple lines of text",
            component: "div"
          },
          {
            value: "code-php",
            text: "Code PHP",
            prompt: "Your response needs to be a code snippet",
            component: "bbn-code"
          },
          {
            value: "code-js",
            text: "Code JS",
            prompt: "Your response needs to be a code snippet",
            component: "bbn-code"
          },
          {
            value: "single-line",
            text: "Single Line",
            prompt: "Your response needs to be entered as a single line of text",
            component: "div",
          },
          {
            value: "text",
            text: "Simple text format",
            prompt: "Your response needs to be entered as simple text format",
            component: "div"
          },
          {
            value: "json-editor",
            text: "JSON",
            prompt: "Your response needs to be a valid JSON object",
            component: "bbn-json-editor"
          }
        ],
        cp: null
      }
    },
    computed: {
      formatComponent() {
        return bbn.fn.getRow(this.formats, {value: this.format}).component;
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