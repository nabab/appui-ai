// Javascript Document

(() => {

  return {
    props: {
      source: {
        type: Object,
        required: true
      },
      ai: {
        type: Boolean
      },
      format: {
        type: String,
        required: true
      },
      date: {
        type: Number,
        required: true
      },
      cfg: {
        type: Object
      }
    },
    data() {
      return {
        formats: appui.getRegistered('appui-ai-ui').source.formats.options,
        cp: null
      }
    },
    computed: {
      content() {
        return this.source.content.replaceAll(/<think.*?<\/think>/gis, '').trim();
      },
      formatComponent() {
        return bbn.fn.getRow(this.formats, {code: this.format}).component;
      },
      formatReader() {
        return bbn.fn.getRow(this.formats, {code: this.format}).reader;
      },
      fdate() {
        return bbn.fn.fdate(this.date, true)
      },
      modelName() {
        let name = '';
        if (this.cfg?.model
          && bbn.fn.isUid(this.cfg.model)
          && this.ui?.source?.endpoints?.length
        ) {
          bbn.fn.each(this.ui?.source?.endpoints, e => {
            if (e.models?.length) {
              const m = bbn.fn.getRow(e.models, {id: this.cfg.model});
              if (m) {
                name = m.name;
                return false;
              }
            }
          });
        }

        return name;
      },
      author() {
        return this.ai ? (this.modelName || 'bbn-ai') : (appui?.user?.name || bbn._('you'));
      }
    },
    methods: {
      seeRequest() {
        if (this.cfg) {
          const d = bbn.fn.clone(this.cfg);
          if (this.modelName) {
            d.modelName = this.modelName;
          }

          const jsn = JSON.stringify(
            Object.fromEntries(
              Object.entries(d).sort(([a], [b]) => a.localeCompare(b, undefined, {sensitivity: 'base'}))
            ),
            null,
            2
          );
          this.closest('bbn-container').getPopup({
            label: false,
            content: `<pre class="bbn-padding">${jsn}</pre>`
          })
        }
      },
      componentOptions(type, readonly) {
        const o = {
          readonly: readonly
        };
        if (type) {
          switch (type) {
            case 'textarea':
              o.autosize = true;
              break;
            case 'code-php':
              o.autosize = true;
              o.theme = 'dracula';
              o.mode = 'php';
              o.fill = false
              break;
            case 'code-js':
              o.autosize = true;
              o.theme = 'dracula';
              o.mode = 'js'
              o.fill = false
              break;
          }
        }
        return o;
      },
      copy() {
        bbn.fn.copy(this.source.content);
        appui.success(bbn._("Copied"));
      }
    },
    mounted() {
      this.cp = this.closest('appui-ai-chat');
    }
  }
})();