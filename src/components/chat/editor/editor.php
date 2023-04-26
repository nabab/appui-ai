<!-- HTML Document -->

<div class="appui-ai-chat-editor bbn-spadding">
  <bbn-scroll class="bbn-overlay">
    <bbn-form :action="root + 'prompt/save'"
              :source="formData"
              :submitText="source.id ? 'save' : 'create'"
              :prefilled="true"
              :validation="isValid"
              @success="success">

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
                     class="overflow-auto bbn-scroll bbn-bottom-xsmargin bbn-w-80">
            {{formData.output === 'div' ? response :  ''}}
          </component>
          <br><br>
          <bbn-button class="bbn-bottom-lmargin"
                      @click="send"><?= _("Test") ?></bbn-button>
        </div>
      </div>
    </bbn-form>
  </bbn-scroll>

</div>
