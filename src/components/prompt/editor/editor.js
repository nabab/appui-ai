// Javascript Document

(() => {
  const getBlankData = (data) => {
    return {
      id: data?.id || null,
      id_note: data?.id_note || null,
      title: data?.title || "",
      content: data?.content || "",
      output: data?.output || "textarea",
      input: data?.input || "textarea",
      lang: data?.lang || null,
      shortcode: data?.shortcode || null,
      temperature: data?.temperature || 0.7,
      presence: data?.presence || 0.1,
      frequency: data?.frequency || 0.1,
      top_p: data?.top_p || 0.95,
    }
  };

  return {
    props: {
      source: {
        type: Object,
        required: false
      },
      model: {
        type: String
      },
      endpoint: {
        type: String
      },
      endpoints: {
        type: Array,
        default() {
          const ui = appui.getRegistered('appui-ai-ui');
          if (ui?.source?.endpoints) {
            return ui.source.endpoints;
          }

          return [];
        }
      },
      formats: {
        type: Array,
        default() {
          const ui = appui.getRegistered('appui-ai-ui');
          if (ui?.source?.formats?.options) {
            return ui.source.formats.options;
          }

          return [];
        }
      },
      languages: {
        type: Array,
        default() {
          const ui = appui.getRegistered('appui-ai-ui');
          if (ui?.source?.languages) {
            return ui.source.languages;
          }

          return [];
        }
      },
    },
    data() {
      return {
        root: appui.plugins['appui-ai'] + '/',
        formData: this.source?.id ? this.source : getBlankData(),
        input: "",
        response: null,
        loading: false,
        cp: null,
        generating: false,
        isValid: false,
        ready: false,
      }
    },
    computed: {
      aiFormatComponent() {
        let res = bbn.fn.getRow(this.formats, {id: this.formData.output}).component;
        if (res === 'bbn-textarea') {
          return "div";
        }
        return res;
      },
      userFormatComponent() {
        return bbn.fn.getRow(this.formats, {id: this.formData.input}).component;
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
              o.theme = 'dracula';
              o.autosize = true;
              break;
            case 'code-js':
              o.mode = 'js'
              o.theme = 'dracula';
              o.fill = false
              break;
          }
        }
        return o;
      }
    },
    mounted() {
      this.cp = this.closest('bbn-container').getComponent();
      this.ready = true;
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
      success(d) {
        if (d.success) {
          this.$emit('success', d)
        }
      },
      send() {
        this.loading = true;
        this.response = '';
        bbn.fn.post(this.root + 'chat', {
          content: this.formData.content + '\n' + bbn.fn.getRow(this.formats, {value: this.formData.output}).prompt + ' and the language must be in ' +  bbn.fn.getRow(this.languages, {value: this.formData.lang}).text,
          input: this.input,
          test: true,
          model: this.model,
          endpoint: this.endpoint,
          cfg: {
            temperature: this.formData.temperature,
            top_p: this.formData.top_p,
            frequency_penalty: this.formData.frequency_penalty,
            presence_penalty: this.formData.presence_penalty,
          }
        }, (d) => {
          if (d.success) {
            this.response = this.formData.output === 'bbn-json-editor' ? JSON.parse(d.text) : d.text
            setTimeout(() =>  {
              this.loading = false;
            }, 300);
          } else {
            setTimeout(() =>  {
              this.loading = false;
            }, 300);
          }
        })
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
          content: "The given text is a prompt for which you need to provide (only) a short clear and descriptive title for this prompt.",
          input: this.formData.content,
          test: true,
          model: this.model,
          endpoint: this.endpoint
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
    },
  }
})();