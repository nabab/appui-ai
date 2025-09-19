<!-- HTML Document -->

<div style="height: auto; max-width: 70%; width: 100%"
     :class="'appui-chat-item bbn-xsmargin bbn-flex-column bbn-radius bbn-bottom-smargin ' + (ai ? 'flex-end bbn-secondary' : 'flex-start bbn-primary')">
  <div class="bbn-w-100 bbn-xspadding"
       style="max-width: 100%">
    <div bbn-if="source.loading"
         class="bbn-xspadding bbn-background bbn-text bbn-w-100 bbn-radius">
      <span class="bbn-anim-dots"
            bbn-text="_('Artificially thinking')"/>
    </div>
    <div bbn-else-if="source.error"
         class="bbn-state-error bbn-m"
         bbn-text="_('There was an error while getting the response')"/>
    <component bbn-else-if="formatReader && (formatReader !== 'div')"
               :is="formatReader"
               class="bbn-xspadding bbn-background bbn-text bbn-w-100 bbn-radius"
               style="max-width: 100%; white-space: break-spaces"
               :readonly="true"
               bbn-bind="componentOptions(format, true)"
               bbn-model="content"/>
    <div bbn-else
         class="bbn-xspadding bbn-background bbn-text bbn-w-100 bbn-radius"
         style="max-width: 100%; white-space: break-spaces"
         bbn-html="content"/>
  </div>

  <div class="bbn-flex bbn-w-100 bbn-vxspadding  bbn-hspadding"
       style="margin-top: auto">
    <span class="bbn-small bbn-grey bbn-w-50 bbn-left">
      <span bbn-if="cfg"
            class="bbn-right-smargin bbn-p"
            @click="seeRequest">
        <i class="nf nf-fa-cog"
           :title="_('See whole request and reply')"/>
      </span>
      <span class="bbn-right-smargin bbn-p"
            @click="copy">
        <i class="nf nf-fa-copy"
           :title="_('Copy to clipboard')"/>
      </span>
      {{ai ? (source.model ? source.model : 'bbn-ai') : _('you')}}
    </span>
    <span class="bbn-small bbn-grey bbn-w-50 bbn-right">{{fdate}}</span>
  </div>
</div>
