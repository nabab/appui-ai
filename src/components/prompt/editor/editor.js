// Javascript Document

(() => {
  const getBlankData = (data, cp) => {
    const defFormat = bbn.fn.getField(cp.formats, 'id', 'code', 'textarea');
    let currentSettings = {};
    if (data?.settings) {
      currentSettings = {
        settings: data.settings,
        endpoint: cp.getEndpointByModel(data.settings.id_model),
        model: data.settings.id_model,
        cfg: data.settings.cfg || {},
      };
    }

    return bbn.fn.extend({}, cp.getDefaultSettings(), {
      id: data?.id || null,
      id_note: data?.id_note || null,
      title: data?.title || "",
      content: data?.content || "",
      output: data?.output || defFormat || "",
      input: data?.input || defFormat || "",
      shortcode: data?.shortcode || null,
    }, currentSettings);
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
        cfg: null,
        formData: null,
        testFormData: {
          input: ""
        },
        response: null,
        loading: false,
        cp: null,
        generatingTitle: false,
        isValid: false,
        ready: false,
        changingUserFormatComponent: false,
      }
    },
    computed: {
      aiFormatComponent() {
        return this.formData?.output ? bbn.fn.getField(this.formats, 'component', 'id', this.formData.output) : null;
      },
      aiComponentOptions() {
        const o = {
          readonly: true
        };
        if (this.formData?.output) {
          const outputCode = bbn.fn.getField(this.formats, 'code', 'id', this.formData.output);
          switch (outputCode) {
            case 'textarea':
              o.autosize = true;
              o.resizable = false;
              break;
            case 'code-php':
              o.autosize = true;
              o.mode = 'php';
              o.theme = 'dracula';
              o.fill = false
              break;
            case 'code-js':
              o.autosize = true;
              o.mode = 'js'
              o.theme = 'dracula';
              o.fill = false
              break;
          }
        }

        return o;
      },
      userFormatComponent() {
        return this.formData?.input ? bbn.fn.getField(this.formats, 'component', 'id', this.formData.input) : null;
      },
      userComponentOptions() {
        const o = {
          required: true
        };
        if (this.formData?.input) {
          const inputCode = bbn.fn.getField(this.formats, 'code', 'id', this.formData.input);
          switch (inputCode) {
            case 'textarea':
              o.autosize = true;
              o.resizable = false;
              o.rows = 3;
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
    methods: {
      success(d) {
        if (d.success) {
          this.$emit('success', d)
          appui.success(bbn._("Prompt saved successfully"));
        }
      },
      testPrompt() {
        this.loading = true;
        this.response = '';
        if (this.formData?.content?.length) {
          this.post(this.root + 'chat', bbn.fn.extend({}, this.testFormData, {
            content: this.formData.content,
            test: true,
            output: this.formData.output,
            model: this.formData.model,
            endpoint: this.formData.endpoint,
            cfg: this.formData.cfg,
            id_prompt: this.formData.id || null
          }), d => {
            if (d.success) {
              if (d.result?.content) {
                this.response = this.formData.output === 'bbn-json-editor' ? JSON.parse(d.result.content) : d.result.content;
              }

              this.$nextTick(() => {
                this.loading = false;
              })
            }
            else {
              appui.error(bbn._("An error occurred while processing the request: %s", d.error || bbn._("Unknown error")));
              this.loading = false;
            }
          }, () => {
            appui.error(bbn._("An error occurred while processing the request: %s", d.error || bbn._("Unknown error")));
            this.loading = false;
          })
        }
      },
      generateTitle() {
        this.generatingTitle = true;
        this.post(this.root + 'chat', {
          content: "The given text is a prompt for which you need to provide (only) a short clear and descriptive title for this prompt.",
          input: this.formData.content,
          test: true,
          model: this.formData.model,
          endpoint: this.formData.endpoint,
          cfg: this.formData.cfg
        }, d => {
          this.isLoading = false;
          if (d.success && d.result?.content) {
            this.formData.title = d.result.content.trim().replace(/^\"(.+)\"$/,"$1");
          }

          this.generatingTitle = false;
        }, () => {
          this.isLoading = false;
        })
      }
    },
    mounted() {
      this.cp = this.closest('bbn-container').getComponent();
      this.formData = getBlankData(this.source?.id ? this.source : {}, this);
      bbn.fn.log('formData', this.formData);
      this.ready = true;
    },
    watch: {
      userFormatComponent(newVal, oldVal) {
        this.changingUserFormatComponent = true;
        this.$nextTick(() => {
          this.changingUserFormatComponent = false;
        })
      }
    }
  }
})();