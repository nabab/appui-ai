// Javascript Document

(() => {
  const newConfig = () => {
    return {
      model: null,
      temperature: 0.7,
      presence: 0.9,
      frequency: 0.1,
      top_p: 0.95,
    }
  };

  const newConversation = text => {
    return [{
      text,
      ai: 1,
      id: bbn.fn.randomString(),
      creation_date: bbn.fn.timestamp(),
      title: '',
    }];
  };

  return {
    mixins: [bbn.cp.mixins.basic, bbn.cp.mixins.localStorage],
    props: {
      endpoints: {
        type: Array,
        required: true
      },
      intro: {
        type: Array
      },
      formats: {
        type: Array,
        required: true
      },
      source: {
        type: Array,
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
        languages: [
          {text: bbn._('Italian'), value: 'it'},
          {text: bbn._('French'), value: 'fr'},
          {text: bbn._('English'), value: 'en'},
        ],
        cfg: newConfig(),
        currentEndpointId: this.endpoints?.length ? this.endpoints[0].id : null,
        currentModelId: this.endpoints?.length ? this.endpoints[0].models?.[0]?.id : null,
        currentModels: this.endpoints[0]?.models || [],
        conversationChange: false,
        input: "",
        root: appui.plugins['appui-ai'] + '/',
        prompt: this.configuration?.title || null,
        conversation: this.source?.length || (this.mode === 'prompt') ? this.source : [],
        editMode: false,
        userFormat: this.configuration?.input || "textarea",
        aiFormat: this.configuration?.output || "textarea",
        isLoadingResponse: false,
        currentChat: [],
      }
    },
    computed: {
      currentEndpoint() {
        if (this.currentEndpointId) {
          return bbn.fn.getRow(this.endpoints, {id: this.currentEndpointId})
        }

        return null;
      },
      currentModel() {
        if (this.currentEndpoint && this.currentModelId) {
          return bbn.fn.getRow(this.currentEndpoint.models, {id: this.currentModelId});
        }

        return null;
      },
      lastModelsUsed() {
        if (this.currentEndpoint) {
          return bbn.fn.order(this.currentEndpoint.models.filter(a => !!a.lastUsed), 'lastUsed', 'DESC');
        }

        return [];
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
      getRandomIntroSentence() {
        const randomIndex = Math.floor(Math.random() * this.intro.length);
        return this.intro[randomIndex].text;
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
      send() {
        bbn.fn.log("SEND", this.configuration);

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
          bbn.fn.post(this.root + 'chat', {
            prompt: input,
            date: inputDate,
            aiFormat: this.aiFormat,
            userFormat: 'textarea',
            model: this.currentModelId,
            endpoint: this.endpoint
          }, d => {
            this.conversation.at(-1).creation_date = d.date;
            if (d.success) {
              this.$set(this.conversation.at(-1), 'text', d.text);
            }
            else {
              this.$set(this.conversation.at(-1), 'error', true);
            }
            this.conversation.at(-1).loading = false;
            this.isLoadingResponse = false;
            this.$nextTick(this.updateScroll);
          });
        }
      }
    },
    watch: {
      currentModelId() {
        bbn.fn.log("CHANGIN currentModelId");
      }
    },
    mounted() {
      if (!this.conversation.length) {
        this.conversation = newConversation(this.getRandomIntroSentence());
      }

      setTimeout(() => {
        bbn.fn.log("TIMEOUT");
        this.updateScroll();
      }, 500)
    },
  }
})();