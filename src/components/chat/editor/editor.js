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
        promptType: [
          {
            value: "bbn-rte",
            text: "Rich Text Editor",
            prompt: "Your response needs to be in rich text format"
          },
          {
            value: "bbn-markdown",
            text: "Markdown",
            prompt: "Your response needs to be in Markdown format"
          },
          {
            value: "div",
            text: "Text Multiline",
            prompt: "Your response needs to be entered as multiple lines of text"
          },
          {
            value: "bbn-code",
            text: "Code",
            prompt: "Your response needs to be pure code"
          },
          {
            value: "bbn-input",
            text: "Single Line",
            prompt: "Your response needs to be entered as a single line of text"
          },
          {
            value: "bbn-json-editor",
            text: "JSON",
            prompt: "Your response needs to be a valid JSON object"
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
          output: this.source.output || "div",
          input: this.source.input || "div",
          lang: this.source.language || "en"
        },
        input: "",
        response: null,
        loading: false,
        cp: null,
        generating: false
      }
    },
    mounted() {
      this.cp = this.closest('bbn-container').getComponent();
    },
    methods: {
      success() {
        this.cp.updatePromptsList();
        this.cp.editMode = false;
      },
      isValid() {
        return true;
      },
      send() {
        this.loading = true;
        bbn.fn.post(this.root + 'chat', {
          prompt: this.formData.prompt + '\n' + bbn.fn.getRow(this.promptType, {value: this.formData.output}).prompt + ' and the language must be in ' +  bbn.fn.getRow(this.languages, {value: this.formData.lang}).text,
          input: this.input
        }, (d) => {
          if (d.success) {
            this.response = this.formData.output === 'bbn-json-editor' ? JSON.parse(d.text) : d.text
						setTimeout(() => {
              this.loading = false;
            }, 300);
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