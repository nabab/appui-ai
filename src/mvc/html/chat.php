<!-- HTML Document -->

<div class="bbn-overlay appui-ai-chat-page">
  <bbn-debugger/>
  <bbn-router :url-navigation="true"
              :autoload="false"
              mode="tabs">
    <!-- DROPDOWNS IN TABS BAR -->
    <div class="bbn-block bbn-nowrap bbn-border-bottom"
         slot="tabs">
    </div>
    <!-- HOME -->
    <bbn-container url="home"
                   :notext="true"
                   icon="nf nf-fa-home"
                   :fixed="true"
                   :label="_('Home')"
                   :bcolor="$origin.bcolor"
                   :fcolor="$origin.fcolor"
                   :scrollable="true">
      <div class="bbn-w-100 bbn-padding">
        <div class="bbn-section">
          <h2 class="bbn-top-nomargin bbn-c">
            <?= _("Welcome to the appui-ai plugin") ?>
          </h2>
          <h3 class="bbn-top-nomargin bbn-c">
            <?= _("From this page you can") ?>:
          </h3>
          <h4 class="bbn-top-nomargin bbn-flex-vcentered">
            <ul class="bbn-list">
              <li><?= _("Set up any AI service, including local LLM") ?></li>
              <li><?= _("Keep a record of all your conversations with AI") ?></li>
              <li><?= _("Prepare and test your AI prompts") ?></li>
              <li><?= _("Create documents directly out of answers") ?></li>
              <li><?= _("Automatize processes with your saved prompts") ?></li>
            </ul>
          </h4>
        </div>
      </div>
      <div class="bbn-w-100">
        <bbn-dashboard :scrollable="false">
          <bbns-widget :closable="false"
                       item-component="appui-ai-endpoint-widget"
                       :items="source.endpoints"
                       label="<?= _("API endpoints") ?>"
                       :no-data-component="$options.components.newEndpoint"
                       :buttons-right="[{
                          action: addEndpoint,
                          icon: 'nf nf-fa-plus',
                          title: '<?= _('New endpoint') ?>'
                       }]"/>
          <bbns-widget bbn-if="lastModelsUsed?.length"
                       :closable="false"
                       item-component="appui-ai-model-widget"
                       :items="lastModelsUsed"
                       label="<?= _("Models used") ?>"
                       nodata="<?= _("You haven't used AI yet") ?>"/>
          <bbns-widget :closable="false"
                       item-component="appui-ai-prompt-widget"
                       :items="source.prompts"
                       label="<?= _("Prompts") ?>"
                       :no-data-component="$options.components.newPrompt"
                       :buttons-right="[{
                          action: () => bbn.fn.link(root + 'chat/prompts/new'),
                          icon: 'nf nf-fa-plus',
                          title: '<?= _('New prompt') ?>'
                       }]"/>
          <bbns-widget :closable="false"
                       item-component="appui-ai-chat-widget"
                       :items="lastChats"
                       label="<?= _("Chats") ?>"
                       :no-data-component="$options.components.newChat"
                       :buttons-right="[{
                          action: addNewChat,
                          icon: 'nf nf-fa-plus',
                          title: '<?= _('New chat') ?>'
                       }]"/>
          <bbns-widget :closable="false"
                       item-component="appui-ai-chat-widget"
                       bbn-if="lastConfigs.length"
                       :items="lastConfigs"
                       label="<?= _("Settings") ?>"/>
        </bbn-dashboard>
      </div>
    </bbn-container>
    <!-- CHAT -->
    <bbn-container url="dialogs"
                   :fixed="true"
                   :label="_('Chats')"
                   :bcolor="$origin.bcolor"
                   :fcolor="$origin.fcolor"
                   :scrollable="false">
      <bbn-splitter orientation="horizontal"
                    :resizable="true"
                    :collapsible="true">
        <bbn-pane :size="250">
          <div class="bbn-flex-height">
            <bbn-toolbar class="bbn-spadding">
              <bbn-button icon="nf nf-md-forum_plus"
                          :label="_('New chat')"
                          @click="addNewChat"
                          class="bbn-left-xsmargin"/>
            </bbn-toolbar>
            <div class="bbn-flex-fill bbn-padding">
              <bbn-list :source="root + 'conversations'"
                        class="appui-ai-chat-list-items"
                        :alternate-background="true"
                        source-text="title"
                        source-value="id"
                        uid="id"
                        ref="chatList"
                        @select="chatSelectItem"
                        :menu="chatMenu"
                        :selected="selectedListItem"
                        no-data="<?= _("You will see the list of your conversations here") ?>">
                <template bbn-pre>
                  <bbn-context :source="appui.getRegistered('appui-ai-ui').chatMenu(source)"
                               tag="div"
                               :context="true"
                               class="bbn-w-100">
                    <span bbn-text="source.title"/>
                    <span bbn-text="bbn.fn.fdate(source.last, true)"
                          class="bbn-light bbn-i bbn-s"/>
                  </bbn-context>
                </template>
              </bbn-list>
            </div>
          </div>
        </bbn-pane>
        <bbn-pane :scrollable="true">
          <appui-ai-chat :source="currentChat"
                         mode="chat"
                         ref="chatui"
                         :model="currentModelId"
                         :endpoint="currentEndpointId"
                         :endpoints="source.endpoints"
                         :formats="source.formats.options"
                         :intros="source.intro.options"
                         :languages="source.languages"
                         :configuration="chatSelected"
                         :storage="true"
                         @setfile="onSetFile"
                         storage-full-name="appui-ai-chat-model"/>
        </bbn-pane>
      </bbn-splitter>
    </bbn-container>

    <!-- PROMPTS -->
    <bbn-container url="prompts"
                   :fixed="true"
                   :label="_('Prompts')"
                   :bcolor="$origin.bcolor"
                   :fcolor="$origin.fcolor"
                   :scrollable="false">
      <bbn-router :autoload="true"
                  mode="tabs"
                  ref="promptRouter"
                  :storage="true"
                  storage-full-name="appui-ai-prompt-router">
        <bbn-container url="list"
                       :notext="true"
                       icon="nf nf-fa-list"
                       :fixed="true"
                       :label="_('List')"
                       :bcolor="$origin.bcolor"
                       :fcolor="$origin.fcolor"
                       :scrollable="false">
          <bbn-table :source="source.prompts"
                     ref="promptTable"
                     :toolbar="[{
                       icon:'nf nf-md-forum_plus',
                       label: _('New prompt'),
                       url: root + 'chat/prompts/new'
                     }]">
            <bbns-column field="title"
                         :sortable="true"
                         :filterable="true"
                         :label="_('Title')"
                         :min-width="250"/>
            <bbns-column field="content"
                         :sortable="true"
                         :filterable="true"
                         :label="_('Text')"/>
            <bbns-column field="input"
                         :sortable="true"
                         :filterable="true"
                         :source="formats"
                         :label="_('Input format')"
                         :width="120"/>
            <bbns-column field="output"
                         :sortable="true"
                         :source="formats"
                         :filterable="true"
                         :label="_('Output format')"
                         :width="120"/>
            <bbns-column :sortable="false"
                         :buttons="getPromptButtons"
                         :label="_('Actions')"
                         :width="120"/>
          </bbn-table>
        </bbn-container>
        <bbn-container url="new"
                       :notext="true"
                       icon="nf nf-fa-edit"
                       :fixed="true"
                       :label="_('New prompt')"
                       :bcolor="$origin.bcolor"
                       :fcolor="$origin.fcolor"
                       :scrollable="false">
          <appui-ai-prompt :source="promptSelected"
                           @success="onPromptNewSuccess"/>
        </bbn-container>
      </bbn-router>
    </bbn-container>

    <!-- SETTINGS -->
    <bbn-container url="settings"
                   :notext="true"
                   icon="nf nf-fa-cogs"
                   :fixed="true"
                   :label="_('Settings')"
                   :bcolor="$origin.bcolor"
                   :fcolor="$origin.fcolor"
                   :scrollable="false">
      <bbn-router class="bbn-overlay"
                  :source="routerSettings"
                  mode="tabs"
                  :autoload="false"/>
    </bbn-container>
  </bbn-router>
</div>
