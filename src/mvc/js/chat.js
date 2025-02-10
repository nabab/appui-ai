// Javascript Document

(() => {
  return {
    mixins: [
      bbn.cp.mixins.basic,
      bbn.cp.mixins.localStorage
    ],
    data() {
      return {
        root: appui.plugins['appui-ai'] + '/',
        isLoading: false,
        currentEndpointId: this.source.endpoints?.length ? this.source.endpoints[0].id : null,
        currentModelId: this.source.endpoints?.length ? this.source.endpoints[0].models[0].id : null,
        currentPromptId: null,
        currentChatId: null,

        editPrompt: false,
        editChat: false,
        mode: null,
        conversationChange: false,
        listChange: false,
        // prompt mode
        currentPrompt: null,
        selectedPromptId: null,
        // chat mode
        currentChat: [],
        selectedYear: this.source.years && this.source.years.length ? this.source.years[this.source.years.length - 1] : new Date().getFullYear(),
        conversationList: [],
        selectedChatPath: this.listSource?.length ? this.listSource[0].value : null
      }
    },
    computed: {
      currentEndpoint() {
        if (this.currentEndpointId) {
          return bbn.fn.getRow(this.source.endpoints, {id: this.currentEndpointId})
        }

        return null;
      },
      currentModel() {
        if (this.currentEndpoint && this.currentModelId) {
          return bbn.fn.getRow(this.currentEndpoint.models, {id: this.currentModelId});
        }

        return null;
      },
      currentPrompt() {
        
      },
      lastModelsUsed() {
        if (this.currentEndpoint) {
          return bbn.fn.order(this.currentEndpoint.models.filter(a => !!a.lastUsed), 'lastUsed', 'DESC');
        }

        return [];
      },
      lastChats() {
        return [];
      },
      promptList() {
        let res = [];
        for (let i in this.source.prompts) {
          res.push({
            text: this.source.prompts[i].title,
            value: this.source.prompts[i].id
          })
        }
        return res;
      },
      chatSelected() {
        return bbn.fn.getRow(this.conversationList, {value: this.selectedChatPath}) || null;
      },
      promptSelected() {
        if (this.selectedPromptId) {
          return bbn.fn.getRow(this.source.prompts || [], {id: this.selectedPromptId}) || null;
        }

        return null;
      },
      selectedListItem() {
        if (this.mode === 'prompt') {
          return [this.selectedPromptId];
        }
        else if (this.mode === 'chat') {
          if (!this.selectedChatPath && this.listSource?.length) {
          	this.listSelectItem(this.listSource[0]);
            return [this.listSource[0].value];
          }
          return [this.selectedChatPath];
        }
      }
    },
    methods: {
      addEndpoint() {
        this.getPopup({
          title: false,
          component: 'appui-ai-endpoint-form',
          source: {
            text: '',
            url: '',
            pass: ''
          }
        })
      },
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
        return;
        if (!this.selectedYear) {
          return;
        }

        let res = [];
        bbn.fn.post(this.root + 'conversations', {
          year: this.selectedYear
        }, (d) => {
          if(d.success) {
            for (let i in d.data) {
              res.push({
                date: d.data[i].name === 'Current' ? '99999999999999' : bbn.fn.fdate(d.data[i].name),
                text: d.data[i].name === 'Current' ? d.data[i].name : bbn.fn.fdate(d.data[i].name),
                value: d.data[i].path,
                editable: d.data[i].editable
              })
            }
            this.conversationList = bbn.fn.order(res, [{field: 'date', dir: 'desc'}]);
            if (this.conversationList.length && this.selectedChatPath === null) {
              this.$nextTick(() => {
                this.chatSelectItem(this.conversationList[0]);
              });
            }
          }
        })
      },
      conversationYearsSource() {
        return this.source.years ? this.source.years.map(a => {return {text: a, value: a}}) : [];
      },
      createPrompt() {
        this.conversationChange = true;
        this.promptList.push({text: 'New Prompt', value: bbn.fn.randomString()});
        this.selectedPromptId = this.promptList[this.promptList.length - 1].value;
        this.editPrompt = true;
        setTimeout(() => {
          this.conversationChange = false;
        }, 250);
      },
      getPromptConversation() {
        return;
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
      chatSelectItem(item) {
        if (item.value) {
          this.conversationChange = true;
          this.selectedChatPath = item.value;
          bbn.fn.post(this.root + 'conversation', {
            path: item.value
          }, (d) => {
            for (let message of d.conversation) {
              message.id = message.id || bbn.fn.randomString();
            }
            this.currentChat = d.conversation;
          })
          setTimeout(() => {
            this.conversationChange = false;
          }, 500);
        }

      },
      promptSelectItem(item) {
        if (item.value) {
          this.conversationChange = true;
          bbn.fn.log(item);
          this.selectedPromptId = item.value
          this.getPromptConversation();
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
      onPromptEditSuccess() {
        this.updatePromptsList();
        this.editPrompt = false;
      },
      updatePromptsList() {
        return;
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
      selectedYear() {
        this.listChange = true;
        this.getConversationList();
        setTimeout(() => {
          this.listChange = false;
        }, 300)
      }
    },
    components: {
      newEndpoint: {
        template: `
<div class="bbn-c bbn-w-100 bbn-padding">
  <bbn-button @click="addEndpoint"
              :label="_('Add a new API endpoint')"
              icon="nf nf-fa-plus"/>
</div>
        `,
        methods: {
          addEndpoint() {
            bbn.fn.log("CREATE")
          }
        }
      },
      newPrompt: {
        template: `
<div class="bbn-c bbn-w-100 bbn-padding">
  <bbn-button @click="createPrompt"
              :label="_('Create a new prompt')"
              icon="nf nf-fa-plus"/>
</div>
        `,
        methods: {
          createPrompt() {
            bbn.fn.log("CREATE")
          }
        }
      },
      newChat: {
        template: `
<div class="bbn-c bbn-w-100 bbn-padding">
  <bbn-button @click="haveChat"
              :label="_('Have a new chat')"
              icon="nf nf-fa-plus"/>
</div>
        `,
        methods: {
          haveChat() {
            bbn.fn.log("CREATE")
          }
        }
      }
    }
  }
})();