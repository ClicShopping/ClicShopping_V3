CKEDITOR.dialog.add('chatgptDialog', function(editor) {
  var botUrl = 'https://api.openai.com/v1/completions';
  var apiKey = apiKeyGpt; // Replace with your own API key
  var conversationState = '';

  return {
    title: titleGpt,
    minWidth: 400,
    minHeight: 300,

    contents: [
      {
        id: 'tab1',
        label: 'First Tab',
        title: 'First Tab',
        elements: [
          {
            type: 'textarea',
            id: 'message',
            label: 'Message',
            rows: 4,
            setup: function(element) {
              this.setValue('');
            },
            commit: function(element) {
              var message = this.getValue();
              var dialog = this.getDialog();

              // Send the message to the GPT bot
              var xhr = new XMLHttpRequest();
              xhr.open('POST', botUrl, true);
              xhr.setRequestHeader('Accept', 'application/json');
              xhr.setRequestHeader('Content-Type', 'application/json');
              xhr.setRequestHeader('Authorization',  'Bearer ' + apiKey);
              xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                  var response = JSON.parse(xhr.responseText);
                  var text = response.choices[0].text;

                  // Append the response to the editor
                  editor.editable().insertHtml(`<p>${text}</p>`);

                  // Clear the message input
                  dialog.getContentElement('tab1', 'message').setValue('');
                }
              };
//https://api.openai.com/v1/engines/text-davinci-003/completions
              xhr.send(JSON.stringify({
                model: modelGpt,
                frequency_penalty: frequency_penalty_gpt,
                presence_penalty: presence_penalty_gpt,
                prompt: conversationState + message,
                max_tokens: max_tokens_gpt,
                temperature: temperatureGpt,
                best_of: best_of_gpt,
                top_p: top_p_gpt,
                n: nGpt,
               // stop: '\n',
              }));
              conversationState += message + '\n';
            },
          },
        ],
      },
    ],

    onOk: function() {
      this.commitContent(editor);
    },
  };
});

