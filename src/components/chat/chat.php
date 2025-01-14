
<div class="bbn-overlay appui-ai-chat">
  <bbn-scroll ref="scroll" >
    <div class="bbn-w-100 overflow-auto bbn-flex-column bbn-vpadding">
      <appui-ai-chat-item v-for="item in conversation"
                          :key="item.id"
                          :source="item"
                          :ai-format="mode === 'prompt' ? aiFormat : (item.format || 'textarea')"
                          :user-format="mode === 'prompt' ? userFormat : (item.format || 'textarea')"/>
    </div>
    <hr class="bbn-hr">
    <div v-if="configuration?.title"
         class="bbn-lpadding bbn-bottom-xsmargin bbn-background bbn-text bbn-w-100 bbn-radius"
         style="maxWidth: 100%; white-space: break-spaces"
         >{{configuration.content}}</div>
    <div v-if="(mode !== 'chat') || configuration === null || configuration?.editable"
         class="bbn-w-100 bbn-padding">
      <div class="bbn-w-100">
        <component v-if="userFormatComponent"
                   :is="userFormatComponent"
                   v-bind="userComponentOptions"
                   v-model="input"
                   ref="chatPrompt"
                   v-focused
                   style="width: 100%"/>
        <bbn-textarea v-else
                      v-model="input"
                      ref="chatPrompt"
                      style="width: 100%"
                      v-focused
                      :resizable="false"
                      :autosize="true"/>
      </div>
      <div class="bbn-w-100 bbn-vmiddle bbn-vspadding">
        <span v-text="_('AI response format')"
              class="bbn-s"/>
        <bbn-dropdown v-model="aiFormat"
                      :source="formats"
                      class="bbn-left-space bbn-s"/>
        <bbn-button @click="send"
                    :disabled="isLoadingResponse"
                    :label="_('send')"
                    class="bbn-left-space"/>
      </div>
    </div>
  </bbn-scroll>
</div>
