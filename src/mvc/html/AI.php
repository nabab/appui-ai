<!-- HTML Document -->

<div class="bbn-overlay">
  <h1 v-if="source.error"
      v-text="source.error"/>
  <appui-ai-chat v-else
                 :source="source"/>
</div>

