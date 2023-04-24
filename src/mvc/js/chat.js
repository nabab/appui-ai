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
        conversationChange: false
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
      getPromptConversation() {
        this.conversationChange = true;
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
        if (item.value) {
          bbn.fn.log(item);
          this.selectedPromptId = this.source.prompts[item.value].id
          this.getPromptConversation();
        } else {
         	this.selectedPromptId = null;
        }
        this.find('appui-ai-chat').$nextTick(this.find('appui-ai-chat').updateScroll);
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
        if (this.mode === 'chat') {
          this.currentChat = [];
        }
        if (this.mode === 'prompt') {
          appui.confirm(bbn._('Do you want to delete all the conversation ?'), () => {
            bbn.fn.post(this.source.root + '/prompt/conversation/clear' , {
              id: this.selectedPromptId
            }, (d) => {
              if (d.success) {
                appui.success(bbn._('Conversation deleted'));
                this.currentPrompt = [];
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