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
        mode: null,
        conversationChange: false,
        listChange: false,
        generating: false,
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
          return {text: "Current Chat", value: null};
        }
        return bbn.fn.getRow(this.source.prompts, {id: this.selectedPromptId});
      }
    },
    methods: {
      generate() {
        const editor = this.find('appui-ai-chat-editor');
        if (editor) {
          this.generating = true;
          editor.generateTitle();
        }
      },
      create() {
        this.editMode = true;
      },
      edit() {
        this.editMode = true;
      },
      getPromptConversation() {
        bbn.fn.post(this.source.root + '/prompt/conversation/get', {
          id: this.selectedPromptId
        }, (d) => {
          if (d.success) {
            this.currentPrompt = d.data;
            this.conversationChange = false;
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
        this.conversationChange = true;
        if (this.editMode) {
          this.editMode = false;
        }
        if (item.value) {
          bbn.fn.log(item);
          this.selectedPromptId = this.source.prompts[item.value].id
          this.getPromptConversation();
        } else {
          this.selectedPromptId = null;
          this.currentPrompt = [];
          setTimeout(() => {
            this.conversationChange = false;
          }, 300);
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
        bbn.fn.log("CLEAR", this.mode, this.selectedPromptId);
        if (this.mode === 'chat') {
          this.currentChat = [];
        }
        if (this.mode === 'prompt' && this.selectedPromptId) {
          appui.confirm(bbn._('Do you want to delete all the conversation ?'), () => {
            bbn.fn.post(this.source.root + '/prompt/conversation/clear' , {
              id: this.selectedPromptId
            }, (d) => {
              if (d.success) {
                appui.success(bbn._('Conversation deleted'));
                this.currentPrompt = [];
                this.conversationChange = true;
                setTimeout(() => {
                  this.conversationChange = false;
                }, 200)
              } else {
                appui.error(bbn._('An error occurred during deletion'))
              }
            })
          })
        } else {
          this.currentPrompt = [];
          this.conversationChange = true;
          setTimeout(() => {
            this.conversationChange = false;
          }, 200)
        }
      },
      updatePromptsList() {
        this.listChange = true;
        bbn.fn.post(this.source.root + '/prompts' , {}, (d) => {
          if (d.success) {
            this.source.prompts.splice(0, this.source.prompts.length, ...d.data);
             
            setTimeout(() => {
              this.listChange = false;
            }, 300)
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