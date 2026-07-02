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
        ui: null,
        root: appui.plugins['appui-ai'] + '/'
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
        if (this.currentEndpoint && this.currentEndpointId) {
          let model = '';
          bbn.fn.each(defaultModels, m => {
            const idModel = bbn.fn.getField(this.currentEndpoint.models, 'id', 'name', m);
            if (idModel) {
              model = idModel;
              return false;
            }
          });

          return {
            endpoint: this.currentEndpointId,
            model,
            lang: bbn.env.lang,
            settings: {},
            cfg: {
              language: null,
              /*aiFormat: 'multilines',
              temperature: '1',
              presence: '0.2',
              frequency: '0.7',
              top_p: '0.5' */
            }
          };
        }

        return null;
      },
      getEndpointByModel(idModel){
        return this.ui.getEndpointByModel(idModel);
      }
    },
    created() {
      this.ui = appui.getRegistered('appui-ai-ui');
    },
  };
  bbn.cp.addPrefix('appui-ai-', null, [mix]);

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
      const types = ['formats', 'promptBits', 'chatModes', 'intro'];
      bbn.fn.each(types, t => {
        routerSettings.push({
          url: bbn.fn.camelToCss(t),
          label: this.source[t].text,
          fixed: true,
          scrollable: false,
          component: 'appui-option-list',
          source: this.source[t]
        })
      })
      return {
        intros: this.source.intro.options,
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
      onSetFile(fileName) {
        const lst = this.getRef('chatList');
        if (lst?.selected?.length && lst?.currentData?.length) {
          const d = bbn.fn.getRow(lst.currentData, {key: lst.selected[0]});
          if (d) {
            d.data.file = fileName;
          }
        }
        else if (lst?.[0]?.data?.file === 'new') {
          lst[0].data.file = fileName;
        }
      },
      onChangeTitle(title) {
        const lst = this.getRef('chatList');
        if (lst?.selected?.length && lst?.currentData?.length) {
          const d = bbn.fn.getRow(lst.currentData, {key: lst.selected[0]});
          if (d) {
            d.data.title = title;
          }
        }
      },
      getRandomIntroSentence() {
        const randomIndex = Math.floor(Math.random() * this.intros.length);
        return this.intros[randomIndex].text;
      },
      getPromptButtons(row) {
        return [{
          icon: 'nf nf-fa-edit',
          notext: true,
          action: () => bbn.fn.link(this.root + 'chat/prompts/' + row.id)
        }, {
          icon: 'nf nf-fa-trash bbn-red',
          notext: true,
          action: () => this.deletePrompt(row.id)
        }]
      },
      chatMenu(row) {
        const menu = [];
        if (row.file !== 'new') {
          menu.push({
            icon: 'nf nf-fa-edit',
            text: bbn._("Rename"),
            action: () => this.renameChat(row)
          }, {
            icon: 'nf nf-fa-trash bbn-red',
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
            this.root + 'chat/delete',
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
        return new Promise(resolve => {
          const chats = this.getRef('chatList');
          if (chats) {
            const now = bbn.fn.timestamp();
            chats.addToData({
              title: bbn._("New chat"),
              file: 'new',
              id: bbn.fn.randomString(),
              creation: now,
              last: now,
              num: 1,
              isNew: true,
            }, true);
            this.$nextTick(() => {
              chats.select(0);
              resolve();
            });
          }
        });
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
      deletePrompt(idPrompt) {
        if (!bbn.fn.isUid(idPrompt)) {
          idPrompt = this.selectedPromptId;
        }

        this.confirm(bbn._('Do you want to delete this prompt?'), () => {
          this.post(this.root + 'prompt/delete', {
            id: idPrompt
          }, d => {
            if (d.success) {
              appui.success(bbn._('Success'));
            }
            else {
              appui.error(d.error || bbn._('Error'));
            }

            this.updatePromptsList();
          }, () => {
            appui.error(bbn._('Error'));
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
        this.post(this.root + 'prompt/conversation/get', {
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
            item.conversation = [{
              asked: now,
              responded: now,
              messages: [{
                role: 'assistant',
                content: this.getRandomIntroSentence()
              }]
            }];
            this.currentChat = item;
          }
          else {
            bbn.fn.post(this.root + 'conversation', {
              path: this.selectedChatPath
            }, d => {
              if (d.success) {
                delete d.success;
                this.currentChat = d;
                if (d.conversation?.length
                  && d.conversation.at(-1)?.cfg
                ) {
                  const chatui = this.getRef('chatui');
                  if (chatui) {
                    const cfg = bbn.fn.clone(d.conversation.at(-1).cfg);
                    if (cfg.model && bbn.fn.isUid(cfg.model)) {
                      const endpoint = this.getEndpointByModel(cfg.model);
                      if (endpoint) {
                        cfg.endpoint = endpoint;
                        chatui.cfg = cfg;
                      }
                    }
                  }
                }
              }
              else {
                appui.error(d);
              }

              this.conversationChange = false;
            }, () => {
              this.conversationChange = false;
            })
          }
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
            this.post(this.root + 'prompt/conversation/clear' , {
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

          bbn.fn.link(this.root + 'chat/prompts/' + d.data.id);
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
        this.post(this.root + 'prompts' , {list: true}, (d) => {
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
        this.post(this.root + 'actions/sync', {
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
      },
      getEndpointByModel(idModel){
        let endpoint = null;
        bbn.fn.each(this.source.endpoints, e => {
          if (e.models?.length && bbn.fn.getRow(e.models, 'id', idModel)) {
            endpoint = e.id;
            return false;
          }
        });

        return endpoint;
      },
      promptsMap(row) {
        let endpoint = row.settings?.id_model ? this.getEndpointByModel(row.settings.id_model) : '';
        if (endpoint) {
          endpoint = bbn.fn.getRow(this.source.endpoints, 'id', endpoint);
        }

        row.endpoint = endpoint?.text || '';
        row.model = row.settings?.id_model && endpoint?.models?.length ? bbn.fn.getField(endpoint.models, 'name', 'id', row.settings.id_model) : '';
        row.language = row.settings?.language || bbn.env.lang;
        return row;
      }
    },
    mounted() {
      appui.register('appui-ai-ui', this)
      if (this.source.prompts.length) {
        this.currentPrompt = this.promptList[0];
      }
    },
    beforeDestroy() {
      appui.unregister('appui-ai-ui')
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
            return bbn.fn.link(appui.plugins['appui-ai'] + '/chat/prompts/new')
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