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
                @success="success">
        <div class="bbn-grid-fields bbn-hpadding bbn-bottom-padded">
          <label><?= _("Initial prompt") ?></label>
          <bbn-textarea :autosize="true"
                        bbn-model="formData.content"
                        :required="true"
                        :resizable="false"
                        :required="true"/>
          <label><?= _("Title of the prompt") ?></label>
          <div class="bbn-flex">
            <bbn-input bbn-model="formData.title"
                      :required="true"/>
            <bbn-button :label="_('Generate title for me')"
                        @click="generateTitle"
                        class="bbn-left-lmargin"
                        :disabled="generating"/>
          </div>
          <label><?= _("User input format") ?></label>
          <div>
            <bbn-dropdown bbn-model="formData.input"
                          :source="formats"
                          source-value="id"
                          :required="true"/>
          </div>
          <label><?= _("AI output format") ?></label>
          <div>
            <bbn-dropdown bbn-model="formData.output"
                          :source="formats"
                          source-value="id"
                          :required="true"/>
          </div>
          <label><?= _("Language") ?></label>
          <div>
            <bbn-dropdown bbn-model="formData.lang"
                          :source="languages"
                          source-value="code"
                          :required="false"/>
          </div>
          <label><?= _("Shortcode") ?></label>
          <div>
            <bbn-input bbn-model="formData.shortcode"
                      :nullable="true"/>
          </div>
        </div>
      </bbn-form>
    </div>
    <div class="bbn-margin bbn-spadding bbn-radius bbn-border bbn-dotted"
         bbn-if="formData.content.length > 5">
      <h3 class="bbn-c bbn-upper bbn-secondary-text-alt"
          style="margin-top: 0">
        <?= _("Test your prompt") ?>
      </h3>
      <div class="bbn-bottom-smargin bbn-background bbn-text"
           style="max-width: 100%; white-space: break-spaces"
           bbn-html="formData.content"/>
      <!--<bbn-form :buttons="[]">-->
        <div bbn-if="userFormatComponent && !changingUserFormatComponent"
             class="bbn-w-100 bbn-bottom-smargin"
             style="min-height: 3rem">
          <component :is="userFormatComponent"
                      bbn-model="input"
                      bbn-bind="userComponentOptions"
                      class="bbn-w-100"/>
        </div>
        <div class="bbn-c">
          <bbn-button class="bbn-bottom-xsmargin"
                      :disabled="loading"
                      @click="send"><?= _("Test") ?></bbn-button>
        </div>
      <!--</bbn-form>-->
      <div>
        <!--component bbn-if="response && !loading"
                    :is="aiFormatComponent"
                    bbn-model="response"
                    bbn-bind="componentOptions(aiFormatComponent, true)"
                    class="overflow-auto bbn-scroll bbn-bottom-xsmargin bbn-w-80">
          {{formData.output === 'div' ? response :  ''}}
      </component-->
        <div bbn-if="response"
             bbn-text="response"/>
        <span bbn-else-if="loading" class="bbn-anim-dots"
          bbn-text="_('Artificially thinking')"/>
      </div>
    </div>
  </bbn-scroll>
</div>
