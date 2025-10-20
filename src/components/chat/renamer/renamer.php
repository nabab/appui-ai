<!-- HTML Document -->

<div class="bbn-lpadding bbn-w-100">
  <bbn-form :action="root + '/chat/rename'"
            @success="onSuccess"
            :source="source">
    <bbn-input bbn-model="source.title"
               :required="true"
               ref="input"
               class="bbn-wide"/>
  </bbn-form>
</div>
