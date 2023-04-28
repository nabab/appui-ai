<!-- HTML Document -->

<div class="appui-ai-chat-editor bbn-spadding">
  <bbn-scroll class="bbn-overlay">
    <bbn-form :action="root + 'prompt/save'"
              :source="formData"
              :submitText="source.id ? 'save' : 'create'"
              :prefilled="true"
              :validation="isValid"
              @success="success"
              class="bbn-padding">

      <div class="bbn-grid-fields">
        <label><?= _("Initial prompt") ?></label>
        <bbn-textarea :autosize="true"
                      v-model="formData.prompt"
                      :required="true"
                      class="bbn-w-100"/>
        <label class="bbn-m"><?= _("Title of the prompt") ?></label>
        <div class="bbn-flex">
          <bbn-input class="bbn-m bbn-w-90"
                     v-model="formData.title"/>
          <bbn-button :text="_('Generate title for me')"
                      @click="generateTitle"
                      class="bbn-left-lmargin"
                      :disabled="generating"/>
        </div>

        <label><?= _("User input format") ?></label>
        <div>
          <bbn-dropdown v-model="formData.input"
                        :source="formats"></bbn-dropdown>
        </div>

        <label><?= _("AI output format") ?></label>
        <div>
          <bbn-dropdown v-model="formData.output"
                        :source="formats"></bbn-dropdown>
        </div>
        <label  v-if="!formData.id"><?= _("Language") ?></label>
        <div  v-if="!formData.id">
          <bbn-dropdown
                        v-model="formData.lang"
                        :source="languages"
                        :required="true"/>
        </div>

      </div>
      <div class="bbn-margin bbn-spadding bbn-radius bbn-bordered bbn-dotted bbn-flex-height">
        <h3 class="bbn-c">
          <?= _("Test your prompt") ?>
        </h3>
        <div class="bbn-lpadding bbn-bottom-xsmargin bbn-background bbn-text bbn-w-100 bbn-radius"
         style="maxWidth: 100%; white-space: break-spaces"
         >{{formData.prompt}}</div>
        <div>
          <component :is="userFormatComponent"
                     v-model="input"
                     v-bind="userComponentOptions"
                     class="overflow-auto bbn-scroll bbn-bottom-xsmargin bbn-w-80"/>
          <br><br>
          <bbn-button class="bbn-bottom-xsmargin"
                      :disabled="loading"
                      @click="send"><?= _("Test") ?></bbn-button>
          <br><br>
          <component v-if="response && !loading"
                     :is="aiFormatComponent"
                     v-model="response"
                     v-bind="componentOptions(aiFormatComponent, true)"
                     class="overflow-auto bbn-scroll bbn-bottom-xsmargin bbn-w-80">
            {{formData.output === 'div' ? response :  ''}}
          </component>
          <span v-else-if="loading" class="bbn-anim-dots"
            v-text="_('Artificially thinking')"/>
        </div>
      </div>
    </bbn-form>
  </bbn-scroll>

</div>
