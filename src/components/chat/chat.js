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
      },
      configuration: {
        type: Object,
        required: false
      }
    },
    data() {
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
            value: "bbn-textarea",
            text: "Text Multiline",
            prompt: "Your response needs to be entered as multiple lines of text"
          },
          {
            value: "bbn-code",
            text: "Code",
            prompt: "Your response needs to be a code snippet"
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
        input: "",
        root: appui.plugins['appui-ai'] + '/',
        prompt: this.configuration?.title || null,
        conversation: this.source?.length ? this.source :  [{
          text: this.getRandomIntroSentence(),
          ai: 1,
          id: bbn.fn.randomString(),
          creation_date: (new Date()).getTime()
        }],
        userChat: {
          prompt: "",
          conversation: [],
          input: ""
        },
        editMode: false,
        userPromptType: "bbn-textarea",
        aiFormat: this.configuration?.output || "div",
        isLoadingResponse: false
      }
    },
    mounted() {
      setTimeout(() => {
        bbn.fn.log("TIMEOUT");
        this.updateScroll();

      }, 300)
    },
    computed: {
			userComponent() {
        if ((this.mode === 'prompt') && this.configuration) {
          return this.configuration.input;
        }
        return false;
      },
      userComponentOptions() {
        const o = {};
        if (this.configuration?.input) {
          switch (this.configuration.input) {
            case 'bbn-textarea':
              o.autosize = true;
              break;
            case 'bbn-code':
              o.mode = 'php';
            
              
          }
        }
        return o;
      }
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
      updateScroll() {
        if (this.getRef('scroll')) {
          this.$nextTick(() => {
            this.getRef('scroll').onResize(true).then(() => {
              this.$nextTick(() => {
                this.getRef('scroll').scrollEndY(true);
              });
            });
          });
        }
      },
      send() {
        bbn.fn.log("SEND", this.configuration);

        if (this.configuration?.id) {
          let request_object = {
            prompt: this.configuration.content + '\n' + bbn.fn.getRow(this.promptType, {value: this.configuration.output}).prompt + ' and the language must be in ' +  bbn.fn.getRow(this.languages, {value: this.configuration.lang}).text,
            input: this.input
          }
          let id = this.configuration.id;

          if (id) {
            request_object.id = id;
          }

          let input = this.input;
          let inputDate = (new Date()).getTime();

          this.conversation.push({text: input, ai: 0, creation_date: inputDate});
          this.conversation.push({ai: 1, loading: 1, creation_date: inputDate});

          this.isLoadingResponse = true;

          this.input = '';

          this.$nextTick(this.updateScroll);
          this.getRef('chatPrompt').focus();

          bbn.fn.post(this.root + 'chat', request_object, (d) => {
            if (d.success && this.configuration.id === id) {
              let inputDate = (new Date()).getTime();
              this.conversation.at(-1).creation_date = inputDate;
              if (d.success) {
                this.$set(this.conversation.at(-1), 'text', this.configuration.output === 'bbn-json-editor' ? JSON.parse(d.text) : d.text);
              }
              else {
                this.$set(this.conversation.at(-1), 'error', true);
              }
              this.conversation.at(-1).loading = false;
              this.isLoadingResponse = false;
              this.$nextTick(this.updateScroll);
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
            date: inputDate
          }, (d) => {
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
    }
  }
})();