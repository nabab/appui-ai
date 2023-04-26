<!-- HTML Document -->

<div class="bbn-overlay bbn-flex-height">
  <bbn-toolbar class="bbn-m bbn-xspadding">
    <bbn-button icon="nf nf-mdi-home"
                :text="_('Return to home page')"
                :disabled="!mode || editMode"
                :notext="true"
                @click="mode = null"/>
    <!--bbn-button text="test"
                @click="test"/-->
    <!--bbn-button icon="nf nf-fa-arrow_right"
                class="bbn-left-xsspace"
                :text="_('Send to prompt editor')"
                :notext="true"
                @click="mode = 'promptEditor'"/-->
    <bbn-button icon="nf nf-md-chat_question_outline"
                @click="mode = 'chat'"
                :text="_('Chat')"
                :disabled="mode === 'chat' || editMode"/>
    <bbn-button @click="mode = 'prompt'"
                icon="nf nf-md-chat_alert_outline"
                :text="_('Prompts')"
                :disabled="mode === 'prompt' || editMode"/>
    <bbn-button v-if="mode === 'prompt' && !editMode"
                icon="nf nf-md-forum_plus"
                :text="_('New prompt')"
                @click="create"
          			class="bbn-left-xsmargin"
                />
    <bbn-button v-if="selectedPromptId && !editMode"
                icon="nf nf-md-comment_edit"
                :text="_('Edit prompt')"
                @click="edit"
                slot="right"/>
    <bbn-button v-if="editMode"
                icon="nf nf-cod-chrome_close"
                :text="_('Close')"
                slot="right"
                @click="() => {editMode = false;}"/>
    <bbn-button v-if="!editMode && mode !== 'chat'"
                icon="nf nf-md-eraser"
                slot="right"
                :disabled="!mode"
                :text="_('Clear the conversation')"
                @click="clear"/>
  </bbn-toolbar>
  <h1 v-if="source.error"
      v-text="source.error"/>
  <div class="bbn-flex-fill bbn-qr-chat">
    <div v-if="mode === null"
         class="bbn-100 bbn-flex-column">
      <h2 class="bbn-bottom-lmargin">
        <?= _("Here you can simply chat with an AI and keep a daily record of all your discussions") ?></h2>
      <h3>
        <?= _("You can create semi automated prompts, where the first part of the prompt and the return format are predefined in a way so that you just need to enter a single parameter to get a sharp response.") ?>
      </h3>
      <h3>
        <?= _("Try our examples in the left menu to get an idea of its capabilities") ?>
      </h3>
    </div>
    <bbn-splitter v-else-if="mode && !isLoading"
                  orientation="horizontal"
                  :resizable="true"
                  :collapsible="true">
      <bbn-pane size="20%">
        <bbn-toolbar v-if="source.years && source.years.length > 1">
        	<bbn-dropdown :source="conversationYearsSource"
                        v-model="selectedYear"/>
        </bbn-toolbar>
        <bbn-loader v-if="listChange"/>
        <bbn-list v-else-if="!listChange"
                  :source="listSource"
                  :alternateBackground="true"
                  @select="listSelectItem"/>
        
      </bbn-pane>
      <bbn-pane>
        <bbn-loader v-if="conversationChange"/>
        <appui-ai-chat-editor v-else-if="editMode"
                              :source="currentSelected"/>
        <appui-ai-chat v-else
                       :source="currentConversation"
                       :mode="mode"
                       :configuration="currentSelected"/>
      </bbn-pane>
    </bbn-splitter>
  </div>

</div>

