// Javascript Document

(() => {
  const defaultConfig = cp => {
    return {
      endpoint: cp.endpoints[0].id,
      model: cp.currentEndpoint.models[0].id,
      temperature: 0.7,
      presence: 0.9,
      frequency: 0.1,
      top_p: 0.95,
      language: bbn.env.lang,
      aiFormat: 'multilines'
    }
  };

  const newConversation = text => {
    const tst = bbn.fn.timestamp();
    return {
      id: null,
      title: "",
      num: 0,
      tags: [],
      creation: tst,
      last: tst,
      conversation: []
    };
  };

  return {
    mixins: [bbn.cp.mixins.basic, bbn.cp.mixins.localStorage],
    props: {
      endpoints: {
        type: Array,
        required: true
      },
      intros: {
        type: Array
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
        type: String,
        required: true
      },
      endpoint: {
        type: String,
        required: true
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
        itemIntro: null,
        currentIntro: '',
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

        return null;
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
      clear() {
        bbn.fn.log("CLEAR", this.mode, this.selectedPromptId);
      },
      createChat() {
        this.isLoadingResponse = true;
        this.$nextTick(() => {
          this.conversation = newConversation(this.getRandomIntroSentence());
          setTimeout(() => this.isLoadingResponse = false, 250);
        })
      },
      fdate: bbn.fn.fdate,
      updateCurrentIntro() {
        this.currentIntro = this.getRandomIntroSentence();
      },
      getRandomIntroSentence() {
        const randomIndex = Math.floor(Math.random() * this.intros.length);
        return this.intros[randomIndex].text;
      },
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
          const cfg = bbn.fn.extend({}, this.cfg);
          delete cfg.endpoint;
          cfg.model = this.currentModel.text;
          const data = {
            prompt: input,
            date: inputDate,
            userFormat: 'textarea',
            endpoint: this.cfg.endpoint,
            cfg,
            id: this.currentChat?.id || ''
          };
          this.isLoading = true;
          bbn.fn.log(["SENDING", data]);
          bbn.fn.post(this.root + 'chat', data, d => {
            if (d.success&& d.conversation) {
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
      itemIntro() {
        this.tst = bbn.fn.timestamp();
      },
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
        cfg = defaultConfig(this);
      }

      this.cfg = cfg;
    },
    mounted() {
      this.updateCurrentIntro();
      if (!this.conversation.length) {
        this.itemIntro = newConversation(this.currentIntro)[0];
      }

      setTimeout(() => {
        bbn.fn.log("TIMEOUT");
        this.updateScroll();
      }, 500)
    },
  }
})();