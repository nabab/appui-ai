(() => {
  return {
    mixins: [bbn.cp.mixins.basic],
    props: {
      source: {
        type: Object,
        required: true
      },
      endpoints: {
        type: Array,
        required: true
      },
      formats: {
        type: Array,
        required: true
      },
      languages: {
        type: Array
      },
      mode : {
        type: String
      }
    },
    data() {
      return {
        showBar2: false,
      }
    },
    computed: {
      currentEndpoint() {
        if (this.source.endpoint) {
          return bbn.fn.getRow(this.endpoints, {id: this.source.endpoint})
        }

        return null;
      },
      currentModel() {
        if (this.source.endpoint && this.source.model) {
          return bbn.fn.getRow(this.currentEndpoint.models, {id: this.source.model});
        }

        return null;
      },
    },
    methods: {
      saveCfg() {
        this.getPopup({
          component: 'appui-ai-config-saver',
          componentOptions: {
            source: this.source
          }
        });
      },
    },
  };
})();
