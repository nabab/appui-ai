<!-- HTML Document -->
<bbn-form :action="root + 'actions/endpoint/insert'"
          :source="source"
          @success="success">
  <div class="bbn-grid-fields bbn-padding">
    <div><?= _("Nom du service") ?></div>
    <bbn-input class="bbn-wide"
               bbn-model="source.text"
               placeholder="LM Studio? Open AI?"
               :required="true"/>
    <div><?= _("API's endpoint URL") ?></div>
    <bbn-input class="bbn-wide"
               bbn-model="source.url"
               type="url"
               placeholder="https://api.openai.com"
               :required="true"/>
    <div><?= _("API password") ?></div>
    <bbn-input class="bbn-wide"
               bbn-model="source.pass"/>
    <div>
      <?= _("File capabilities") ?>
    </div>
    <div class="bbn-grid-fields">
      <div><?= _("Mode") ?></div>
      <bbn-dropdown bbn-model="source.fileCapabilities.mode"
                    :source="['auto']"
                    :required="true"/>
      <div><?= _("Images") ?></div>
      <bbn-switch bbn-model="source.fileCapabilities.images"
                  :value="1"
                  :novalue="0"/>
      <div><?= _("PDF Inline") ?></div>
      <bbn-switch bbn-model="source.fileCapabilities.pdf_inline"
                  :value="1"
                  :novalue="0"/>
      <div><?= _("File IDS") ?></div>
      <bbn-switch bbn-model="source.fileCapabilities.file_ids"
                  :value="1"
                  :novalue="0"/>
      <div><?= _("Max Size") ?></div>
      <bbn-numeric bbn-model="source.fileCapabilities.max_size"
                  :min="0"
                  :step="1"/>
    </div>
  </div>
</bbn-form>