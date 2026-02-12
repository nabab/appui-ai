<!-- HTML Document -->

<div class="bbn-iblock">
  <bbn-button :title="_('Open experiment')"
              :url="appui.plugins['appui-ai'] + '/lab/experiment/' + source.id"
              icon="nf nf-oct-file_symlink_directory"
              class="bbn-left-xsmargin"
              :notext="true"/>
  <span bbn-text="source.name"/>
</div>
