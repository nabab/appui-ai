<!-- HTML Document -->

<div class="bbn-overlay appui-ai-chat-page">
  <bbn-router :url-navigation="true"
              :autoload="false"
              mode="tabs">
    <!-- DROPDOWNS IN TABS BAR -->
    <div class="bbn-block bbn-nowrap bbn-border-bottom"
         slot="tabs">
    </div>
    <!-- HOME -->
    <bbn-container url="dashboard"
                   :notext="true"
                   icon="nf nf-md-view_dashboard_outline"
                   :fixed="true"
                   :label="_('Lab dashboard')"
                   :bcolor="$origin.bcolor"
                   :fcolor="$origin.fcolor"
                   :scrollable="true">
      <div class="bbn-w-100 bbn-padding">
        <div class="bbn-section">
          <h2 class="bbn-top-nomargin bbn-c">
            <?= _("Welcome to the appui-ai plugin lab") ?>
          </h2>
          <h3 class="bbn-top-nomargin bbn-c">
            <?= _("From this page you can") ?>:
          </h3>
          <h4 class="bbn-top-nomargin bbn-flex-vcentered">
            <ul class="bbn-list">
              <li><?= _("Set up any AI service, including local LLM") ?></li>
              <li><?= _("Create new experiments") ?></li>
              <li><?= _("Prepare and test your AI prompts") ?></li>
              <li><?= _("Compare models and configuration") ?></li>
              <li><?= _("Create datasets for training your models") ?></li>
              <li><?= _("Automatize training") ?></li>
            </ul>
          </h4>
        </div>
      </div>
      <div class="bbn-w-100">
        <bbn-dashboard :scrollable="false">
          <bbns-widget :closable="false"
                       item-component="appui-ai-endpoint-widget"
                       :items="source.endpoints"
                       label="<?= _("API endpoints") ?>"
                       :no-data-component="$options.components.newEndpoint"
                       :buttons-right="[{
                          action: addEndpoint,
                          icon: 'nf nf-fa-plus',
                          title: '<?= _('New endpoint') ?>'
                       }]"/>
          <bbns-widget bbn-if="lastModelsUsed?.length"
                       :closable="false"
                       item-component="appui-ai-lab-model-widget"
                       :items="lastModelsUsed"
                       label="<?= _("Models used") ?>"
                       nodata="<?= _("You haven't used AI yet") ?>"/>
          <bbns-widget :closable="false"
                       item-component="appui-ai-lab-prompt-widget"
                       :items="source.prompts"
                       label="<?= _("Prompts") ?>"
                       :no-data-component="$options.components.newPrompt"
                       :buttons-right="[{
                          action: () => bbn.fn.link(root + 'lab/prompts/new'),
                          icon: 'nf nf-fa-plus',
                          title: '<?= _('New prompt') ?>'
                       }]"/>
          <bbns-widget :closable="false"
                       item-component="appui-ai-lab-experiment-widget"
                       :items="source.experiments"
                       label="<?= _("Experiments") ?>"
                       :no-data-component="$options.components.newExperiment"
                       :buttons-right="[{
                          action: experimentCreate,
                          icon: 'nf nf-fa-plus',
                          title: '<?= _('New experiment') ?>'
                       }]"/>
        </bbn-dashboard>
      </div>
    </bbn-container>
  </bbn-router>
</div>
