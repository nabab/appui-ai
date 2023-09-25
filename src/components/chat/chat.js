// Javascript Document

(() => {

  return {
    statics() {
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
      return {introSentences};
    },
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
        input: "",
        root: appui.plugins['appui-ai'] + '/',
        prompt: this.configuration?.title || null,
        conversation: (this.source?.length || this.mode === 'prompt') ? this.source :  [{
          text: this.getRandomIntroSentence(),
          ai: 1,
          id: bbn.fn.randomString(),
          creation_date: (new Date()).getTime()
        }],
        editMode: false,
        userFormat: this.configuration?.input || "textarea",
        aiFormat: this.configuration?.output || "textarea",
        isLoadingResponse: false
      }
    },
    mounted() {
      setTimeout(() => {
        bbn.fn.log("TIMEOUT");
        this.updateScroll();

      }, 500)
    },
    computed: {
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

      fdate: bbn.fn.fdate,
      getRandomIntroSentence() {
        const randomIndex = Math.floor(Math.random() * this.constructor.introSentences.length);
        return this.constructor.introSentences[randomIndex];
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
            id_prompt: this.configuration.id,
            input: this.input
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
            if (d.success && this.configuration.id === request_object.id_prompt) {
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
            date: inputDate,
            aiFormat: this.aiFormat,
            userFormat: 'textarea'
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