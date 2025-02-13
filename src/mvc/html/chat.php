<!-- HTML Document -->

<div class="bbn-overlay appui-ai-chat-page">
  <bbn-router :url-navigation="true"
              :autoload="false"
              :nav="true">
    <!-- DROPDOWNS IN TABS BAR -->
    <div class="bbn-border-bottom"
         slot="tabs">
      <div class="bbn-middle-right bbn-nowrap">
        <bbn-dropdown bbn-model="currentEndpointId"
                      :source="source.endpoints"
                      :source-url="false"
                      source-value="id"/>
        <bbn-dropdown :disabled="!currentEndpoint"
                      bbn-model="currentModelId"
                      :source="currentEndpoint ? currentEndpoint.models : []"
                      source-value="id"/>
      </div>
    </div>
    <!-- HOME -->
    <bbn-container url="home"
                   :notext="true"
                   icon="nf nf-fa-home"
                   :fixed="true"
                   :label="_('Home')"
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
                       label="<?= _("My prompts") ?>"
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
                   :scrollable="false">
      <bbn-splitter orientation="horizontal"
                    :resizable="true"
                    :collapsible="true">
        <bbn-pane :size="250"
                  :scrollable="false">
          <div class="bbn-flex-height">
            <bbn-toolbar v-if="source.years && source.years.length > 1"
                         class="bbn-spadding">
              <bbn-dropdown :source="conversationYearsSource"
                            v-model="selectedYear"/>
            </bbn-toolbar>
            <bbn-loader v-if="listChange"/>
            <div v-else-if="conversationList.length"
                 class="bbn-flex-fill">
              <bbn-scroll>
                <div class="bbn-padding">
                  <bbn-list :source="conversationList"
                            :alternateBackground="true"
                            class="appui-ai-chat-list-items"
                            ref="chatList"
                            @select="chatSelectItem"
                            :selected="selectedListItem"/>
                </div>
              </bbn-scroll>
            </div>
            <h3 v-else class="bbn-padding">
              <?= _("You will see the list of your conversations here") ?>
            </h3>
          </div>
        </bbn-pane>
        <bbn-pane :scrollable="true">
          <div class="bbn-overlay bbn-flex-height">
            <bbn-toolbar class="bbn-spadding">
              <bbn-button icon="nf nf-md-forum_plus"
                          :label="_('New chat')"
                          @click="createChat"
                          class="bbn-left-xsmargin"/>
              <div slot="right"
                   class="bbn-nowrap">
                <bbn-button icon="nf nf-md-eraser"
                            :disabled="!mode"
                            :label="_('Clear the conversation')"
                            @click="clear"/>
                <bbn-button icon="nf nf-md-delete"
                            :disabled="!mode"
                            :label="_('Delete the conversation')"
                            @click="deleteChat"/>
              </div>
            </bbn-toolbar>
            <div class="bbn-flex-fill">
              <bbn-loader v-if="conversationChange"/>
              <appui-ai-chat v-elseif="currentModelId"
                            :source="currentChat"
                            mode="chat"
                            :model="currentModelId"
                            :endpoint="currentEndpointId"
                            :configuration="chatSelected"/>
            </div>
          </div>
        </bbn-pane>
      </bbn-splitter>
    </bbn-container>

    <!-- PROMPTS -->
    <bbn-container url="prompts"
                   :fixed="true"
                   :label="_('Prompts')"
                   :scrollable="false">
      <bbn-splitter orientation="horizontal"
                    :resizable="true"
                    :collapsible="true">
        <bbn-pane :size="250"
                  :scrollable="false">
          <div class="bbn-flex-height">
            <bbn-toolbar class="bbn-spadding">
              <bbn-button icon="nf nf-md-forum_plus"
                          :label="_('New prompt')"
                          @click="createPrompt"
                          class="bbn-right-xsmargin"/>
            </bbn-toolbar>
            <bbn-loader v-if="listChange"/>
            <div v-else-if="promptList.length"
                 class="bbn-flex-fill">
              <bbn-scroll>
                <div class="bbn-padding">
                  <bbn-list :source="promptList"
                            class="appui-ai-chat-list-items"
                            ref="promptList"
                            :alternate-background="true"
                            @select="promptSelectItem"
                            :selected="selectedListItem"/>
                </div>
              </bbn-scroll>
            </div>
            <h3 v-else class="bbn-padding">
              <?= _("You will see the list of your prompts here") ?>
            </h3>
          </div>
        </bbn-pane>
        <bbn-pane :scrollable="false">
            <!--bbn-toolbar class="bbn-spadding">
              <bbn-button bbn-if="conversa"
                          icon="nf nf-md-eraser"
                          :label="_('Clear the conversation')"
                          class="bbn-right-xsmargin"
                          @click="clear"/>
              <bbn-button :disabled="editPrompt"
                          icon="nf nf-md-delete"
                          :label="_('Delete the prompt')"
                          class="bbn-right-xsmargin"
                          @click="deletePrompt"/>
            </bbn-toolbar-->
          <div class="bbn-overlay"
               bbn-if="currentModelId">
            <bbn-loader v-if="conversationChange"/>
            <appui-ai-prompt-editor bbn-else
                                    :source="promptSelected"
                                    :model="currentModelId"
                                    :endpoint="currentEndpointId"
                                    @success="onPromptEditSuccess"/>
          </div>
          <div class="bbn-overlay bbn-middle bbn-lg"
               bbn-else>
            <?= _("Select first an endpoint and a model") ?>
          </div>
        </bbn-pane>
      </bbn-splitter>
    </bbn-container>
  </bbn-router>
</div>
