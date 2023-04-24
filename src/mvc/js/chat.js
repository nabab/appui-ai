// Javascript Document

(() => {
  return {
    data() {
      return {
        root: appui.plugins['appui-ai'] + '/',
        isLoading: false,
        currentChat: [],
        currentPrompt: [],
        selectedPromptId: null,
        userChat: {
          prompt: "",
          conversation: [],
          input: ""
        },
        editMode: false,
        mode: null
      }
    },
    computed: {
      currentConversation() {
        if (this.mode === 'chat') {
          return this.currentChat;
        }
        if (this.mode === 'prompt') {
          return this.currentPrompt;
        }
        return [];
      },
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
      }
    },
    watch: {
      mode() {
        this.isLoading = true;
        setTimeout(() => {
          this.isLoading = false;
        }, 250);
      }
    }
  }
})();