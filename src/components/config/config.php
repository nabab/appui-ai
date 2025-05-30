<!-- HTML Document -->
<div>
  <bbn-toolbar class="bbn-spadding">
    <div class="bbn-xspadding bbn-border bbn-radius bbn-reactive">
      <span bbn-text="_('Endpoint')"
            class="bbn-link bbn-right-smargin"
            bbn-if="endpoints"
            @click="getRef('endpointEditor').edit()"/>
      <bbn-editable bbn-model="currentEndpointId"
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
      <bbn-editable bbn-model="currentModelId"
                    component="bbn-dropdown"
                    ref="modelEditor"
                    :disabled="!currentEndpoint"
                    bbn-if="endpoints"
                    :component-options="{
                      source: currentEndpoint ? currentEndpoint.models : [],
                      sourceValue: 'id'
                    }"/>
    </div>

    <div class="bbn-xspadding bbn-border bbn-radius bbn-reactive bbn-c">
      <span bbn-text="_('Temperature')"
            class="bbn-link bbn-right-smargin"
            @click="getRef('temperatureEditor').edit()"/>
      <bbn-editable bbn-model="source.temperature"
                    component="bbn-numeric"
                    ref="temperatureEditor"
                    :disabled="!currentModel"
                    :component-options="{
                      step: 0.01,
                      decimals: 2,
                      min: 0,
                      max: 1,
                      size: 4
                    }"/>
    </div>

    <div class="bbn-xspadding bbn-border bbn-radius bbn-reactive bbn-c">
      <span bbn-text="_('Presence')"
            class="bbn-link bbn-right-smargin"
            @click="getRef('presenceEditor').edit()"/>
      <bbn-editable bbn-model="source.presence"
                    component="bbn-numeric"
                    :disabled="!currentModel"
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
                    :disabled="!currentModel"
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
                    :disabled="!currentModel"
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
         class="bbn-nowrap">
      <bbn-button icon="nf nf-md-delete"
                  :disabled="!mode"
                  :label="_('Delete the conversation')"
                  @click="deleteChat"/>
    </div>
  </bbn-toolbar>
</div>