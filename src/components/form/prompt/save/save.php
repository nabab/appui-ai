<!-- HTML Document -->

<bbn-form :source="formData"
          :action="root + '/prompt/save'"
          class="bbn-padding bbn-lg"
          @success="success"
          @failure="failure"
          :validation="validation"
          :disabled="isLoading">
  <div class="bbn-flex-fill bbn-medium bbn-middle">
    <div class="bbn-grid-fields">
      <span>{{_('Title')}}</span>
      <bbn-input v-model="formData.title"
                 class="bbn-wider"/>
      <span>{{_('Prompt')}}</span>
      <bbn-textarea v-model="formData.prompt"/>
      <span>{{_('Description')}}</span>
      <bbn-textarea v-model="formData.description"/>
      <span>{{_('Language')}}</span>
      <bbn-dropdown v-model="formData.language"
                    :source="languages"/>
      <span>{{_('Input type')}}</span>
      <bbn-dropdown v-model="formData.input"
                    :source="type"></bbn-dropdown>
      <span>{{_('Output type')}}</span>
      <bbn-dropdown v-model="formData.output"
                    :source="type"></bbn-dropdown>
      <div class="bbn-grid-full">
        <bbn-button class="generate bbn-primary"
                    @click="generate">Ask the AI to generate title and description</bbn-button>
      </div>
    </div>
  </div>
</bbn-form>