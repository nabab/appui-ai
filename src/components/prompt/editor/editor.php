<!-- HTML Document -->

<div class="appui-ai-prompt-editor bbn-overlay">
  <bbn-scroll class="bbn-overlay">
    <div class="bbn-margin bbn-spadding bbn-radius bbn-border bbn-dotted">
      <bbn-form :action="root + 'prompt/save'"
                :source="formData"
                ref="form"
                :submitText="formData.id ? 'save' : 'create'"
                :prefilled="true"
                @success="success">

        <div class="bbn-grid-fields bbn-padding">
          <label><?= _("Initial prompt") ?></label>
          <bbn-textarea :autosize="true"
                        v-model="formData.prompt"
                        :required="true"
                        :resizable="false"
                        :required="true"/>
          <label class="bbn-m"><?= _("Title of the prompt") ?></label>
          <div class="bbn-flex">
            <bbn-input class="bbn-m bbn-w-90"
                      v-model="formData.title"
                      :required="true"/>
            <bbn-button :label="_('Generate title for me')"
                        @click="generateTitle"
                        class="bbn-left-lmargin"
                        :disabled="generating"/>
          </div>

          <label><?= _("User input format") ?></label>
          <div>
            <bbn-dropdown v-model="formData.input"
                          :source="formats"
                          :required="true"/>
          </div>

          <label><?= _("AI output format") ?></label>
          <div>
            <bbn-dropdown v-model="formData.output"
                          :source="formats"
                          :required="true"/>
          </div>
          <label><?= _("Language") ?></label>
          <div>
            <bbn-dropdown
                          v-model="formData.lang"
                          :source="languages"
                          :required="false"/>
          </div>

          <label><?= _("Shortcode") ?></label>
          <div>
            <bbn-input v-model="formData.shortcode"
                      :nullable="true"/>
          </div>

          <label>
            <?= _("Temperature") ?> 
            <bbn-tooltip icon="nf nf-oct-info"
                         :source="getRef('infoTemperature')?.innerHTML"
                         bbn-if="ready"/>
          </label>
          <div>
            <bbn-numeric bbn-model="formData.temperature"
                     :min="0"
                     :max="2"
                     :decimals="2"
                     :step="0.01"/>
          </div>

          <label>
            <?= _("Top_P (Nucleus Sampling") ?> 
            <bbn-tooltip icon="nf nf-oct-info"
                         :source="getRef('infoTopP')?.innerHTML"
                         bbn-if="ready"/>
          </label>
          <div>
            <bbn-numeric bbn-model="formData.top_p"
                     :min="0"
                     :max="1"
                     :decimals="2"
                     :step="0.01"/>
          </div>

          <label>
            <?= _("Frequency penalty") ?> 
            <bbn-tooltip icon="nf nf-oct-info"
                         :source="getRef('infoFrequency')?.innerHTML"
                         bbn-if="ready"/>
          </label>
          <div>
            <bbn-numeric bbn-model="formData.frequency_penalty"
                     :min="0"
                     :max="1"
                     :decimals="2"
                     :step="0.01"/>
          </div>

          <label>
            <?= _("Presence penalty") ?> 
            <bbn-tooltip icon="nf nf-oct-info"
                         :source="getRef('infoPresence')?.innerHTML"
                         bbn-if="ready"/>
          </label>
          <div>
            <bbn-numeric bbn-model="formData.presence_penalty"
                     :min="0"
                     :max="1"
                     :decimals="2"
                     :step="0.01"/>
          </div>

        </div>
      </bbn-form>
    </div>
    <div class="bbn-margin bbn-spadding bbn-radius bbn-border bbn-dotted"
         v-if="formData.prompt.length > 5">
      <h3 class="bbn-c">
        <?= _("Test your prompt") ?>
      </h3>
      <div class="bbn-lpadding bbn-bottom-xsmargin bbn-background bbn-text"
            style="maxWidth: 100%; white-space: break-spaces">{{formData.prompt}}</div>
      <div class="bbn-w-100" style="min-height: 3rem">
        <component :is="userFormatComponent"
                    v-model="input"
                    v-bind="userComponentOptions"/>
      </div>
      <div class="bbn-c">
        <bbn-button class="bbn-bottom-xsmargin"
                    :disabled="loading"
                    @click="send"><?= _("Test") ?></bbn-button>
      </div>
      <div>
        <!--component v-if="response && !loading"
                    :is="aiFormatComponent"
                    v-model="response"
                    v-bind="componentOptions(aiFormatComponent, true)"
                    class="overflow-auto bbn-scroll bbn-bottom-xsmargin bbn-w-80">
          {{formData.output === 'div' ? response :  ''}}
      </component-->
        <div bbn-if="response"
             bbn-text="response"/>
        <span v-else-if="loading" class="bbn-anim-dots"
          v-text="_('Artificially thinking')"/>
      </div>
    </div>
  </bbn-scroll>
  <div class="bbn-hidden"
       ref="infoTemperature">
    <div class="bbn-padding">
      <h3 class="bbn-c"><?= _("Temperature") ?></h3>
      <ul>
        <li>
          <strong><?= _("What it does") ?>:</strong> 
          <?= _("Controls the randomness of the generated text.") ?>
        </li>
        <li>
          <strong><?= _("How It Works") ?>:</strong> 
          <?= _("Adjusts the probability distribution of the next word in the text.") ?>
        </li>
        <li>
          <strong><?= _("Benefits") ?>:</strong> 
          <?= _("Higher temperature makes the text more creative and varied, while lower temperature makes it more predictable and focused.") ?>
        </li>
        <li>
          <strong><?= _("Control") ?>:</strong> 
          <?= _("Adjust the temperature value to balance between creativity and coherence.") ?>
        </li>
        <li>
          <strong><?= _("Use Cases") ?>:</strong> 
          <?= _("Useful for creative writing, brainstorming, and generating diverse content.") ?>
        </li>
      </ul>
      <p>
        <?= _("In short, temperature helps AI models generate text that is either more creative or more focused, depending on the setting.") ?>
      </p>
    </div>
  </div>

  <div class="bbn-hidden"
       ref="infoTopP">
    <div class="bbn-padding">
      <h3 class="bbn-c"><?= _("Top_p (Nucleus Sampling)") ?></h3>
      <ul>
        <li>
          <strong><?= _("What it does") ?>:</strong> 
          <?= _("Helps language models generate diverse and coherent text.") ?>
        </li>
        <li>
          <strong><?= _("How It Works") ?>:</strong> 
          <?= _("The model picks words from a dynamic list of top options, based on a probability threshold (p).") ?>
        </li>
        <li>
          <strong><?= _("Benefits") ?>:</strong> 
          <?= _("Makes the text more natural and less repetitive.") ?>
        </li>
        <li>
          <strong><?= _("Control") ?>:</strong> 
          <?= _("Adjust the p value to change how creative or focused the text is.") ?>
        </li>
        <li>
          <strong><?= _("Use Cases") ?>:</strong> 
          <?= _("Great for writing, chatbots, and content creation.") ?>
        </li>
      </ul>
      <p>
        <?= _("In short, top_p makes the text generated by AI models more varied and human-like.") ?>
      </p>
    </div>
  </div>

  <div class="bbn-hidden"
       ref="infoFrequency">
    <div class="bbn-padding">
      <h3 class="bbn-c"><?= _("Frequency penalty") ?></h3>
      <ul>
        <li>
          <strong><?= _("What it does") ?>:</strong> 
          <?= _("Reduces repetition in generated text.") ?>
        </li>
        <li>
          <strong><?= _("How It Works") ?>:</strong> 
          <?= _("Applies a penalty to words that have already appeared frequently.") ?>
        </li>
        <li>
          <strong><?= _("Benefits") ?>:</strong> 
          <?= _("Makes the text more diverse and less repetitive.") ?>
        </li>
        <li>
          <strong><?= _("Control") ?>:</strong> 
          <?= _("Adjust the penalty value to control how much repetition is allowed.") ?>
        </li>
        <li>
          <strong><?= _("Use Cases") ?>:</strong> 
          <?= _("Useful for creative writing, chatbots, and content generation.") ?>
        </li>
      </ul>
      <p>
        <?= _("In short, the frequency penalty helps AI models avoid repeating the same words or phrases too often.") ?>
      </p>
    </div>
  </div>

  <div class="bbn-hidden"
       ref="infoPresence">
    <div class="bbn-padding">
      <h3 class="bbn-c"><?= _("Presence penalty") ?></h3>
      <ul>
        <li>
          <strong><?= _("What it does") ?>:</strong> 
          <?= _("Discourages the use of certain words or topics.") ?>
        </li>
        <li>
          <strong><?= _("How It Works") ?>:</strong> 
          <?= _("Applies a penalty to specific words or topics to reduce their likelihood of appearing in the text.") ?>
        </li>
        <li>
          <strong><?= _("Benefits") ?>:</strong> 
          <?= _("Helps steer the conversation away from unwanted topics.") ?>
        </li>
        <li>
          <strong><?= _("Control") ?>:</strong> 
          <?= _("Adjust the penalty value to control how strongly certain words or topics are avoided.") ?>
        </li>
        <li>
          <strong><?= _("Use Cases") ?>:</strong> 
          <?= _("Useful for chatbots, customer support, and content moderation.") ?>
        </li>
      </ul>
      <p>
        <?= _("In short, the presence penalty helps AI models avoid using specific words or discussing certain topics.") ?>
      </p>
    </div>
  </div>
  

</div>
