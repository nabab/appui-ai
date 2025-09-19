<!-- HTML Document -->

<div class="bbn-overlay appui-ai-chat-page">
  <bbn-router :url-navigation="true"
              :autoload="false"
              :nav="true">
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
          </h2>
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
          <bbns-widget :closable="false"
                       item-component="appui-ai-model-widget"
                       :items="lastModelsUsed"
                       label="<?= _("Last models used") ?>"
                       :buttons-right="[{
                          action: addEndpoint,
                          icon: 'nf nf-fa-plus',
                          title: '<?= _('New endpoint') ?>'
                       }]"/>
          <bbns-widget :closable="false"
                       item-component="appui-ai-prompt-widget"
                       :items="source.prompts"
                       label="<?= _("Last prompts") ?>"
                       :no-data-component="$options.components.newPrompt"
                       :buttons-right="[{
                          action: addEndpoint,
                          icon: 'nf nf-fa-plus',
                          title: '<?= _('New endpoint') ?>'
                       }]"/>
          <bbns-widget :closable="false"
                       item-component="appui-ai-chat-widget"
                       :items="lastChats"
                       label="<?= _("Last chats") ?>"
                       :no-data-component="$options.components.newChat"
                       :buttons-right="[{
                          action: addEndpoint,
                          icon: 'nf nf-fa-plus',
                          title: '<?= _('New chat') ?>'
                       }]"/>
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
                          @click="createChat"
                          class="bbn-left-xsmargin"/>
            </bbn-toolbar>
            <div class="bbn-flex-fill bbn-padding">
              <bbn-tree :source="root + 'conversations'"
                        class="appui-ai-chat-list-items"
                        :alternateBackground="true"
                        source-text="title"
                        source-value="file"
                        ref="chatList"
                        @select="chatSelectItem"
                        :menu="chatMenu"
                        :selected="selectedListItem"
                        no-data="<?= _("You will see the list of your conversations here") ?>"/>
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
                  :nav="true"
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
                       action: createPrompt
                     }]">
            <bbns-column field="title"
                         :sortable="true"
                         :filterable="true"
                         :label="_('Title')"
                         :min-width="250"/>
            <bbns-column field="text"
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
                  :nav="true"
                  :autoload="false"/>
    </bbn-container>
  </bbn-router>
</div>
