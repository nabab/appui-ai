<!-- HTML Document -->

<div class="appui-chat-item bbn-xsmargin bbn-flex-column bbn-radius"
     style="height: auto; max-width: 70%; width: 100%"
     :class="source.ai ? ['flex-end', 'bbn-secondary'] : ['flex-start', 'bbn-primary']">
  <div class="bbn-w-100 bbn-xspadding"
       style="maxWidth: 100%">
    <div v-if="source.loading"
         class="bbn-xspadding bbn-background bbn-text bbn-w-100 bbn-radius">
      <span class="bbn-anim-dots"
            v-text="_('Artificially thinking')"/>
    </div>
    <div v-else-if="source.error"
         class="bbn-state-error bbn-m"
         v-text="_('There was an error while getting the response')"/>
    <component v-else-if="outputType !== 'div'"
               :is="outputType"
               class="bbn-xspadding bbn-background bbn-text bbn-w-100 bbn-radius"
               style="maxWidth: 100%; white-space: break-spaces"
               v-bind="componentOptions(outputType, true)"
               v-model="source.text.trim()">
    </component>
    <div v-else
         class="bbn-xspadding bbn-background bbn-text bbn-w-100 bbn-radius"
         style="maxWidth: 100%; white-space: break-spaces"
         v-text="source.text.trim()"/>

  </div>

  <div class="bbn-flex bbn-w-100 bbn-vxspadding  bbn-hspadding"
       style="margin-top: auto">
    <span class="bbn-small bbn-grey bbn-w-50 bbn-left">
      <span class="bbn-right-smargin bbn-p"
            @click="copy">
        <i class="nf nf-fa-copy"
           :title="_('Copy to clipboard')"/>
      </span>
      {{source.ai ? 'bbn-ai' : _('you')}}
    </span>
    <span class="bbn-small bbn-grey bbn-w-50 bbn-right">{{fdate}}</span>
  </div>
</div>
