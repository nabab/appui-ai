// Javascript Document

(() => {
  return {
    methods: {
      sync() {
        const ui = appui.getRegistered('appui-ai-ui');
        if (ui) {
          ui.syncModels(this.source.id);
        }
      }
    }
  }
})();