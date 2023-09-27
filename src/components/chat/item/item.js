// Javascript Document

(() => {

  return {
    props: {
      source: {
        type: Object,
        required: true
      },
      aiFormat: {
        type: String,
        required: true
      },
      userFormat: {
        type: String,
        required: true
      }
    },
    data() {
      return {
        formats: [
          {
            value: "rte",
            text: "Rich Text Editor",
            prompt: "Your response needs to be in rich text format",
            component: "bbn-rte"
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
            component: "bbn-input"
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
      aiFormatComponent() {
        return bbn.fn.getRow(this.formats, {value: this.aiFormat}).component;
      },
      userFormatComponent() {
        return bbn.fn.getRow(this.formats, {value: this.userFormat}).component;
      },
      fdate() {
        return bbn.fn.fdate(this.source.creation_date, true)
      }
    },
    methods: {
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
        bbn.fn.copy(this.source.text);
        appui.success(bbn._("Copied"));
      }
    },
    mounted() {
      this.cp = this.closest('appui-ai-chat');
    }
  }
})();