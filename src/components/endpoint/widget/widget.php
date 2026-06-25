<!-- HTML Document -->

<div class="bbn-iblock">
  <span class="bbn-right-margin">
    <bbn-button :title="_('Synchronize models')"
                @click="sync"
                icon="nf nf-fa-refresh"
                :notext="true"/>
    <bbn-button :title="_('Remove endpoint')"
                @click="remove"
                icon="nf nf-fa-trash"
                class="bbn-red"
                :notext="true"/>
  </div>
  <span bbn-text="source.text"/>
</div>
