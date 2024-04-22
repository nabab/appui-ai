(() => {
  return {
    data() {
      return {
        code: '',
        mode: 'max',
        ddSrc: [
          {
            text: bbn._('Average'),
            value: 'average'
          }, {
            text: bbn._('Words'),
            value: 'words'
          }, {
            text: bbn._('Characters'),
            value: 'chars'
          }, {
            text: bbn._('Maximum'),
            value: 'max'
          }, {
            text: bbn._('Minimum'),
            value: 'min'
          }
        ]
      }
    },
    computed: {
      numTokens() {
        return bbn.fn.money(this.estimateTokens(this.code, this.mode), false, '', '0')
      }
    },
    methods: {
      /**
       * method can be "average", "words", "chars", "max", "min", defaults to "max"
       * "average" is the average of words and chars
       * "words" is the word count divided by 0.75
       * "chars" is the char count divided by 4
       * "max" is the max of word and char
       * "min" is the min of word and char
       */
      estimateTokens(text, method = "max") {
        if (!text) {
          return 0;
        }
        const word_count = text.split(" ").length;
        const char_count = text.length;
        let tokens_count_word_est = (word_count / 0.75);
        let tokens_count_char_est = (char_count / 4.0);
      
        // Include additional tokens for spaces and punctuation marks
        const additional_tokens = (text.match(/[\s.,!?;]/g) || []).length;
      
        tokens_count_word_est += additional_tokens
        tokens_count_char_est += additional_tokens
      
        let output = 0
        switch (method) {
          case "average":
            output = (tokens_count_word_est + tokens_count_char_est) / 2;
            break;
          case "words":
            output = tokens_count_word_est;
            break;
          case "chars":
            output = tokens_count_char_est;
            break;
          case "max":
            output = Math.max(tokens_count_word_est, tokens_count_char_est);
            break;
          case "min":
            output = Math.min(tokens_count_word_est, tokens_count_char_est);
            break;
          default:
            throw Error("Invalid method. Use 'average', 'words', 'chars', 'max', or 'min'.");
        }
      
        return Math.round(output);
        
      }
    }
  }
})();