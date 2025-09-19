<!-- HTML Document -->

<div class="bbn-iblock">
  <bbn-button :title="_('Synchronize models')"
              @click="sync"
              icon="nf nf-md-sync_circle"
              class="bbn-left-xsmargin"
              :notext="true"/>
  <span bbn-text="source.title"/>
</div>
