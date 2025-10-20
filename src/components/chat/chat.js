// Javascript Document

(() => {
  return {
    mixins: [bbn.cp.mixins.basic, bbn.cp.mixins.localStorage],
    props: {
      endpoints: {
        type: Array,
        required: true
      },
      formats: {
        type: Array,
        required: true
      },
      languages: {
        type: Array
      },
      source: {
        type: Object,
        required: true,
      },
      mode: {
        type: String,
        required: true
      },
      configuration: {
        type: Object,
        required: false
      },
      model: {
        type: String
      },
      endpoint: {
        type: String
      },
      storage: {
        type: Boolean,
        default: true
      },
      storageName: {
        type: String,
        default: 'appui-ai-chat'
      }
    },
    data() {
      return {
        cfg: null,
        currentEndpointId: this.endpoints?.length ? this.endpoints[0].id : null,
        currentModelId: this.endpoints?.length ? this.endpoints[0].models?.[0]?.id : null,
        currentModels: this.endpoints[0]?.models || [],
        conversationChange: false,
        input: "",
        root: appui.plugins['appui-ai'] + '/',
        prompt: this.configuration?.title || null,
        conversation: this.source.conversation,
        editMode: false,
        userFormat: this.configuration?.input || "textarea",
        aiFormat: this.configuration?.output || "textarea",
        isLoadingResponse: false,
        currentChat: this.source || null,
        isLoading: false,
        isSelecting: false,
        selected: [],
        tst: bbn.fn.timestamp()
      }
    },
    computed: {
      itemCurrent() {
        return {
          text: this.input,
          ai: 0
        }
      },
      currentEndpoint() {
        
        if (this.cfg?.endpoint) {
          return bbn.fn.getRow(this.endpoints, {id: this.cfg.endpoint})
        }

        return this.endpoints[0] || null;
      },
      currentModel() {
        if (this.currentEndpoint && this.cfg.model) {
          return bbn.fn.getRow(this.currentEndpoint.models, {id: this.cfg.model});
        }

        return null;
      },
      aiFormatComponent() {
        return bbn.fn.getRow(this.formats, {value: this.aiFormat}).component;
      },
      userFormatComponent() {
        return bbn.fn.getRow(this.formats, {value: this.userFormat}).component;
      },
      userComponentOptions() {
        const o = {};
        if (this.configuration?.input) {
          switch (this.configuration.input) {
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
    methods: {
      deleteChat(item) {
        bbn.fn.log("deleteChat", item);
        this.post(appui.plugins['appui-ai'] + '/chat/actions/delete', item, d => {
          bbn.fn.log("SUCCESS DELETE", d);
        })
      },
      checkSend(e) {
        bbn.fn.log(e);
        if (e.ctrlKey && (e.key === 'Enter')) {
          e.preventDefault();
          e.stopImmediatePropagation();
          this.send();
        }
        if (e.metaKey && (e.key === 'Enter')) {
          e.preventDefault();
          e.stopImmediatePropagation();
          this.send();
        }
      },
      clear() {
        bbn.fn.log("CLEAR", this.mode, this.selectedPromptId);
      },
      fdate: bbn.fn.fdate,
      updateScroll() {
        const scroll = this.getRef('scroll');
        if (scroll?.onResize) {
          scroll.onResize(true).then(() => {
            this.$nextTick(() => {
              scroll.scrollEndY(true);
            });
          });
        }
      },
      resend() {
        if (this.conversation.length > 1) {
          this.input = this.conversation.at(-2).text;
          this.$nextTick(this.send);
        }
      },
      send() {
        bbn.fn.log("SEND", this.configuration, this.cfg);
        if (!this.input) {
          return;
        }

        if (this.configuration?.id) {
          let request_object = {
            id_prompt: this.configuration.id,
            input: this.input,
            model: this.model,
            endpoint: this.endpoint
          }

          let input = this.input;
          let inputDate = (new Date()).getTime();

          this.conversation.push({text: input, ai: 0, creation_date: inputDate});
          const lastDialog = {ai: 1, loading: 1, creation_date: inputDate, text: ''};
          this.conversation.push();

          this.isLoadingResponse = true;

          this.input = '';

          this.$nextTick(this.updateScroll);
          this.getRef('chatPrompt').focus();

          this.isLoading = true;
          bbn.fn.post(this.root + 'chat', request_object, (d) => {
            if (d.success) {
              let inputDate = (new Date()).getTime();
              lastDialog.creation_date = inputDate;
              //lastDialog.text = this.configuration.output === 'bbn-json-editor' ? JSON.parse(d.text) : d.text);
              lastDialog.text = d.text;
              lastDialog.loading = false;
              this.isLoadingResponse = false;
              this.$nextTick(this.updateScroll);
            }
            else {
              this.$set(this.conversation.at(-1), 'error', true);
            }
            this.isLoading = false;
          })
        }
        else {
          let input = this.input;
          let inputDate = (new Date()).getTime();
          this.conversation.push({text: input, ai: 0, creation_date: inputDate, id: bbn.fn.randomString()});
          this.conversation.push({ai: 1, loading: 1, creation_date: inputDate, id: bbn.fn.randomString()});
          this.isLoadingResponse = true;
          this.input = '';
          this.$nextTick(this.updateScroll);
          this.getRef('chatPrompt').focus();
          const data = bbn.fn.extend({
            prompt: input,
            date: inputDate,
            userFormat: 'textarea',
            endpoint: this.cfg.endpoint,
            id: this.currentChat?.id || ''
          }, this.cfg);
          this.isLoading = true;
          bbn.fn.post(this.root + 'chat', data, d => {
            if (d.success && d.conversation) {
              if (d.file) {
                this.$emit('setfile', d.file);
              }
              const conv = d.conversation;
              if (this.currentChat?.id !== conv.id) {
                this.currentChat = conv;
              }
              else {
                this.currentChat.last = conv.last;
                this.currentChat.num = conv.num;
                if (conv.title !== this.currentChat.title) {
                  this.currentChat.title = conv.title;
                }

                this.currentChat.conversation.push(conv);
              }
            }
            else {
              this.$set(this.conversation.at(-1), 'error', true);
            }
            this.conversation.at(-1).loading = false;
            this.isLoadingResponse = false;
            this.$nextTick(this.updateScroll);
            this.isLoading = false;
          });
        }
      }
    },
    watch: {
      currentModelId() {
        bbn.fn.log("CHANGIN currentModelId");
      },
      isSelecting() {
        this.selected.splice(0);
      },
      cfg() {
        if (this.$isMounted) {
          this.setStorage(this.cfg);
        }
      },
      source(v) {
        if (v) {
          this.currentChat = v;
        }
      }
    },
    created() {
      let cfg = this.getStorage();
      if (!cfg) {
        cfg = this.getDefaultSettings();
        this.setStorage(cfg);
      }

      this.cfg = cfg;
    },
    mounted() {
    },
  }
})();