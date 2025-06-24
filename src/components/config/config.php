<!-- HTML Document -->
<div>
  <bbn-toolbar :class="['bbn-spadding', 'appui-ai-config-topbar', {'appui-ai-config-withbar2': showBar2}]">
    <div class="bbn-xspadding bbn-border bbn-radius bbn-reactive">
      <span bbn-text="_('Endpoint')"
            class="bbn-link bbn-right-smargin"
            bbn-if="endpoints"
            @click="getRef('endpointEditor').edit()"/>
      <bbn-editable bbn-model="source.endpoint"
                    component="bbn-dropdown"
                    ref="endpointEditor"
                    bbn-if="endpoints"
                    :component-options="{
                      source: endpoints,
                      sourceUrl: false,
                      sourceValue: 'id'
                    }"/>
    </div>

    <div class="bbn-xspadding bbn-border bbn-radius bbn-reactive">
      <span bbn-text="_('Model')"
            class="bbn-link bbn-right-smargin"
            bbn-if="endpoints"
            @click="getRef('modelEditor').edit()"/>
      <bbn-editable bbn-model="source.model"
                    component="bbn-dropdown"
                    ref="modelEditor"
                    :disabled="!currentEndpoint"
                    bbn-if="endpoints"
                    :component-options="{
                      source: currentEndpoint ? currentEndpoint.models : [],
                      sourceValue: 'id'
                    }"/>
    </div>

    <div class="bbn-xspadding bbn-border bbn-radius bbn-reactive"
         bbn-if="languages">
      <span bbn-text="_('Language')"
            class="bbn-link bbn-right-smargin"
            bbn-if="source.model"
            @click="getRef('languageEditor').edit()"/>
      <bbn-editable bbn-model="source.language"
                    component="bbn-dropdown"
                    ref="languageEditor"
                    :disabled="!source.model"
                    bbn-if="endpoints"
                    :component-options="{
                      source: languages,
                      sourceValue: 'code'
                    }"/>
    </div>

    <div slot="right"
         class="bbn-nowrap bbn-m">
      <slot/>
      <bbn-button icon="nf nf-md-cog_outline"
                  :label="_('Advanced settings')"
                  :notext="true"
                  @click="showBar2 = !showBar2"/>
    </div>

  </bbn-toolbar>

  <bbn-toolbar :class="['bbn-bottom-spadding', 'bbn-hspadding', 'appui-ai-config-bottombar', {'appui-ai-config-shownbar': showBar2}]">
    <div class="bbn-xspadding bbn-border bbn-radius bbn-reactive">
      <span bbn-text="_('Format')"
            class="bbn-link bbn-right-smargin"
            bbn-if="endpoints"
            @click="getRef('formatEditor').edit()"/>
      <bbn-editable bbn-model="source.aiFormat"
                    component="bbn-dropdown"
                    ref="formatEditor"
                    :disabled="!source.model"
                    bbn-if="endpoints"
                    :component-options="{
                      source: formats,
                      sourceValue: 'code'
                    }"/>
    </div>

    <div class="bbn-xspadding bbn-border bbn-radius bbn-reactive bbn-c bbn-iblock">
      <span bbn-text="_('Temperature')"
            class="bbn-link bbn-right-smargin"
            @click="getRef('temperatureEditor').edit()"/>
      <bbn-editable bbn-model="source.temperature"
                    component="bbn-numeric"
                    ref="temperatureEditor"
                    :disabled="!source.model"
                    :component-options="{
                      step: 0.01,
                      decimals: 2,
                      min: 0,
                      max: 1,
                      size: 4
                    }"/>
    </div>

    <div class="bbn-xspadding bbn-border bbn-radius bbn-reactive bbn-c bbn-iblock">
      <span bbn-text="_('Presence')"
            class="bbn-link bbn-right-smargin"
            @click="getRef('presenceEditor').edit()"/>
      <bbn-editable bbn-model="source.presence"
                    component="bbn-numeric"
                    :disabled="!source.model"
                    ref="presenceEditor"
                    :component-options="{
                      step: 0.01,
                      decimals: 2,
                      min: 0,
                      max: 1,
                      size: 4
                    }"/>
    </div>

    <div class="bbn-xspadding bbn-border bbn-radius bbn-reactive bbn-c">
      <span bbn-text="_('Frequency')"
            class="bbn-link bbn-right-smargin"
            @click="getRef('frequencyEditor').edit()"/>
      <bbn-editable bbn-model="source.frequency"
                    component="bbn-numeric"
                    :disabled="!source.model"
                    ref="frequencyEditor"
                    :component-options="{
                      step: 0.01,
                      decimals: 2,
                      min: 0,
                      max: 1,
                      size: 4
                    }"/>
    </div>

    <div class="bbn-xspadding bbn-border bbn-radius bbn-reactive bbn-c">
      <span bbn-text="_('Top P')"
            class="bbn-link bbn-right-smargin"
            @click="getRef('topPEditor').edit()"/>
      <bbn-editable bbn-model="source.top_p"
                    component="bbn-numeric"
                    :disabled="!source.model"
                    ref="topPEditor"
                    :component-options="{
                      step: 0.01,
                      decimals: 2,
                      min: 0,
                      max: 1,
                      size: 4
                    }"/>
    </div>

    <div slot="right"
         class="bbn-nowrap bbn-m">
      <bbn-button icon="nf nf-cod-save"
                  :disabled="!source.model"
                  :label="_('Save the configuration')"
                  :notext="true"
                  @click="saveCfg"/>
    </div>

  </bbn-toolbar>
</div>