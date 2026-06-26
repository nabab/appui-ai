<div class="bbn-overlay bbn-flex-height appui-ai-chat">
  <appui-ai-config :source="cfg"
                   :endpoints="endpoints"
                   :formats="formats"
                   :languages="languages"
                   mode="chat">
    <bbn-button bbn-if="source?.conversation?.length > 2"
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
  <div class="bbn-flex-fill"
       bbn-if="!conversationChange">
    <div class="bbn-overlay">
      <bbn-scroll ref="scroll">
        <div class="bbn-w-100 bbn-vpadding">
          <div class="bbn-w-100 bbn-ai-chat-selector overflow-auto bbn-flex-column"
               bbn-for="item in source.conversation">
            <appui-ai-chat-item bbn-for="msg in item.messages"
                                :source="msg"
                                :cfg="item.cfg"
                                :ai="msg.role === 'assistant'"
                                :date="item.asked"
                                :format="msg.role === 'assistant' ? (item.aiFormat || 'textarea') : (item.userFormat || 'textarea')"/>
          </div>
        </div>
      </bbn-scroll>
    </div>
  </div>
  <div bbn-if="!conversationChange"
       class="bbn-w-100 bbn-top-padding">
    <div bbn-if="(mode !== 'chat') || !configuration || configuration?.editable"
         class="bbn-flex-hcentered">
      <div class="bbn-card bbn-padding bbn-bottom-margin"
           style="width: calc(100% - calc(var(--space) * 2))">
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
        <div class="bbn-vmiddle bbn-top-spadding"
             style="justify-content: flex-end">
          <bbn-button bbn-if="input.length || (conversation.length <= 1)"
                      @click="send"
                      :disabled="!input.length || isLoadingResponse"
                      :label="_('Send')"
                      icon="nf nf-fa-circle_arrow_up"/>
          <bbn-button bbn-else
                      @click="resend"
                      :label="_('Resend')"
                      :disabled="isLoadingResponse"/>
        </div>
      </div>
    </div>
    <div bbn-if="configuration?.title"
         class="bbn-lpadding bbn-bottom-xsmargin bbn-background bbn-text bbn-w-100 bbn-radius"
         style="max-width: 100%; white-space: break-spaces"
         bbn-html="configuration.content"/>
  </div>
</div>
