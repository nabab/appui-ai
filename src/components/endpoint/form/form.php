<!-- HTML Document -->

<bbn-form :action="root + 'actions/endpoint/insert'"
          :source="source">
  <div class="bbn-grid-fields bbn-padding">
    
    <div><?= _("Nom du service") ?></div>
    <div>
      <bbn-input class="bbn-wide"
                 bbn-model="source.text"
                 placeholder="LM Studio? Open AI?"
                 :required="true"/>
    </div>
    
    <div>
      <?= _("API's endpoint URL") ?>
    </div>
    <div>
      <bbn-input class="bbn-wide"
                 bbn-model="source.url"
                 type="url"
                 placeholder="https://api.openai.com"
                 :required="true"/>
    </div>

    <div>
      <?= _("API password") ?>
    </div>
    <div>
      <bbn-input class="bbn-wide"
                 bbn-model="source.pass"
                 :required="true"/><br>
    </div>
    <div class="bbn-grid-full">
      <em><?= _("If you use a local server and do not need password, still enter something, anything") ?></em>
    </div>
  </div>
</bbn-form>
