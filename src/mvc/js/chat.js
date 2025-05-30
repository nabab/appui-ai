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
        options: this.source.options,
        isLoading: false,
        currentEndpointId: this.source.endpoints?.length ? this.source.endpoints[0].id : null,
        currentModelId: this.source.endpoints?.length ? this.source.endpoints[0].models?.[0]?.id : null,
        selectedPromptId: null,
        currentChatId: null,
        editChat: false,
        mode: null,
        conversationChange: false,
        listChange: false,
        // prompt mode
        currentPrompt: null,
        // chat mode
        currentChat: [],
        selectedChatPath: this.listSource?.length ? this.listSource[0].value : null,
          
      }
    },
    computed: {
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
        bbn.fn.log('chatSelected', arguments);
        const chats = this.getRef('chatList');
        if (chats) {
          return chats.currentData[chat.currentSelected[0]];
        }

        return undefined;
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
      createChat() {
        this.addNewChat();
        setTimeout(() => {
          
        })
      },
      chatMenu(row) {
        const menu = [];
        if (row.data.file !== 'new') {
          menu.push({
            text: bbn._("Rename"),
            action: () => this.renameChat(row.data.file)
          }, {
            text: bbn._("Delete"),
            action: () => this.deleteChat(row)
          })
        }

        return menu;
      },
      renameChat() {
        bbn.fn.log("renameChat");
      },
      deleteChat(node, force) {
        this.confirm(bbn._("Are you sure you want to delete this chat?"), () => {
          bbn.fn.log("deleteChat", node);
          this.post(
            appui.plugins['appui-ai'] + '/chat/delete',
            {file: node.data.file},
            d => {
              if (d.error) {
                appui.error(d.error);
              }
              else if (d.success) {
                appui.success(bbn._("Chat deleted"));
                const tree = this.getRef('chatList');
                if (tree) {
                  tree.updateData();
                }
              }
            }
          )
        })
      },
      addNewChat() {
        const chats = this.getRef('chatList');
        if (chats) {
          chats.currentData.unshift({
            index: 0,
            key: 'new',
            _bbn: true,
            data: {
              title: bbn._("New chat"),
              file: 'new'
            }
          });
          chats.updateIndexes();
        }
      },
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
      createPrompt() {
        this.conversationChange = true;
        this.selectedPromptId = null;
        setTimeout(() => {
          this.conversationChange = false;
        }, 250);
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
      chatSelectItem(item) {
        if (item.data.file) {
          this.conversationChange = true;
          this.selectedChatPath = item.data.file;
          if (item.data.file === 'new') {
            this.addNewChat();
            this.currentChat = [];
          }
          else {
            bbn.fn.post(this.root + 'conversation', {
              path: this.selectedChatPath
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
        if ((this.mode === 'prompt') && this.selectedPromptId) {
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
      onPromptEditSuccess(o) {
        if (o.id) {
          bbn.fn.log('onPromptEditSuccess', arguments);
          this.updatePromptsList();
          this.selectedPromptId = o.id;
        }
      },
      updatePromptsList() {
        this.listChange = true;
        bbn.fn.post(this.source.root + '/prompts' , {list: true}, (d) => {
          if (d.success) {
            this.source.prompts.splice(0, this.source.prompts.length, ...d.data);

            setTimeout(() => {
              this.listChange = false;
              this.editPrompt = true;
            }, 300)
          }
        })
      }
    },
    mounted() {
      if (this.source.prompts.length) {
        this.currentPrompt = this.promptList[0];
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