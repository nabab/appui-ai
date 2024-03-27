<!-- HTML Document -->

<div class="bbn-overlay appui-ai-chat-page">
  <bbn-router :url-navigation="false"
              :autoload="false"
              :nav="true">
    <bbn-container url="home"
                   :notext="true"
                   icon="nf nf-fa-home"
                   :fixed="true"
                   :title="_('Home')"
                   :scrollable="true">
      <div class="bbn-padding">
        <h2 class="bbn-bottom-lmargin">
          <?= _("Here you can simply chat with an AI and keep a daily record of all your discussions") ?></h2>
        <h3>
          <?= _("You can create semi automated prompts, where the first part of the prompt and the return format are predefined in a way so that you just need to enter a single parameter to get a sharp response.") ?>
        </h3>
        <h3>
          <?= _("Try our examples in the left menu to get an idea of its capabilities") ?>
        </h3>
      </div>
    </bbn-container>

    <bbn-container url="dialogs"
                   :fixed="true"
                   :title="_('Chats')"
                   :scrollable="false">
      <bbn-splitter orientation="horizontal"
                    :resizable="true"
                    :collapsible="true">
        <bbn-pane :size="250"
                  :scrollable="false">
          <div class="bbn-flex-height">
            <bbn-toolbar v-if="source.years && source.years.length > 1">
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
            <bbn-toolbar>
              <bbn-button icon="nf nf-md-forum_plus"
                          :text="_('New chat')"
                          @click="createChat"
                          class="bbn-left-xsmargin"/>
              <bbn-button icon="nf nf-md-eraser"
                          slot="right"
                          :disabled="!mode"
                          :text="_('Clear the conversation')"
                          @click="clear"/>
              <bbn-button icon="nf nf-md-delete"
                          slot="right"
                          :disabled="!mode"
                          :text="_('Delete the conversation')"
                          @click="deleteChat"/>

            </bbn-toolbar>
            <div class="bbn-flex-fill">
              <bbn-loader v-if="conversationChange"/>
              <appui-ai-chat v-else
                            :source="currentChat"
                            mode="chat"
                            :configuration="chatSelected"/>
            </div>
          </div>
        </bbn-pane>
      </bbn-splitter>
    </bbn-container>
    <bbn-container url="prompts"
                   :fixed="true"
                   :title="_('Prompts')"
                   :scrollable="false">
      <bbn-splitter orientation="horizontal"
                    :resizable="true"
                    :collapsible="true">
        <bbn-pane :size="250"
                  :scrollable="false">
          <div class="bbn-flex-height">
            <bbn-toolbar v-if="source.years && source.years.length > 1">
              <bbn-dropdown :source="conversationYearsSource"
                            v-model="selectedYear"/>
            </bbn-toolbar>
            <bbn-loader v-if="listChange"/>
            <div v-else-if="promptList.length"
                 class="bbn-flex-fill">
              <bbn-scroll>
                <div class="bbn-padding">
                  <bbn-list :source="promptList"
                            class="appui-ai-chat-list-items"
                            ref="promptList"
                            :alternateBackground="true"
                            @select="promptSelectItem"
                            :selected="selectedListItem"/>
                </div>
              </bbn-scroll>

            </div>
            <h3 v-else class="bbn-padding">
              <?= _("You will see the list of your conversations here") ?>
            </h3>
          </div>
        </bbn-pane>
        <bbn-pane :scrollable="false">
          <div class="bbn-overlay bbn-flex-height">
            <bbn-toolbar>
              <bbn-button :disabled="!selectedPromptId"
                          icon="nf nf-md-forum_plus"
                          :text="_('New prompt')"
                          @click="createPrompt"
                          class="bbn-left-xsmargin"/>
              <bbn-button :disabled="editPrompt"
                          icon="nf nf-md-comment_edit"
                          :text="_('Edit prompt')"
                          @click="editPrompt = true"/>
              <bbn-button :disabled="!editPrompt"
                          icon="nf nf-cod-chrome_close"
                          :text="_('Exit')"
                          @click="editPrompt = false"/>
              <bbn-button :disabled="editPrompt"
                          icon="nf nf-md-eraser"
                          :text="_('Clear the conversation')"
                          @click="clear"/>
              <bbn-button :disabled="editPrompt"
                          icon="nf nf-md-delete"
                          :text="_('Delete the prompt')"
                          @click="deletePrompt"/>

            </bbn-toolbar>
            <div class="bbn-flex-fill">
              <bbn-loader v-if="conversationChange"/>
              <appui-ai-chat-editor v-else-if="editPrompt"
                                    :source="promptSelected"
                                    @success="onPromptEditSuccess"/>
              <appui-ai-chat v-else
                             :source="currentPrompt"
                             mode="prompt"
                             :configuration="promptSelected"/>
            </div>
          </div>
        </bbn-pane>
      </bbn-splitter>
    </bbn-container>
  </bbn-router>
</div>
