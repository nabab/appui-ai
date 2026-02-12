// Javascript Document

(() => {
  const newConversation = text => {
    const tst = bbn.fn.timestamp();
    return {
      id: null,
      title: "",
      num: 0,
      tags: [],
      creation: tst,
      last: tst,
      conversation: []
    };
  };

  const defaultModels = [
    'mistralai/mistral-small-3.2',
    'bartowski/deepseek-r1-distill-llama-8b',
    'llama-3.2-3b-instruct'
  ];

  const mix = {
    data() {
      return {
        ui: null
      }
    },
    computed: {
      currentEndpoint() {
        return this.ui?.currentEndpoint
      },
      currentEndpointId() {
        return this.ui?.currentEndpointId
      }
    },
    methods: {
      getDefaultSettings() {
        if (this.currentEndpoint) {
          let model = '';
          bbn.fn.each(defaultModels, m => {
            if (this.currentEndpoint.models.includes(m)) {
              model = m;
              return false;
            }
          });

          return {
            endpoint: this.currentEndpointId, 
            model,
            cfg: {
              language: bbn.env.lang,
              aiFormat: 'multilines',
              temperature: '1',
              presence: '0.2',
              frequency: '0.7',
              top_p: '0.5'
            }
          };
        }

        return null;
      },
    },
    created() {
      this.ui = appui.getRegistered('appui-ai-lab');
    },
  };
  //bbn.cp.addPrefix('appui-ai-lab-', null, [mix]);
  bbn.cp.addUrlAsPrefix(
    'appui-ai-lab-',
    appui.plugins['appui-component'] + '/',
    [mix]
  );

  return {
    mixins: [
      bbn.cp.mixins.basic
    ], 
    props: {
      storage: {
        default: true,
        type: Boolean
      },
      storageFullName: {
        type: String,
        default() { 
          return 'appui-ai-ui-' + this.$node.uid;
        }
      }
    },
    data() {
      const routerSettings = [];
      return {
        routerSettings,
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
        currentChat: newConversation(),
        selectedChatPath: this.listSource?.length ? this.listSource[0].value : null,
      }
    },
    computed: {
      currentEndpoint() {
        if (!this.currentEndpointId) {
          return null;
        }

        return bbn.fn.getRow(this.source.endpoints, {id: this.currentEndpointId});
      },
      formats() {
        return this.source.formats.options.map(a => ({text: a.text, value: a.id}));
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
      lastConfigs() {
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
      experimentCreate() {
        this.getPopup({
          label: bbn._("New experiment"),
          component: 'appui-ai-lab-experiment-editor',
          source: {
            name: '',
            description: ''
          }
        })
      },
      onSetFile(fileName) {
        const lst = this.getRef('chatList').currentData;
        if (lst[0]?.data?.file === 'new') {
          lst[0].data.file = fileName;
        }
      },
      getRandomIntroSentence() {
        const randomIndex = Math.floor(Math.random() * this.intros.length);
        return this.intros[randomIndex].text;
      },
      getPromptButtons(row) {
        return [{
          icon: 'nf nf-md-arrow_right_bold',
          notext: true,
          action: () => bbn.fn.link(this.root + 'lab/prompts/' + row.id)
        }]
      },
      chatMenu(row) {
        const menu = [];
        if (row.file !== 'new') {
          menu.push({
            text: bbn._("Rename"),
            action: () => this.renameChat(row)
          }, {
            text: bbn._("Delete"),
            action: () => this.deleteChat(row)
          })
        }

        return menu;
      },
      renameChat(row) {
        bbn.fn.log("renameChat", row);
        this.getPopup({
          label: false,
          component: 'appui-ai-chat-renamer',
          source: row,
          scrollable: false,
          closable: true
        });
      },
      deleteChat(node, force) {
        this.confirm(bbn._("Are you sure you want to delete this chat?"), () => {
          bbn.fn.log("deleteChat", node);
          this.post(
            appui.plugins['appui-ai'] + '/chat/delete',
            {file: node.file},
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
          const now = bbn.fn.timestamp();
          chats.addToData({
            title: bbn._("New chat"),
            file: 'new',
            id: bbn.fn.randomString(),
            creation: now,
            last: now,
            num: 1
          }, true);
          setTimeout(() => chats.select(0), 250)
        }
      },
      addEndpoint() {
        this.getPopup({
          label: false,
          component: 'appui-ai-endpoint-form',
          source: {
            text: '',
            url: '',
            pass: ''
          },
          componentEvents: {
            success: d => {
              if (d.success && d.data.data) {
                const data = d.data.data;
                data.models = d.data.models;
                this.source.endpoints.push(data);
              }
              else {
                appui.error(d.error || bbn._('An error occurred'));
              }
            }
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
        if (item.file) {
          this.conversationChange = true;
          this.selectedChatPath = item.file;
          if (item.file === 'new') {
            const now = bbn.fn.timestamp();
            this.currentChat = {
              conversation: [{
                asked: now,
                responded: now,
                messages: [{
                  role: 'assistant',
                  content: this.getRandomIntroSentence()
                }]
              }],
              creation: now,
              id: bbn.fn.randomString(),
              num: 0,
              tags: [],
              title: bbn._("New chat"),
            };
          }
          else {
            bbn.fn.post(this.root + 'conversation', {
              path: this.selectedChatPath
            }, (d) => {
              if (d.success) {
                delete d.success;
                this.currentChat = d;
              }
              else {
                appui.error(d);
              }
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
      onPromptNewSuccess(d) {
        bbn.fn.log('onPromptNewSuccess', arguments);
        if (d.success && d.data) {
          if (this.getRef('promptTable')?.ready) {
            this.getRef('promptTable').updateData();
          }

          bbn.fn.link(this.root + 'lab/prompts/' + d.data.id);
        }
      },
      onPromptEditSuccess(o) {
        if (o.id) {
          bbn.fn.log('onPromptEditSuccess', arguments);
          this.updatePromptsList();
          this.selectedPromptId = o.id;
        }
      },
      onExperimentEditSuccess(o) {
        if (o.id) {
          bbn.fn.log('onExperimentEditSuccess', arguments);
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
      },
      syncModels(endpointId) {
        bbn.fn.post(appui.plugins['appui-ai'] + '/actions/sync', {
          id: endpointId
        }, d => {
          if (d.success) {
            appui.success(bbn._('Sync successful'));
            const idx = bbn.fn.search(this.source.endpoints, {id: endpointId});
            if (this.source.endpoints[idx]) {
              this.source.endpoints[idx].models = d.models;
            }
          }
          else {
            appui.error(d.error || bbn._('Error during sync'));
          }
        });
        
      }
    },
    mounted() {
      appui.register('appui-ai-lab', this)
    },
    beforeDestroy() {
      appui.unregister('appui-ai-lab')
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
          addEndpoint(...args) {
            return appui.getRegistered('appui-ai-ui').addEndpoint(...args);
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
            return bbn.fn.link(appui.plugins['appui-ai'] + '/lab/prompts/new')
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