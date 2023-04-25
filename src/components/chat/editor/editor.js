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
          {value: "bbn-rte", text: bbn._('Rich text editor')},
          {value: "bbn-markdown", text: bbn._('Markdown')},
          {value: "div", text: bbn._('Text multiline')},
          {value: "bbn-code", text: bbn._('Code')},
          {value: "bbn-input", text: bbn._('Text inline')},
          {value: "bbn-json-editor", text: bbn._('Json')},
        ],
        languages: [
          {value: "fr", text: bbn._('French')},
          {value: "it", text: bbn._('Italian')},
          {value: "en", text: bbn._('English')}
        ],
        root: appui.plugins['appui-ai'] + '/',
        formData: {
          id: this.source.id ?? null,
          id_note: this.source.id_note ?? null,
          title: this.source.title ?? "",
          prompt: this.source.content ?? "",
          output: this.source.output ?? "div",
          input: this.source.input ?? "div",
          lang: this.source.language ?? "en"
        },
        input: "",
        response: null,
        loading: false,
        cp: null
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
          prompt: this.formData.prompt,
          input: this.input
        }, (d) => {
          if (d.success) {
            this.response = d.text

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
        bbn.fn.post(this.root + 'chat', {
          prompt: "Please generate a title for the following prompt:",
          input: this.formData.title
        }, (d) => {
          this.isLoading = false;
          if (d.success) {
            this.formData.title = d.text;
          }
          setTimeout(() => {
            this.cp.generating = false;
          }, 300);
        })
      }
    }
  }
})();