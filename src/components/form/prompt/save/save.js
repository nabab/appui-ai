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
        generatePrompt: "I will provide you with a prompt, and your task is to generate an object that describes the prompt. The object should include a title, description, language, and output type. You may choose from three language options: English ('en'), French ('fr'), or Italian ('it'). Please respond with a JSON object that includes the following properties: 'title', 'description', 'language', and 'output'. Each value need to be in same language of the prompt matching language. Your response should be formatted like this: {\"title\": \"\", \"description\": \"\", \"language\": \"\", \"output\": \"\"}. Do not use the prompt in your response. Here is an example prompt that you may use to generate your response:\n\n\n",
        formData: {
          title: "",
          prompt: this.source.prompt ?? "",
          description: "",
          language: "en",
          output: ""
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