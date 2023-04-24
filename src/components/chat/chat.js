// Javascript Document

(() => {
  const introSentences = [
    bbn._("Hi! I'm an AI language model here to chat with you. How can I be of assistance?"),
    bbn._("Welcome to my chat page! I'm an intelligent chatbot designed to answer your questions and have meaningful conversations with you."),
    bbn._("Hey there! I'm an AI assistant that's always here to listen and help. What can I do for you today?"),
    bbn._("Greetings! I'm an advanced AI model here to converse with you. What's on your mind?"),
    bbn._("Hello! I'm an AI language model trained to understand natural language and assist you with anything you need."),
    bbn._("Salutations! I'm a highly-intelligent chatbot here to chat with you and provide any information you need."),
    bbn._("Good day! I'm an AI language model designed to have engaging conversations and answer your questions. How can I help you today?"),
    bbn._("Hello there! I'm an AI chatbot that's always eager to learn and have conversations with humans. What brings you to my chat page?"),
    bbn._("Hi! I'm an intelligent language model here to have meaningful conversations with you. Ask me anything!"),
    bbn._("Welcome! I'm an AI assistant designed to make your life easier. What can I do for you today?"),
    bbn._("Hello! I'm a language model powered by artificial intelligence. Let's have a conversation!"),
    bbn._("Good to see you! I'm an intelligent chatbot here to help you with anything you need."),
    bbn._("Hey, I'm a smart AI model that's always here to listen and chat. How can I assist you today?"),
    bbn._("Hi there! I'm a sophisticated chatbot trained to converse with humans. What's on your mind?"),
    bbn._("Greetings! I'm an AI language model designed to answer your questions and have engaging conversations."),
    bbn._("Hello! I'm an intelligent chatbot that's always ready to assist you. What can I do for you today?"),
    bbn._("Hi! I'm a cutting-edge AI model capable of understanding natural language. Let's chat!"),
    bbn._("Welcome to my chat page! I'm an AI assistant here to help you out. Feel free to ask me anything!"),
    bbn._("Hey there! I'm a chatbot that's always eager to have conversations and provide useful information. What can I do for you today?"),
    bbn._("Greetings! I'm a language model powered by artificial intelligence. How can I be of assistance to you?"),
  ];

  return {
    props: {
      source: {
        type: Array,
        required: true,
      },
      mode: {
        type: String,
        required: true
      }
    },
    data() {
      return {
        promptType: [
          {value: "bbn-rte", text: bbn._('Rich text editor')},
          {value: "bbn-markdown", text: bbn._('Markdown')},
          {value: "bbn-textarea", text: bbn._('Text multiline')},
          {value: "bbn-code", text: bbn._('Code')},
          {value: "bbn-input", text: bbn._('Text inline')},
          {value: "bbn-json-editor", text: bbn._('Json')}
        ],
        input: "",
        root: appui.plugins['appui-ai'] + '/',
        prompt: "",
        conversation: this.mode === 'chat' ? [{
          text: this.getRandomIntroSentence(),
          ai: 1,
          creation_date: (new Date()).getTime()
        }] : this.source,
        userChat: {
          prompt: "",
          conversation: [],
          input: ""
        },
        editMode: false,
        userPromptType: "bbn-textarea",
        aiFormat: "bbn-textarea",
        isLoadingResponse: false
      }
    },
    computed: {

    },
    methods: {

      fdate: bbn.fn.fdate,
      getRandomIntroSentence() {
        const randomIndex = Math.floor(Math.random() * introSentences.length);
        return introSentences[randomIndex];
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
      test() {
        bbn.fn.log(this.source.prompts);
      },
      testClick(e) {
        bbn.fn.log(this.source.prompts[e.value]);
      },
      savePrompt() {
        this.getPopup({
          title: bbn._("Folder name"),
          component: "appui-ai-form-prompt-save",
          source: {
            prompt: this.editMode ? this.prompt : this.input,
            root: this.root,
            type: this.promptType,
            input: this.userPromptType,
            output: this.messagePromptType
          }
        })
      },
      clear() {
        if (!this.selectedPromptId) {
          this.conversation = [];
        } else {
          appui.confirm(bbn._('Do you want to delete all the conversation ?'), () => {
            bbn.fn.post(this.root + 'prompt/conversation/clear' , {
              id: this.selectedPromptId
            }, (d) => {
              if (d.success) {
                appui.success(bbn._('Conversation deleted'));
                this.conversation = [];
              } else {
                appui.error(bbn._('An error occurred during deletion'))
              }
            })
          })
        }
      },
      updatePromptsList() {
        bbn.fn.post(this.root + 'prompts' , {}, (d) => {
          if (d.success) {
            this.source.prompts.splice(0, this.source.prompts.length, ...d.data);
            this.$forceUpdate();
          }
        })
      },
      updateScroll() {
        this.$nextTick(() => {
          this.getRef('scroll').onResize(true).then(() => {
            this.$nextTick(() => {
              this.getRef('scroll').scrollEndY(true);
            });
          });
        });
      },
      send() {
        let request_object = {
          prompt: this.prompt,
          input: this.input
        }
        if (this.selectedPromptId) {
          let id = this.selectedPromptId;

          if (id) {
            request_object.id = id;
          }

          bbn.fn.post(this.root + 'chat', request_object, (d) => {
            if (d.success && this.selectedPromptId === id) {
              this.getConversation();
            }
          })
        }
        else {
          let input = this.input;
          let inputDate = (new Date()).getTime();
          this.conversation.push({text: input, ai: 0, creation_date: inputDate});
          this.conversation.push({ai: 1, loading: 1, creation_date: inputDate});
          this.isLoadingResponse = true;
          this.input = '';
          this.$nextTick(this.updateScroll);
          this.getRef('chatPrompt').focus();
          bbn.fn.post(this.root + 'chat', {prompt: input}, (d) => {
            let inputDate = (new Date()).getTime();
            this.conversation.at(-1).creation_date = inputDate;
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
    }
  }
})();