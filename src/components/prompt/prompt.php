<!-- HTML Document -->
<div class="bbn-overlay">
  <bbn-splitter orientation="horizontal"
                :resizable="true"
                :collapsible="true"
                class="bbn-overlay">
    <bbn-pane>
      <div class="bbn-w-100">
        <appui-ai-prompt-editor :source="source"
                                @success="onSuccess"/>
      </div>
    </bbn-pane>
    <bbn-pane>
      <div class="bbn-w-100 bbn-padding">
        <p>Messages</p>
      </div>
    </bbn-pane>
  </bbn-splitter>
</div>
