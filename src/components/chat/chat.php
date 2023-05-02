
<div class="bbn-overlay appui-ai-chat">
  <bbn-scroll ref="scroll" >
    <div class="bbn-w-100 overflow-auto bbn-flex-column bbn-vpadding">
      <appui-ai-chat-item v-for="item in conversation"
                          :key="item.id"
                          :source="item"
                          :aiFormat="mode === 'prompt' ? aiFormat : (item.format || 'textarea')"
                          :userFormat="mode === 'prompt' ? userFormat : (item.format || 'textarea')"/>
    </div>
    <hr class="bbn-hr">
    <div v-if="configuration?.title"
         class="bbn-lpadding bbn-bottom-xsmargin bbn-background bbn-text bbn-w-100 bbn-radius"
         style="maxWidth: 100%; white-space: break-spaces"
         >{{configuration.content}}</div>
    <div v-if="(mode !== 'chat') || configuration === null || configuration?.editable" class="bbn-w-100 bbn-padding bbn-flex-width">
      <div class="bbn-flex-fill">
        <component v-if="userFormatComponent"
                   :is="userFormatComponent"
                   v-bind="userComponentOptions"
                   v-model="input"
                   ref="chatPrompt"
                   style="width: 100%"
                   v-focused/>
        <bbn-textarea v-else
                      v-model="input"
                      ref="chatPrompt"
                      style="width: 100%"
                      v-focused
                      :autosize="true"/>
      </div>
      <div class="bbn-block bbn-left-lspace bbn-nowrap">
        <bbn-button class=""
                    @click="send"
                    :disabled="isLoadingResponse"
                    :text="_('send')"/>
        <span  v-if="mode !== 'prompt'"
              v-text="_('AI response format')"
              class="bbn-s bbn-left-lspace"/>
        <bbn-dropdown v-if="mode !== 'prompt'"
                      v-model="aiFormat"
                      class="bbn-s"
                      :source="formats"/>
      </div>
    </div>
  </bbn-scroll>
</div>
