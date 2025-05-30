(() => {
  return {
    props: {
      source: {
        type: Object,
        required: true
      },
      endpoints: {
        type: Array
      },
      endpoint: {
        type: String
      },
      model: {
        type: String
      },
      mode : {
        type: String
      }
    },
    data() {
      return {
        currentEndpointId: this.endpoint || this.endpoints[0].id || null,
        currentModelId: this.model || this.currentEndpoint?.models?.[0]?.id
      }
    },
    computed: {
      currentEndpoint() {
        if (this.currentEndpointId) {
          return bbn.fn.getRow(this.endpoints, {id: this.currentEndpointId})
        }

        return null;
      },
      currentModel() {
        if (this.currentEndpoint && this.currentModelId) {
          return bbn.fn.getRow(this.currentEndpoint.models, {id: this.currentModelId});
        }

        return null;
      },
    }
  };
})();
