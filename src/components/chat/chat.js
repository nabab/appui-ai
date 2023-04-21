// Javascript Document
// Javascript Document

(() => {
  return {
    props: {
      source: {
        type: Object,
        required: false,
      },
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
        root: this.source.root ?? "",
        prompt: "",
        selectedPromptId: null,
        conversation: [],
        userChat: {
          prompt: "",
          conversation: [],
          input: ""
        },
        editMode: false,
        userPromptType: "bbn-textarea",
        messagePromptType: "bbn-textarea",
        mode: null,
        introSentences: [
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
        ]
      }
    },
    computed: {

      listSource() {
        let res = [
          {text: "Current Chat", value: null}
        ];
        for (let i in this.source.prompts) {
          res.push({
            text: this.source.prompts[i].title,
            value: i
          })
        }
        return res;
      },
      currentSelected() {
        if (!this.selectedPromptId) {
          return null;
        }
        return bbn.fn.getRow(this.source.prompts, {id: this.selectedPromptId});
      }
    },
    methods: {
      getRandomIntroSentence() {
        const randomIndex = Math.floor(Math.random() * this.introSentences.length);
        return this.introSentences[randomIndex];
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
      getConversation() {
        bbn.fn.post(this.source.root + '/prompt/conversation/get', {
          id: this.selectedPromptId
        }, (d) => {
          if (d.success) {
            this.conversation.splice(0, this.conversation.length, ...d.data);
          }
        })
      },
      getTextareaSize(ref) {
        let element = this.getRef(ref);
        return element.scrollHeight + "px";
      },
      resize(ref) {
        let element = this.getRef(ref);

        element.style.height = "18px";
        element.style.height = element.scrollHeight + "px";
      },
      test() {
        bbn.fn.log(this.source.prompts);
      },
      testClick(e) {
        bbn.fn.log(this.source.prompts[e.value]);
      },
      listSelectItem(item) {
        if (!this.selectedPromptId) {
          this.userChat.prompt = this.prompt;
          this.userChat.input = this.input;
          bbn.fn.log(this.conversation);
          this.userChat.conversation = [...this.conversation];
        }

        if (item.value) {
          this.prompt = this.source.prompts[item.value].content;
          this.selectedPromptId = this.source.prompts[item.value].id;
          this.input = "";
          this.getConversation();
        } else {
          this.selectedPromptId = null;
          this.prompt = this.userChat.prompt;
          this.input = this.userChat.input;
          this.conversation = this.userChat.conversation
        }
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
            bbn.fn.post(this.source.root + '/prompt/conversation/clear' , {
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
        bbn.fn.post(this.source.root + '/prompts' , {}, (d) => {
          if (d.success) {
            this.source.prompts.splice(0, this.source.prompts.length, ...d.data);
            this.$forceUpdate();
          }
        })
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

          bbn.fn.post(this.source.root + '/AI', request_object, (d) => {
            if (d.success && this.selectedPromptId === id) {
              this.getConversation();
            }
          })
        }
        else {
          let input = this.input;
          let inputDate = new Date().toLocaleString('en-US', { timeZone: 'UTC', hour12: false });
          bbn.fn.post(this.source.root + '/AI', {prompt: this.input}, (d) => {
            if (d.success) {
              this.conversation.push({text: input, ai: 0, creation_date: inputDate});
              this.conversation.push({text: d.text, ai: 1, creation_date: new Date().toLocaleString('en-US', { timeZone: 'UTC', hour12: false })});
            }
          })
        }
      }
    }
  }
})();