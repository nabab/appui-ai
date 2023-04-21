// Javascript Document

(() => {
  return {
    props: {
      source: {
        type: Object,
      }
    },
    data() {
      return {
        root: this.source.root ?? "",
        lang: [
          {
            'text': bbn._('French'),
            'value': 'fr'
          },
          {
            'text': bbn._('English'),
            'value': 'en'
          },
          {
            'text': bbn._('Italian'),
            'value': 'it'
          }
        ],
        type: this.source.type,
        generatePrompt: "Your task is to generate an object that describes a given prompt. The prompt will be provided by me, and you should extract a title, a description, and the language of the prompt. You have to choose one language from English ('en'), French ('fr'), or Italian ('it') to match the language of the prompt. Please respond with a JSON object that includes the following properties: 'title', 'description', and 'language', where each value should be in the same language as the prompt. Do not include the prompt in your response. Your output should be formatted like this: {\"title\": \"\", \"description\": \"\", \"language\": \"\"}. Here's an example prompt that you may use to generate your response:\n\n\n",
        formData: {
          title: "",
          prompt: this.source.prompt ?? "",
          description: "",
          language: "en",
          output: this.source.output,
          input: this.source.input
        },
        isLoading: false
      }
    },
    methods: {
      success() {
        appui.success(bbn._("Prompt saved with success"))
        let chat = this.closest("bbn-container").getComponent().find("appui-ai-chat");
        chat.updatePromptsList();
      },
      failure() {
        appui.success(bbn._("An error occured"))
      },
      validation() {
        let res = true;
        for (const [key, value] of Object.entries(this.formData)) {
          if (!value) {
            bbn.fn.log(key, value);
            res = false;
          }
        }
        return res;
      },
      generate() {
        this.isLoading = true;
        bbn.fn.post(this.source.root + '/AI', {
          prompt: this.generatePrompt + "\n\n'" + this.formData.prompt + "'",
          input: ""
        }, (d) => {
          this.isLoading = false;
          if (d.success) {
            let object = JSON.parse(d.text);

            for (const [key, value] of Object.entries(this.formData)) {
              if (object[key]) {
                this.formData[key] = object[key];
              }
            }
          }
        })
      }
    }
  }
})();