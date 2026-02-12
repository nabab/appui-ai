<!-- HTML Document -->
<div class="bbn-overlay">
  <bbn-splitter orientation="horizontal"
                :resizable="true"
                :collapsible="true"
                class="bbn-overlay">
    <bbn-pane>
      <div class="bbn-w-100">
        <appui-ai-lab-prompt-editor :source="source"
                                @success="onSuccess"/>
      </div>
    </bbn-pane>
    <bbn-pane :scrollable="true">
      <div class="bbn-w-100 bbn-vpadding">
        <div class="bbn-w-100 bbn-ai-lab-selector overflow-auto bbn-flex-column"
              bbn-for="item in source?.items">
          <appui-ai-lab-prompt-item :source="item"
                                :cfg="item.cfg"
                                :ai="item.ai"
                                :date="bbn.fn.timestamp(item.creation_date)"
                                :format="item.ai ? (item.userFormat || 'textarea') : (item.aiFormat || 'textarea')"/>
        </div>
      </div>
    </bbn-pane>
  </bbn-splitter>
</div>
