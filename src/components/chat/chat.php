<!-- HTML Document -->

<div class="bbn-overlay bbn-qr-chat">
  <bbn-splitter orientation="horizontal"
                :resizable="true"
                :collapsible="true">
    <bbn-pane size="20%">
      <bbn-list :source="listSource"
                :alternateBackground="true"
                @select="listSelectItem">
      </bbn-list>
    </bbn-pane>
    <bbn-pane>
      <div class="bbn-overlay bbn-flex-height">
        <bbn-toolbar class="bbn-m"
                     style="padding-top: 5px">
          <bbn-button icon="nf nf-fa-save"
                      class="bbn-left-xsspace"
                      :text="_('Save the prompt')"
                      :notext="true"
                      @click="savePrompt"/>
          <bbn-button icon="nf nf-md-eraser"
                      class="bbn-left-xsspace"
                      :text="_('Clear the conversation')"
                      :notext="true"
                      @click="clear"/>
        </bbn-toolbar>
        <div class="bbn-flex-fill">
          <div class="bbn-100">
            <bbn-splitter orientation="vertical"
                          :resizable="true"
                          :collapsible="true">
              <bbn-pane size="25%">
                <div class="bbn-flex flex-direction-column bbn-spadding bbn-overlay bbn-middle">
                  <div class="bbn-flex-fill bbn-middle">
                    <h3 v-if="currentSelected">
                      {{currentSelected.title}}
                      <i class="bbn-left-xsspace nf nf-seti-info" :title="currentSelected.description"/>
                    </h3>
                    <h3 v-else>
                      {{_('Write a prompt for the AI')}}
                    </h3>
                  </div>

                  <bbn-textarea class="user-input bbn-bottom-xsmargin bbn-left-xspadding bbn-h-100 bbn-w-100"
                                :resizable="false"
                                v-model="prompt"></bbn-textarea>
                </div>
              </bbn-pane>
              <bbn-pane size="25%">
                <div class="bbn-flex flex-direction-column bbn-spadding bbn-overlay bbn-middle">
                  <bbn-textarea class="user-input bbn-bottom-xsmargin bbn-left-xspadding bbn-h-100 bbn-w-100"
                                :resizable="false"
                                v-model="input"></bbn-textarea>
                  <bbn-button class=""
                              @click="send">send</bbn-button>
                </div>
              </bbn-pane>
              <bbn-pane>
                <div class="bbn-flex bbn-flex-height flex-direction-column conversation-container bbn-top-xsmargin bbn-bottom-xsmargin"
                     style="overflow: auto;">
                  <div v-for="item in conversation" class="bbn-w-90 bbn-flex-height bbn-left-smargin bbn-bottom-smargin">
                    <div class="bbn-bordered bbn-left-spadding bbn-right-spadding bbn-top-spadding bbn-radius bbn-flex-height"
                         :class="item.ai ? ['flex-end', 'bbn-secondary'] : ['flex-start', 'bbn-primary']"
                         style="min-width: 300px; max-width: 70%">
                      <!--span class="bbn-w-100 bbn-bottom-xspadding" v-html="item.text"></span-->
                      <bbn-markdown v-model="item.text"></bbn-markdown>
                      <div class="bbn-flex bbn-w-100"
                           style="margin-top: auto">
                        <span class="bbn-small bbn-grey bbn-w-50 bbn-left">{{item.ai ? 'bbn-ai' : 'you'}}</span>
                        <span class="bbn-small bbn-grey bbn-w-50 bbn-right">{{item.creation_date}}</span>
                      </div>
                    </div>
                  </div>
                </div>
              </bbn-pane>
            </bbn-splitter>
          </div>
        </div>
      </div>
    </bbn-pane>
  </bbn-splitter>
</div>
