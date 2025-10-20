// Javascript Document

(() => {
  return {
    data() {
      return {
        root: appui.plugins['appui-ai'] + '/'
      }
    },
    methods: {
      success(d) {
        if (d.success) {
          const ui = appui.getRegistered('appui-ai-ui');
          const endpoint = bbn.fn.extend({}, d.data.data, {models: d.data.models});
          ui.source.endpoints.push(endpoint);
          if (ui.source.endpoints.length === 1) {
            
          }
        }
      }
    }
  }
})();