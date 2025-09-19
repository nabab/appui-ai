<!-- HTML Document -->

<div class="bbn-overlay">
  <appui-ai-prompt :source="source.data"
                   @success="appui.getRegistered('appui-ai-ui').onPromptEditSuccess"/>
</div>

