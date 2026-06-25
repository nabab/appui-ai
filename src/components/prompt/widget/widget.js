// Javascript Document

(() => {
  return {
    methods: {
      edit(){
        bbn.fn.link(this.root + 'chat/prompts/' + this.source.id)
      },
      remove(){
        this.ui.deletePrompt(this.source.id);
      }
    }
  }
})();