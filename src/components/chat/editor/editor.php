<!-- HTML Document -->

<div class="appui-ai-chat-editor bbn-spadding">
  <bbn-form :action="root + 'prompt/save'"
            :source="formData"
            :submitText="source.id ? 'save' : 'create'"
            :prefilled="true"
            :validation="isValid"
            @success="success">
    <div class="bbn-grid-fields">
      <label class="bbn-m"><?= _("Title of the prompt") ?></label>
      <bbn-input class="bbn-m"
                 v-model="formData.title"/>

      <label><?= _("Initial prompt") ?></label>
      <div>
        <bbn-textarea :autosize="true"
                      v-model="formData.prompt"
                      :required="true"/>
      </div>

      <label><?= _("User input format") ?></label>
      <div>
        <bbn-dropdown v-model="formData.input"
                      :source="promptType"></bbn-dropdown>
      </div>

      <label><?= _("AI output format") ?></label>
      <div>
        <bbn-dropdown v-model="formData.output"
                      :source="promptType"></bbn-dropdown>
      </div>
      <label  v-if="!formData.id"><?= _("Language") ?></label>
      <div  v-if="!formData.id">
        <bbn-dropdown
                      v-model="formData.lang"
                      :source="languages"
                      :required="true"/>
      </div>

    </div>
    <div class="bbn-margin bbn-spadding bbn-radius bbn-bordered bbn-dotted">
      <h3 class="bbn-c">
        <?= _("Try it") ?>
      </h3>
      <div>
        <component :is="formData.input === 'div' ? 'bbn-textarea' : formData.input"
                   v-model="input"
                   v-bind="componentOptions(formData.input, false)"
                   class="overflow-auto bbn-scroll bbn-bottom-xsmargin bbn-w-80"/>
        <component v-if="!loading"
                   :is="formData.output"
                   v-model="response"
                   v-bind="componentOptions(formData.output, false)"
                   v-text="formData.output === 'div' ? response :  ''"
                   class="overflow-auto bbn-scroll bbn-bottom-xsmargin bbn-w-80"/>
        <br><br>
        <bbn-button class="bbn-bottom-lmargin"
                    @click="send"><?= _("Test") ?></bbn-button>
      </div>
    </div>
  </bbn-form>
</div>
