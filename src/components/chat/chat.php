<div class="bbn-overlay bbn-flex-height appui-ai-chat">
  <appui-ai-config :source="cfg"
                   :endpoints="endpoints"
                   :formats="formats"
                   :languages="languages"
                   mode="chat">
    <bbn-button bbn-if="source?.conversation.length > 2"
                icon="nf nf-md-cursor_default_outline"
                :label="_('Selection mode')"
                :notext="true"
                @click="isSelecting = !isSelecting"/>
    <bbn-button icon="nf nf-cod-trash"
                :disabled="!mode"
                :label="_('Delete the conversation')"
                :notext="true"
                @click="deleteChat"/>
  </appui-ai-config>
  <div class="bbn-flex-fill"
       bbn-if="conversationChange">
    <bbn-loader class="bbn-overlay"/>
  </div>
  <div class="bbn-w-100 bbn-bottom-padding">
    <div bbn-if="(mode !== 'chat') || !configuration || configuration?.editable"
         class="bbn-flex-hcentered">
      <div class="bbn-card bbn-widest bbn-padding bbn-bottom-margin">
        <div class="bbn-w-100">
          <component bbn-if="userFormatComponent"
                     :is="userFormatComponent"
                     bbn-bind="userComponentOptions"
                     bbn-model="input"
                     ref="chatPrompt"
                     bbn-focused
                     style="width: 100%"
                     @keyup="checkSend"/>
          <bbn-textarea bbn-else
                        bbn-model="input"
                        ref="chatPrompt"
                        style="width: 100%"
                        bbn-focused
                        :resizable="false"
                        :autosize="true"
                        @keyup="checkSend"/>
        </div>
        <div class="bbn-w-100 bbn-vmiddle bbn-vspadding">
          <bbn-button bbn-if="input.length || (conversation.length <= 1)"
                      @click="send"
                      :disabled="!input.length || isLoadingResponse"
                      :label="_('Send')"/>
          <bbn-button bbn-else
                      @click="resend"
                      :label="_('Resend')"/>
        </div>
      </div>
    </div>
    <div bbn-if="configuration?.title"
         class="bbn-lpadding bbn-bottom-xsmargin bbn-background bbn-text bbn-w-100 bbn-radius"
         style="maxWidth: 100%; white-space: break-spaces"
         bbn-html="configuration.content"/>
    <hr class="bbn-hr">
  </div>
  <div class="bbn-flex-fill"
       bbn-else>
    <div class="bbn-overlay">
      <bbn-scroll ref="scroll">
        <div class="bbn-w-100 bbn-vpadding">
          <div class="bbn-w-100 bbn-ai-chat-selector overflow-auto bbn-flex-column"
               bbn-for="item in source?.conversation">
            <appui-ai-chat-item bbn-for="msg in item.messages"
                                :source="msg"
                                :cfg="item.cfg"
                                :ai="msg.role === 'assistant'"
                                :date="item.asked"
                                :format="msg.role === 'assistant' ? (item.userFormat || 'textarea') : (item.aiFormat || 'textarea')"/>
          </div>
        </div>
      </bbn-scroll>
    </div>
  </div>
</div>
