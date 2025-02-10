// Javascript Document

(() => {
  return {
    methods: {
      sync() {
        bbn.fn.post(appui.plugins['appui-ai'] + '/actions/sync', {
          id: this.source.id
        }, d => {
          if (d.success) {
            appui.success(bbn._('Sync successful'));
            const dash = this.closest('bbn-dashboard');
            if (dash) {
              const widgetIdx = bbn.fn.search(dash.widgets, {code: 'models'});
              if (widgetIdx > -1) {
                const widget = dash.findAll('bbn-widget')?.[widgetIdx];
                if (widget) {
                  widget.reload()
                }
              }
            }
          }
          else {
            appui.error(bbn._('Error during sync'));
          }
        });
        bbn.fn.log(this.source)
      }
    }
  }
})();