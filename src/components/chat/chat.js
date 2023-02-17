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
        input: "",
        root: this.source.root ?? "",
        prompt: "",
        selectedPromptId: "",
        conversation: [],
        userChat: {
          prompt: "",
          conversation: [],
          input: ""
        }
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
      getConversation() {
        bbn.fn.post(this.source.root + '/prompt/get_conversation', {
          id: this.selectedPromptId
        }, (d) => {
          if (d.success) {
            this.conversation.splice(0, this.conversation.length, ...d.data);
          }
        })
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
          this.selectedPromptId = "";
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
            prompt: this.prompt,
            root: this.root,
          }
        })
      },
      clear() {
        if (!this.selectedPromptId) {
          this.conversation = [];
        }
      },
      updatePromptsList() {
        bbn.fn.post(this.source.root + '/prompt/get-all' , {}, (d) => {
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
          bbn.fn.post(this.source.root + '/AI', request_object, (d) => {
            if (d.success) {
              if (!this.selectedPromptId) {
                this.conversation.push({text: input, ai: 0, creation_date: inputDate});
                this.conversation.push({text: d.text, ai: 1, creation_date: new Date().toLocaleString('en-US', { timeZone: 'UTC', hour12: false })});
              }
            }
          })
        }
      }
    }
  }
})();