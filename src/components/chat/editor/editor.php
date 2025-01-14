<!-- HTML Document -->

<div class="appui-ai-chat-editor bbn-overlay">
  <bbn-scroll class="bbn-overlay">
    <div class="bbn-margin bbn-spadding bbn-radius bbn-border bbn-dotted">
      <bbn-form :action="root + 'prompt/save'"
                :source="formData"
                ref="form"
                :submitText="formData.id ? 'save' : 'create'"
                :prefilled="true"
                @success="success">

        <div class="bbn-grid-fields bbn-padding">
          <label><?= _("Initial prompt") ?></label>
          <bbn-textarea :autosize="true"
                        v-model="formData.prompt"
                        :required="true"
                        :resizable="false"
                        :required="true"/>
          <label class="bbn-m"><?= _("Title of the prompt") ?></label>
          <div class="bbn-flex">
            <bbn-input class="bbn-m bbn-w-90"
                      v-model="formData.title"
                      :required="true"/>
            <bbn-button :label="_('Generate title for me')"
                        @click="generateTitle"
                        class="bbn-left-lmargin"
                        :disabled="generating"/>
          </div>

          <label><?= _("User input format") ?></label>
          <div>
            <bbn-dropdown v-model="formData.input"
                          :source="formats"
                          :required="true"/>
          </div>

          <label><?= _("AI output format") ?></label>
          <div>
            <bbn-dropdown v-model="formData.output"
                          :source="formats"
                          :required="true"/>
          </div>
          <label  v-if="!formData.id"><?= _("Language") ?></label>
          <div  v-if="!formData.id">
            <bbn-dropdown
                          v-model="formData.lang"
                          :source="languages"
                          :required="false"/>
          </div>

          <label><?= _("Shortcode") ?></label>
          <div>
            <bbn-input v-model="formData.shortcode"
                      :nullable="true"/>
          </div>

        </div>
      </bbn-form>
    </div>
    <div class="bbn-margin bbn-spadding bbn-radius bbn-border bbn-dotted"
         v-if="formData.prompt.length > 5">
      <h3 class="bbn-c">
        <?= _("Test your prompt") ?>
      </h3>
      <div class="bbn-lpadding bbn-bottom-xsmargin bbn-background bbn-text"
            style="maxWidth: 100%; white-space: break-spaces">{{formData.prompt}}</div>
      <div class="bbn-w-100" style="min-height: 3rem">
        <component :is="userFormatComponent"
                    v-model="input"
                    v-bind="userComponentOptions"/>
      </div>
      <div class="bbn-c">
        <bbn-button class="bbn-bottom-xsmargin"
                    :disabled="loading"
                    @click="send"><?= _("Test") ?></bbn-button>
      </div>
      <div>
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
  </bbn-scroll>

</div>
