<!-- HTML Document -->

<div class="bbn-iblock">
  <span class="bbn-right-margin">
    <bbn-button :title="_('Edit prompt')"
                @click="edit"
                icon="nf nf-fa-edit"
                :notext="true"/>
    <bbn-button :title="_('Remove prompt')"
                @click="remove"
                icon="nf nf-fa-trash"
                class="bbn-red"
                :notext="true"/>
  </div>
  <span bbn-text="source.title"/>
</div>
