// Javascript Document

(() => {
  return {
    methods: {
      onSuccess(...args) {
        this.$emit('success', ...args);
      }
    }
  }
})();