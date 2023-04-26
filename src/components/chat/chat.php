
<div class="bbn-overlay appui-ai-chat">
  <bbn-scroll ref="scroll" >
    <div class="bbn-w-100 overflow-auto bbn-flex-column bbn-vpadding">
      <appui-ai-chat-item v-for="item in conversation"
                          :key="item.id"
                          :source="item"
                          :outputType="aiFormat"
                          :inputType="userPromptType"/>
    </div>
    <hr class="bbn-hr">
    <h4 v-if="configuration?.title" class="bbn-w-100 bbn-lpadding">{{configuration.content}}</h4>
    <div v-if="(mode !== 'chat') || configuration === null || configuration?.editable" class="bbn-w-100 bbn-spadding bbn-flex-width">
      <div class="bbn-flex-fill">
        <component v-if="userComponent"
                   :is="userComponent"
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
        <span v-text="_('AI response format')"
              class="bbn-s bbn-left-lspace"/>
        <bbn-dropdown v-model="aiFormat"
                      class="bbn-s"
                      :source="promptType"/>
      </div>
    </div>
  </bbn-scroll>
</div>
