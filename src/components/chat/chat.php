<div class="bbn-overlay bbn-flex-height appui-ai-chat">
  <appui-ai-config :source="cfg"
                   :endpoints="endpoints"
                   mode="chat"/>
  <div class="bbn-flex-fill">
    <bbn-loader bbn-if="conversationChange"
                class="bbn-overlay"/>
    <div bbn-else
         class="bbn-overlay">
      <bbn-scroll ref="scroll" >
        <div class="bbn-w-100 overflow-auto bbn-flex-column bbn-vpadding">
          <appui-ai-chat-item bbn-for="item in conversation"
                              :key="item.id"
                              :source="item"
                              :ai-format="mode === 'prompt' ? aiFormat : (item.format || 'textarea')"
                              :user-format="mode === 'prompt' ? userFormat : (item.format || 'textarea')"/>
        </div>
        <hr class="bbn-hr">
        <div bbn-if="configuration?.title"
             class="bbn-lpadding bbn-bottom-xsmargin bbn-background bbn-text bbn-w-100 bbn-radius"
             style="maxWidth: 100%; white-space: break-spaces"
             >{{configuration.content}}</div>
        <div bbn-if="(mode !== 'chat') || !configuration || configuration?.editable"
             class="bbn-flex-hcentered">
          <div class="bbn-card bbn-widest bbn-padding">
            <div class="bbn-w-100">
              <component bbn-if="userFormatComponent"
                         :is="userFormatComponent"
                         bbn-bind="userComponentOptions"
                         bbn-model="input"
                         ref="chatPrompt"
                         bbn-focused
                         style="width: 100%"/>
              <bbn-textarea bbn-else
                            bbn-model="input"
                            ref="chatPrompt"
                            style="width: 100%"
                            bbn-focused
                            :resizable="false"
                            :autosize="true"/>
            </div>
            <div class="bbn-w-100 bbn-vmiddle bbn-vspadding">
              <span bbn-text="_('AI response format')"
                    class="bbn-s"/>
              <bbn-dropdown bbn-model="aiFormat"
                            :source="formats"
                            source-value="code"
                            class="bbn-left-space bbn-s"/>
              <bbn-button @click="send"
                          :disabled="isLoadingResponse"
                          :label="_('send')"
                          class="bbn-left-space"/>
            </div>
          </div>
        </div>
      </bbn-scroll>
    </div>
  </div>
</div>
