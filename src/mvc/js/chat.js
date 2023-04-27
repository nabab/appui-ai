// Javascript Document

(() => {
  return {
    data() {
      return {
        root: appui.plugins['appui-ai'] + '/',
        isLoading: false,
        editMode: false,
        mode: null,
        conversationChange: false,
        listChange: false,
        // prompt mode
        currentPrompt: [],
        selectedPromptId: null,
        // chat mode
        currentChat: [],
        selectedYear: this.source.years && this.source.years.length ? this.source.years[this.source.years.length - 1] : new Date().getFullYear(),
        conversationList: [],
        selectedChatPath: null
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
        if (this.mode === 'prompt') {
          let res = [];
          for (let i in this.source.prompts) {
            res.push({
              text: this.source.prompts[i].title,
              value: this.source.prompts[i].id
            })
          }
          return res;
        } else if (this.mode === 'chat') {
          return this.conversationList;
        }
        return [];
      },
      currentSelected() {
        if (this.mode === 'prompt') {
          if (!this.selectedPromptId) {
            if (this.editMode) {
              return {};
            }
            if (this.source?.prompts?.length) {
              this.selectedPromptId = this.source.prompts[0].id;
              this.conversationChange = true;
              this.getPromptConversation();
            }
          }
          return bbn.fn.getRow(this.source.prompts, {id: this.selectedPromptId}) || null;
        } else if (this.mode === 'chat') {
          return bbn.fn.getRow(this.conversationList, {value: this.selectedChatPath}) || null;
        }
        return null;
      }
    },
    methods: {
      deletePrompt() {
        appui.confirm(bbn._('Do you want to delete this prompt ?'), () => {
          bbn.fn.post(this.root + 'prompt/delete', {
            id: this.selectedPromptId
          }, (d) => {
            if (d.success) {
              appui.success(bbn._('Success'))
            } else {
              appui.success(bbn._('Error'))
            }
            this.updatePromptsList();
          })
        })
      },
      getConversationList() {
        if (!this.selectedYear)
          return;
        let res = [];
        bbn.fn.post(this.root + 'conversations', {
          year: this.selectedYear
        }, (d) => {
          if(d.success) {
            for (let i in d.data) {
              res.push({
                text: d.data[i].name === 'Current' ? d.data[i].name : bbn.fn.fdate(d.data[i].name),
                value: d.data[i].path,
                editable: d.data[i].editable
              })
            }
            this.conversationList = res;
            if (this.conversationList.length && this.selectedChatPath === null) {
              this.selectedChatPath = this.conversationList[0].path;
            }
          }
        })
      },
      conversationYearsSource() {
        let res = [];
        bbn.fn.log(this.source.years);
        if (this.source.years && this.source.years.length) {
          for(let i in this.source.years) {
            res.push({
              text: this.source.years[i],
              value: this.source.years[i]
            })
          }
        }
        return res;
      },
      create() {
        this.selectedPromptId = null;
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
            setTimeout(() => {
              this.conversationChange = false;
            }, 300);

          }
        })
      },
      test() {

      },
      testClick(e) {
        bbn.fn.log(this.source.prompts[e.value]);
      },
      listSelectItem(item) {
        if (this.editMode)
          return;
        this.conversationChange = true;
        if (this.mode === 'prompt') {
          if (this.editMode) {
            return;
          }
          if (item.value) {
            bbn.fn.log(item);
            this.selectedPromptId = item.value
            this.getPromptConversation();
          }
        } else {
          if (item.value) {
            this.selectedChatPath = item.value;
            bbn.fn.post(this.root + 'conversation', {
              path: item.value
            }, (d) => {
              for (let message of d.conversation) {
                message.id = message.id || bbn.fn.randomString();
              }
              this.currentChat = d.conversation;
            })
          }
          setTimeout(() => {
            this.conversationChange = false;
          }, 500);
        }
      },
      clear() {
        bbn.fn.log("CLEAR", this.mode, this.selectedPromptId);
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
    mounted() {
      this.getConversationList();
    },
    watch: {
      mode() {
        this.isLoading = true;
        setTimeout(() => {
          this.isLoading = false;
        }, 250);
      },
      selectedYear() {
        this.listChange = true;
        this.getConversationList();
        setTimeout(() => {
          this.listChange = false;
        }, 300)
      }
    }
  }
})();