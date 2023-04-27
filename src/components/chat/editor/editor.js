// Javascript Document

(() => {
  return {
    props: {
      source: {
        type: Object,
        required: false
      }
    },
    data() {
      bbn.fn.log("EDIT", this.source);
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
            component: "bbn-textarea"
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
        languages: [
          {text: bbn._('Italian'), value: 'it'},
          {text: bbn._('French'), value: 'fr'},
          {text: bbn._('English'), value: 'en'},
        ],
        root: appui.plugins['appui-ai'] + '/',
        formData: {
          id: this.source.id || null,
          id_note: this.source.id_note || null,
          title: this.source.title || "",
          prompt: this.source.content || "",
          output: this.source.output || "textarea",
          input: this.source.input || "textarea",
          lang: this.source.language || "en"
        },
        input: "",
        response: null,
        loading: false,
        cp: null,
        generating: false
      }
    },
    computed: {
      aiFormatComponent() {
        let res = bbn.fn.getRow(this.formats, {value: this.formData.output}).component;
        if (res === 'bbn-textarea') {
          return "div";
        }
        return res;
      },
      userFormatComponent() {
        return bbn.fn.getRow(this.formats, {value: this.formData.input}).component;
      },
      userComponentOptions() {
        const o = {};
        if (this.formData.input) {
          switch (this.formData.input) {
            case 'textarea':
              o.autosize = true;
              break;
            case 'code-php':
              o.mode = 'php';
              o.autosize = true;
              o.fill = false
              break;
            case 'code-js':
              o.mode = 'js'
              o.fill = false
              break;
          }
        }
        return o;
      }
    },
    mounted() {
      this.cp = this.closest('bbn-container').getComponent();
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
              o.mode = 'php';
              o.fill = false
              break;
            case 'code-js':
              o.autosize = true;
              o.mode = 'js'
              o.fill = false
              break;
          }
        }
        return o;
      },
      success() {
        this.cp.updatePromptsList();
        this.cp.editMode = false;
      },
      isValid() {
        return true;
      },
      send() {
        this.loading = true;
        this.response = "";
        bbn.fn.post(this.root + 'chat', {
          prompt: this.formData.prompt + '\n' + bbn.fn.getRow(this.formats, {value: this.formData.output}).prompt + ' and the language must be in ' +  bbn.fn.getRow(this.languages, {value: this.formData.lang}).text,
          input: this.input,
          test: true
        }, (d) => {
          if (d.success) {
            this.response = this.formData.output === 'bbn-json-editor' ? JSON.parse(d.text) : d.text
          }
        })
        setTimeout(() =>  {
          this.loading = false;
        }, 300);
      },
      componentOptions(type, readonly) {
        let res = {
          readonly: readonly
        };
        if (type === 'bbn-code') {
          res.fill = false;
        }
        return res;
      },
      generateTitle() {
        this.generating = true;
        bbn.fn.post(this.root + 'chat', {
          prompt: "Please generate a clear and descriptive title for the following prompt:",
          input: this.formData.prompt,
          test: true
        }, (d) => {
          this.isLoading = false;
          if (d.success) {
            this.formData.title = d.text.trim().replace(/^\"(.+)\"$/,"$1");
          }
          setTimeout(() => {
            this.generating = false;
          }, 300);
        })
      }
    }
  }
})();