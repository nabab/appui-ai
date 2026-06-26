<!-- HTML Document -->

<div style="height: auto; max-width: 90%; min-width: 20rem; width: auto;"
     :class="['appui-chat-item', 'bbn-xsmargin', 'bbn-flex-column', 'bbn-radius', 'bbn-bottom-smargin', {
              'flex-start bbn-background-tertiary': ai,
              'flex-end bbn-background-secondary': !ai
            }]">
  <div class="bbn-w-100 bbn-xspadding"
       style="max-width: 100%">
    <div bbn-if="source.loading"
         class="bbn-xspadding bbn-background bbn-text bbn-vmiddle bbn-radius">
      <bbn-loadicon class="bbn-vmiddle bbn-right-sspace"/>
      <span class="bbn-anim-dots"
            bbn-text="_('Artificially thinking')"/>
    </div>
    <div bbn-else-if="source.error"
         class="bbn-state-error bbn-m"
         bbn-text="_('There was an error while getting the response')"/>
    <component bbn-else-if="formatReader && (formatReader !== 'div')"
               :is="formatReader"
               class="bbn-spadding bbn-background bbn-text bbn-w-100 bbn-radius"
               style="max-width: 100%; white-space: break-spaces"
               :readonly="true"
               bbn-bind="componentOptions(format, true)"
               bbn-model="content"/>
    <div bbn-else
         class="bbn-spadding bbn-background bbn-text bbn-w-100 bbn-radius"
         style="max-width: 100%; white-space: break-spaces"
         bbn-html="content"/>
  </div>

  <div :class="['bbn-vmiddle', 'bbn-vxspadding', 'bbn-hspadding', 'bbn-small', {
                'bbn-tertiary-text': ai,
                'bbn-secondary-text': !ai
              }]"
       style="justify-content: space-between; column-gap: var(--lspace); row-gap: var(--sspace)">
    <span class="bbn-vmiddle"
          style="column-gap: var(--sspace)">
      <span bbn-if="cfg"
            class="bbn-p"
            @click="seeRequest"
            :title="_('See whole request and reply')">
        <i class="nf nf-fa-cog"/>
      </span>
      <span class="bbn-p"
            @click="copy"
            :title="_('Copy to clipboard')">
        <i class="nf nf-md-content_copy"/>
      </span>
      <span bbn-text="author"/>
    </span>
    <span bbn-text="fdate"/>
  </div>
</div>
