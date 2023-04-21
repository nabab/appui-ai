<div class="bbn-overlay bbn-qr-chat">
  <div v-if="mode === null" class="bbn-100 bbn-flex bbn-middle flex-direction-column">
    <h2 class="bbn-bottom-lmargin">
      {{getRandomIntroSentence()}}
    </h2>
    <bbn-button class="bbn-bottom-lmargin" @click="() => {mode = 'chat'}">Chat</bbn-button>
    <bbn-button @click="() => {mode = 'promptEditor'}">Your prompt</bbn-button>
  </div>
  <div v-if="mode === 'chat'" class="bbn-100">
    <div class="bbn-100 bbn-flex flex-direction-column">
      <bbn-toolbar class="bbn-m bbn-xspadding">
        <bbn-button icon="nf nf-mdi-home"
                    class="bbn-left-xsspace"
                    :text="_('Return to home page')"
                    :notext="true"
                    @click="() => {mode = null}"/>
        <bbn-button icon="nf nf-md-eraser"
                    class="bbn-left-xsspace"
                    :text="_('Clear the conversation')"
                    :notext="true"
                    @click="clear"/>
        <bbn-button icon="nf nf-fa-arrow_right"
                    class="bbn-left-xsspace"
                    :text="_('Send to prompt editor')"
                    :notext="true"
                    @click="() => { mode = 'promptEditor'}"/>


      </bbn-toolbar>

      <div class="bbn-w-100 overflow-auto bbn-flex-height" style="height: 80%">
        <div v-for="item in conversation" class="bbn-xsmargin bbn-flex flex-direction-column " style="height: auto; max-width: 70%; width: 100%" :class="item.ai ? ['flex-end', 'bbn-secondary'] : ['flex-start', 'bbn-primary']">
          <div class="bbn-flex bbn-middle bbn-xspadding" style="maxWidth: 100%">
            <component :is="item.ai ? messagePromptType : userPromptType"
                       class="bbn-w-100"
                       v-model="item.text"
                       v-bind="componentOptions(messagePromptType, true)"/>
          </div>

          <div class="bbn-flex bbn-w-100 bbn-xspadding"
               style="margin-top: auto">
            <span class="bbn-small bbn-grey bbn-w-50 bbn-left">{{item.ai ? 'bbn-ai' : 'you'}}</span>
            <span class="bbn-small bbn-grey bbn-w-50 bbn-right">{{item.creation_date}}</span>
          </div>
        </div>
      </div>
      <hr style="margin: 0">
      <div class="bbn-flex-height bbn-middle bbn-xspadding" style="height: 20%">

        <component :is="userPromptType"
                   v-model="input"
                   v-bind="componentOptions(userPromptType, false)"
                   class="overflow-auto bbn-scroll bbn-bottom-xsmargin bbn-w-80"/>
        <div class="bbn-w-100 bbn-flex" style="justify-content: center">
          <div class="bbn-s">
            <span>User chat type</span>
            <bbn-dropdown v-model="userPromptType"
                          :source="promptType"></bbn-dropdown>
          </div>
          <bbn-button class="bbn-left-lspace"
                      @click="send">send</bbn-button>

          <div class="bbn-s bbn-left-lspace">
            <span>AI response type</span>
            <bbn-dropdown v-model="messagePromptType"
                          :source="promptType"></bbn-dropdown>

          </div>
        </div>
      </div>
    </div>
  </div>
  <bbn-splitter v-if="mode === 'promptEditor'" orientation="horizontal"
                :resizable="true"
                :collapsible="true">
    <bbn-pane size="20%">
      <bbn-list :source="listSource"
                :alternateBackground="true"
                @select="listSelectItem">
      </bbn-list>
    </bbn-pane>
    <bbn-pane>
      <div class="bbn-100 bbn-flex flex-direction-column">
        <bbn-toolbar class="bbn-m bbn-xspadding">
          <bbn-button icon="nf nf-mdi-home"
                      class="bbn-left-xsspace"
                      :text="_('Return to home page')"
                      :notext="true"
                      @click="() => {mode = null}"/>
          <bbn-button icon="nf nf-fa-save"
                      class="bbn-left-xsspace"
                      :text="_('Save the prompt')"
                      :notext="true"
                      @click="savePrompt"/>
          <bbn-button icon="nf nf-md-eraser"
                      class="bbn-left-xsspace"
                      :text="_('Clear the conversation')"
                      :notext="true"
                      @click="clear"/>
          <bbn-button :icon="editMode ? 'nf nf-mdi-pencil_off' : 'nf nf-mdi-pen'"
                      class="bbn-left-xsspace"
                      :text="editMode ? _('Close edit mode')  :  _('Edit the current prompt')"
                      :notext="true"
                      @click="() => {editMode = !editMode}"/>


        </bbn-toolbar>
        <div v-if="selectedPromptId !== null" class="bbn-flex-height bbn-middle" style="maxHeight: 25%">
          <h3 v-if="currentSelected">
            {{currentSelected.title}}
            <i class="bbn-left-xsspace nf nf-seti-info" :title="currentSelected.description"/>
          </h3>
          <h3 v-else>
            {{_('Write a prompt for the AI')}}
          </h3>
          <textarea placeholder="write something..." @input="resize('textarea')" :readonly="!editMode" ref="textarea" v-model="prompt" class="autoGrowTe bbn-w-80" :style="{height: getTextareaSize('textarea')}"></textarea>
        </div>
        <hr v-if="selectedPromptId !== null" style="margin: 0">
        <bbn-toolbar v-if="selectedPromptId === null || editMode" class="bbn-m bbn-xspadding">
          <div class="bbn-left-lspace bbn-s">
            <span>User input format</span>
            <bbn-dropdown v-model="userPromptType"
                          :source="promptType"></bbn-dropdown>
          </div>
          <div class="bbn-left-lspace bbn-s">
            <span>AI response format</span>
            <bbn-dropdown v-model="messagePromptType"
                          :source="promptType"></bbn-dropdown>
          </div>



        </bbn-toolbar>
        <div class="bbn-flex-height bbn-middle bbn-xspadding" style="" :style="{maxHeight: selectedPromptId !== null ? '30%' : '40%'}">

          <component :is="userPromptType"
                     v-model="input"
                     v-bind="componentOptions(userPromptType, false)"
                     class="overflow-auto bbn-scroll bbn-bottom-xsmargin bbn-w-80"/>
          <bbn-button class="bbn-bottom-lmargin"
                      @click="send">send</bbn-button>
        </div>
        <hr style="margin: 0">
        <div class="bbn-w-100 overflow-auto bbn-flex-height" style="" :style="{maxHeight: selectedPromptId !== null ? '45%' : '60%'}">
          <div v-for="item in conversation" class="bbn-xsmargin bbn-flex flex-direction-column " style="height: auto; max-width: 70%; width: 100%" :class="item.ai ? ['flex-end', 'bbn-secondary'] : ['flex-start', 'bbn-primary']">
            <div class="bbn-flex bbn-middle bbn-xspadding" style="maxWidth: 100%">
              <component :is="item.ai ? messagePromptType : userPromptType"
                         class="bbn-w-100"
                         v-model="item.text"
                         v-bind="componentOptions(messagePromptType, true)"/>
            </div>

            <div class="bbn-flex bbn-w-100 bbn-xspadding"
                 style="margin-top: auto">
              <span class="bbn-small bbn-grey bbn-w-50 bbn-left">{{item.ai ? 'bbn-ai' : 'you'}}</span>
              <span class="bbn-small bbn-grey bbn-w-50 bbn-right">{{item.creation_date}}</span>
            </div>
          </div>
        </div>
      </div>
    </bbn-pane>
  </bbn-splitter>
</div>