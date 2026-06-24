// Javascript Document

(() => {
  return {
    methods: {
      sync() {
        if (this.ui) {
          this.ui.syncModels(this.source.id);
        }
      },
      remove(){

      }
    }
  }
})();