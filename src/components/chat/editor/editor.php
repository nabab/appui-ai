<!-- HTML Document -->

<div class="appui-ai-chat-editor">
  <bbn-form :action="root + 'actions/save'"
            :source="source">
    <div class="bbn-grid-fields">
      <label class="bbn-m"><?= _("Title of the prompt") ?></label>
      <bbn-input class="bbn-m"
                 v-model="source.title"/>

      <label><?= _("Initial prompt") ?></label>
      <div>
        <bbn-textarea :autosize="true"
                      v-model="source.prompt"
                      :required="true"/>
      </div>

      <label><?= _("User input format") ?></label>
      <div>
        <bbn-dropdown v-model="source.userFormat"
                      :source="promptType"></bbn-dropdown>
      </div>

      <label><?= _("AI output format") ?></label>
      <div>
        <bbn-dropdown v-model="source.aiFormat"
                      :source="promptType"></bbn-dropdown>
      </div>
    </div>
    <div class="bbn-margin bbn-radius bbn-bordered bbn-dotted">
      <h3 class="bbn-c">
        <?= _("Try it") ?>
      </h3>
      <div>
        <component :is="userPromptType"
                   v-model="input"
                   v-bind="componentOptions(userPromptType, false)"
                   class="overflow-auto bbn-scroll bbn-bottom-xsmargin bbn-w-80"/>
        <br><br>
        <bbn-button class="bbn-bottom-lmargin"
                    @click="send"><?= _("Test") ?></bbn-button>
      </div>
    </div>
  </bbn-form>
</div>
