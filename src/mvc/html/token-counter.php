<!-- HTML Document -->
<div class="bbn-overlay bbn-flex-height">
  <div class="bbn-w-100 bbn-padding bbn-flex-width">
    <div class="bbn-block bbn-right-lpadding">
      <span bbn-text="_('Count method')"/>
      &nbsp;
      <bbn-dropdown :source="ddSrc"
                    bbn-model="mode"/>
    </div>
    <div class="bbn-flex-fill bbn-xl">
      <span class="bbn-primary-text-alt" bbn-text="numTokens"/> {{_('tokens used in this text')}}
    </div>
  </div>
  <div class="bbn-flex-fill">
    <bbn-textarea bbn-model="code"
                  class="bbn-100"
                  :placeholder="_('Your text here')"/>
  </div>
</div>

