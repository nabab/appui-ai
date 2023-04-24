
<div class="bbn-overlay appui-ai-chat">
  <bbn-scroll ref="scroll">
    <div class="bbn-w-100 overflow-auto bbn-flex-column bbn-vpadding">
      <appui-ai-chat-item v-for="item in conversation"
                       :source="item"/>
    </div>
    <hr class="bbn-hr">
    <div class="bbn-w-100 bbn-spadding bbn-flex-width">
      <div class="bbn-flex-fill">
        <bbn-textarea v-model="input"
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
        <span v-text="_('AI response format')"
              class="bbn-s bbn-left-lspace"/>
        <bbn-dropdown v-model="messagePromptType"
                      class="bbn-s"
                      :source="promptType"/>
      </div>
    </div>
  </bbn-scroll>
</div>
