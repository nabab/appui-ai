<!-- HTML Document -->

<div class="bbn-overlay appui-ai-chat-page">
  <bbn-debugger/>
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
                       item-component="appui-ai-model-widget"
                       :items="lastModelsUsed"
                       label="<?= _("Models used") ?>"
                       nodata="<?= _("You haven't used AI yet") ?>"/>
          <bbns-widget :closable="false"
                       item-component="appui-ai-prompt-widget"
                       :items="source.prompts"
                       label="<?= _("Prompts") ?>"
                       :no-data-component="$options.components.newPrompt"
                       :buttons-right="[{
                          action: () => bbn.fn.link(root + 'lab/prompts/new'),
                          icon: 'nf nf-fa-plus',
                          title: '<?= _('New prompt') ?>'
                       }]"/>
          <bbns-widget :closable="false"
                       item-component="appui-ai-chat-widget"
                       bbn-if="lastConfigs.length"
                       :items="lastConfigs"
                       label="<?= _("Settings") ?>"/>
        </bbn-dashboard>
      </div>
    </bbn-container>
    <!-- PROMPTS -->
    <bbn-container url="prompts"
                   :fixed="true"
                   :label="_('Prompts')"
                   :bcolor="$origin.bcolor"
                   :fcolor="$origin.fcolor"
                   :scrollable="false">
      <bbn-router :autoload="true"
                  mode="tabs"
                  ref="promptRouter"
                  :storage="true"
                  storage-full-name="appui-ai-lab-prompt-router">
        <bbn-container url="list"
                       :notext="true"
                       icon="nf nf-fa-list"
                       :fixed="true"
                       :label="_('List')"
                       :bcolor="$origin.bcolor"
                       :fcolor="$origin.fcolor"
                       :scrollable="false">
          <bbn-table :source="source.prompts"
                     ref="promptTable"
                     :toolbar="[{
                       icon:'nf nf-md-forum_plus',
                       label: _('New prompt'),
                       url: root + 'lab/prompts/new'
                     }]">
            <bbns-column field="title"
                         :sortable="true"
                         :filterable="true"
                         :label="_('Title')"
                         :min-width="250"/>
            <bbns-column field="content"
                         :sortable="true"
                         :filterable="true"
                         :label="_('Text')"/>
            <bbns-column field="input"
                         :sortable="true"
                         :filterable="true"
                         :source="formats"
                         :label="_('Input format')"
                         :width="120"/>
            <bbns-column field="output"
                         :sortable="true"
                         :source="formats"
                         :filterable="true"
                         :label="_('Output format')"
                         :width="120"/>
            <bbns-column :sortable="false"
                         :buttons="getPromptButtons"
                         :label="_('Actions')"
                         :width="120"/>
          </bbn-table>
        </bbn-container>
        <bbn-container url="new"
                       :notext="true"
                       icon="nf nf-fa-edit"
                       :fixed="true"
                       :label="_('New prompt')"
                       :bcolor="$origin.bcolor"
                       :fcolor="$origin.fcolor"
                       :scrollable="false">
          <appui-ai-lab-prompt :source="promptSelected"
                               @success="onPromptNewSuccess"/>
        </bbn-container>
      </bbn-router>
    </bbn-container>

  </bbn-router>
</div>
