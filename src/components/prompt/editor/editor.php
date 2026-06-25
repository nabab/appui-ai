<!-- HTML Document -->

<div class="appui-ai-prompt-editor bbn-flex-height">
  <appui-ai-config bbn-if="formData"
                   :source="formData"
                   :endpoints="endpoints"
                   :formats="formats"
                   :languages="languages"
                   mode="prompt">
    <!--bbn-button bbn-if="source?.conversation.length > 2"
                icon="nf nf-md-cursor_default_outline"
                :label="_('Selection mode')"
                :notext="true"
                @click="isSelecting = !isSelecting"/>
    <bbn-button icon="nf nf-cod-trash"
                :disabled="!mode"
                :label="_('Delete the conversation')"
                :notext="true"
                @click="deleteChat"/-->
  </appui-ai-config>
  <bbn-scroll class="bbn-flex-fill"
              bbn-if="formData">
    <div class="bbn-margin bbn-spadding bbn-radius bbn-border bbn-dotted">
      <h3 class="bbn-c bbn-upper bbn-secondary-text-alt"
          style="margin-top: 0">
        <?= _("Configure your prompt") ?>
      </h3>
      <bbn-form :action="root + 'prompt/save'"
                :source="formData"
                ref="form"
                :submitText="formData.id ? 'save' : 'create'"
                :prefilled="true"
                @success="success"
                mode="big">
        <div class="bbn-grid-fields bbn-hpadding bbn-bottom-padding">
          <label><?= _("Initial prompt") ?></label>
          <bbn-textarea :autosize="true"
                        bbn-model="formData.content"
                        :required="true"
                        :resizable="false"
                        :required="true"/>
          <label><?= _("Title of the prompt") ?></label>
          <div class="bbn-flex-width">
            <bbn-input bbn-model="formData.title"
                      :required="true"
                      class="bbn-flex-fill"/>
            <bbn-button :label="_('Auto-generate title')"
                        @click="generateTitle"
                        class="bbn-left-lmargin"
                        :disabled="generatingTitle"
                        icon="nf nf-cod-wand"/>
          </div>
          <label><?= _("User input format") ?></label>
          <bbn-dropdown bbn-model="formData.input"
                        :source="formats"
                        source-value="id"
                        :required="true"/>
          <label><?= _("AI output format") ?></label>
          <bbn-dropdown bbn-model="formData.output"
                        :source="formats"
                        source-value="id"
                        :required="true"/>
          <label><?= _("Language") ?></label>
          <bbn-dropdown bbn-model="formData.cfg.language"
                        :source="languages"
                        source-value="code"
                        :required="false"
                        :nullable="true"/>
          <label><?= _("Shortcode") ?></label>
          <bbn-input bbn-model="formData.shortcode"
                    :nullable="true"/>
        </div>
      </bbn-form>
    </div>
    <div class="bbn-margin bbn-spadding bbn-radius bbn-border bbn-dotted"
         bbn-if="formData.content.length > 5">
      <h3 class="bbn-c bbn-upper bbn-secondary-text-alt"
          style="margin-top: 0">
        <?= _("Test your prompt") ?>
      </h3>
      <bbn-form :buttons="['cancel', 'submit']"
                @submit="testPrompt"
                @cancel="response = ''"
                :source="testFormData"
                :submit-text="_('Test')"
                :cancel-text="_('Reset')"
                mode="big"
                :disabled="loading">
        <div class="bbn-bottom-padding">
          <div class="bbn-bottom-smargin bbn-background bbn-text"
            style="max-width: 100%; white-space: break-spaces"
            bbn-html="formData.content"/>
          <div bbn-if="userFormatComponent && !changingUserFormatComponent"
              class="bbn-w-100 bbn-bottom-smargin">
            <component :is="userFormatComponent"
                        bbn-model="testFormData.input"
                        bbn-bind="userComponentOptions"
                        class="bbn-w-100"/>
          </div>
        </div>
      </bbn-form>
    </div>
    <div class="bbn-margin bbn-spadding bbn-radius bbn-border bbn-dotted"
         bbn-if="response?.length || loading">
      <h3 class="bbn-c bbn-upper bbn-tertiary-text-alt"
          style="margin-top: 0">
        <?= _("Response") ?>
      </h3>
      <component bbn-if="response && !loading"
                 :is="aiFormatComponent"
                 bbn-model="response"
                 bbn-bind="aiComponentOptions"
                 class="bbn-w-100">
        {{formData.output === 'div' ? response :  ''}}
      </component>
      <div bbn-else-if="loading"
           class="bbn-middle">
        <bbn-loadicon class="bbn-vmiddle bbn-right-sspace"/>
        <span bbn-text="_('Artificially thinking')"/>
      </div>
    </div>
  </bbn-scroll>
</div>
