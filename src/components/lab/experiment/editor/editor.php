<!-- HTML Document -->

<div class="appui-ai-experiment-editor bbn-flex-height">
  <bbn-scroll class="bbn-flex-fill">
    <div class="bbn-margin bbn-spadding bbn-radius bbn-border bbn-dotted">
      <bbn-form :action="appui.plugins['appui-ai'] + '/lab/actions/experiment/save'"
                :source="source"
                ref="form"
                :submitText="source.id ? 'save' : 'create'"
                @success="success">

        <div class="bbn-grid-fields bbn-padding">
          <label><?= _("Name") ?></label>
          <bbn-input bbn-model="source.name"
                    :required="true"
                    class="bbn-wider"/>
          <label><?= _("Description") ?></label>
          <bbn-textarea :autosize="true"
                        bbn-model="source.description"
                        class="bbn-wider"
                        rows="5"
                        :resizable="true"/>

        </div>
      </bbn-form>
    </div>
  </bbn-scroll>
</div>
