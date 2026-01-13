<!-- HTML Document -->

<div class="appui-ai-prompt-editor bbn-flex-height">
  <appui-ai-lab-config :source="formData"
                   :endpoints="endpoints"
                   :formats="formats"
                   :languages="languages"
                   mode="prompt"
                   bbn-if="formData">
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
    
  </appui-ai-lab-config>
  <bbn-scroll class="bbn-flex-fill"
              bbn-if="formData">
    <div class="bbn-margin bbn-spadding bbn-radius bbn-border bbn-dotted">
      <bbn-form :action="root + 'lab/actions/prompt/save'"
                :source="formData"
                ref="form"
                :submitText="formData.id ? 'save' : 'create'"
                :prefilled="true"
                @success="success">

        <div class="bbn-grid-fields bbn-padding">
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

        </div>
      </bbn-form>
    </div>
    <div class="bbn-margin bbn-spadding bbn-radius bbn-border bbn-dotted"
         bbn-if="formData.content.length > 5">
      <h3 class="bbn-c">
        <?= _("Test your prompt") ?>
      </h3>
      <div class="bbn-lpadding bbn-bottom-xsmargin bbn-background bbn-text"
            style="maxWidth: 100%; white-space: break-spaces">{{formData.content}}</div>
      <div class="bbn-w-100" style="min-height: 3rem">
        <component :is="userFormatComponent"
                    bbn-model="input"
                    bbn-bind="userComponentOptions"/>
      </div>
      <div class="bbn-c">
        <bbn-button class="bbn-bottom-xsmargin"
                    :disabled="loading"
                    @click="send"><?= _("Test") ?></bbn-button>
      </div>
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
