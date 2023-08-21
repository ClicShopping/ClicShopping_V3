CKEDITOR.dialog.add('chatgptDialog', function(editor) {
  var botUrl = 'https://api.openai.com/v1/completions'; // Davinci
  var apiKey = apiKeyGpt; // Replace with your own API key
  var conversationState = '';

  return {
    title: titleGpt,
    minWidth: 400,
    minHeight: 300,

    contents: [
      {
        id: 'tab1',
        label: 'Chat Gpt',
        title: 'Chat Gpt',
        elements: [
          {
            type: 'textarea',
            id: 'message',
            label: 'Message',
            rows: 8,
            setup: function(element) {
              this.setValue('');
            },
            commit: function(element) {
              var message = this.getValue();
              var dialog = this.getDialog();

              // Add spinner
              var preloader = document.getElementById('preloader');
              preloader.classList.add('blur'); // Add blur class
              preloader.style.display = 'block';

              // Send the message to the GPT bot
              var xhr = new XMLHttpRequest();
              xhr.open('POST', botUrl, true);
              xhr.setRequestHeader('Accept', 'application/json');
              xhr.setRequestHeader('Content-Type', 'application/json');
              xhr.setRequestHeader('Authorization',  'Bearer ' + apiKey);
              xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                  var response = JSON.parse(xhr.responseText);
                  if (
                    response.choices &&
                    Array.isArray(response.choices) &&
                    response.choices.length > 0 &&
                    response.choices[0].message &&
                    response.choices[0].message.content
                  ) {
                    var text = response.choices[0].message.content; // Use the correct property
                    editor.editable().insertHtml(`<p>${text}</p>`);

                    // Clear the message input
                    dialog.getContentElement('tab1', 'message').setValue('');

                    // Remove spinner
                    preloader.style.display = 'none';
                    preloader.classList.remove('blur'); // Remove blur class
                  } else {
                    // Handle the case when response is empty or invalid
                    console.error('Invalid response from the API');
                  }
                }
              };

              xhr.send(JSON.stringify({
                model: modelGpt,
/*                organization : organizationGpt,*/ //not recognize actually
                frequency_penalty: frequency_penalty_gpt,
                presence_penalty: presence_penalty_gpt,
                prompt: conversationState + message,
                max_tokens: max_tokens_gpt,
                temperature: temperatureGpt,
                best_of: best_of_gpt,
                top_p: top_p_gpt,
                n: nGpt,
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

