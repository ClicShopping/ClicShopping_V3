document.addEventListener("DOMContentLoaded", function () {
  // Check if the user has already voted or not
  function hasUserVoted(reviewId) {
    const votedReviews = getVotedReviews();
    return votedReviews.includes(reviewId);
  }

  // Get local voted review to take the id storage
  function getVotedReviews() {
    const votedReviews = localStorage.getItem('votedReviews');
    return votedReviews ? JSON.parse(votedReviews) : [];
  }

  // Save function
  function markReviewAsVoted(reviewId) {
    const votedReviews = getVotedReviews();
    if (!votedReviews.includes(reviewId)) {
      votedReviews.push(reviewId);
      localStorage.setItem('votedReviews', JSON.stringify(votedReviews));

      // Retrieving data from localStorage using the key "reviewVote"
      const storedData = JSON.parse(localStorage.getItem("reviewVote"));
    }
  }

  // Find all "Oui" buttons and attach the click event handler
  const yesButtons = document.querySelectorAll('.yesButton');

  yesButtons.forEach(function (yesButton) {
    yesButton.addEventListener("click", handleButtonClickYes);
  });

// Function to handle the button click for "Oui"
  function handleButtonClickYes() {
    const reviewId = this.getAttribute('data-unique-id');
    const productId = this.getAttribute('data-product-id');
    const customerId = this.getAttribute('data-customer-id');
    const ajaxUrl = this.dataset.ajaxUrl;

    if (productId) {
      if (!hasUserVoted(reviewId)) {
        const parentDiv = this.closest(".moduleProductsInfoReviewCustomersNotice");
        const noButton = parentDiv.querySelector(".noButton");
        const yesValue = parentDiv.querySelector(".yesValue");
        const noValue = parentDiv.querySelector(".noValue");
        const thankYouMessage = parentDiv.querySelector(".thankYouMessage");

        noButton.style.display = "none";
        noValue.style.display = "none";
        yesValue.style.display = "none";
        thankYouMessage.style.display = "inline";

        markReviewAsVoted(reviewId);

        // Make an AJAX call to save the vote to the server
        saveVoteToServer(reviewId, 1, productId, customerId, ajaxUrl);
      }
    } else {
      console.error("Product ID element not found.");
    }
  }

// Find all "Non" buttons and attach the click event handler
  const noButtons = document.querySelectorAll('.noButton');

  noButtons.forEach(function (noButton) {
    noButton.addEventListener("click", handleButtonClickNo);
  });

// Function to handle the button click for "Non"
  function handleButtonClickNo() {
    const reviewId = this.getAttribute('data-unique-id');
    const productId = this.getAttribute('data-product-id');
    const customerId = this.getAttribute('data-customer-id');
    const ajaxUrl = this.dataset.ajaxUrl;

    if (productId) {
      if (!hasUserVoted(reviewId)) {
        const parentDiv = this.closest(".moduleProductsInfoReviewCustomersNotice");
        const yesButton = parentDiv.querySelector(".yesButton");
        const yesValue = parentDiv.querySelector(".yesValue");
        const noValue = parentDiv.querySelector(".noValue");
        const thankYouMessage = parentDiv.querySelector(".thankYouMessage");

        yesButton.style.display = "none";
        yesValue.style.display = "none";
        noValue.style.display = "none";
        thankYouMessage.style.display = "inline";

        markReviewAsVoted(reviewId);

        // Make an AJAX call to save the vote to the server
        saveVoteToServer(reviewId, 0, productId, customerId, ajaxUrl);
      }
    } else {
      console.error("Product ID element not found.");
    }
  }

  // Function to send an AJAX request to save the vote to the server
  function saveVoteToServer(reviewId, vote, productId, customerId, ajaxUrl) {

    const xhr = new XMLHttpRequest();
    xhr.open("POST", ajaxUrl, true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            // Handle the response from the server if needed
            console.log('Vote saved for review ' + reviewId + ': ' + vote + "&product_id=" + productId + "&customer_id=" + customerId);
        }
    };
    xhr.send("reviewId=" + reviewId + "&vote=" + vote + "&product_id=" + productId + "&customer_id=" + customerId);
  }

  // Select all elements with the uniqueId attribute
  const reviewButtons = document.querySelectorAll('[data-unique-id]'); // Update to 'data-unique-id'

  // Add event listeners to each button
  reviewButtons.forEach(function (button) {
    button.addEventListener("click", function () {
      const reviewId = this.getAttribute('data-unique-id'); // Update to 'data-unique-id'
      if (!hasUserVoted(reviewId)) {
        const parentDiv = this.closest(".moduleProductsInfoReviewCustomersNotice");
        const yesButton = parentDiv.querySelector(".yesButton");
        const noButton = parentDiv.querySelector(".noButton");
        const yesValue = parentDiv.querySelector(".yesValue");
        const noValue = parentDiv.querySelector(".noValue");
        const thankYouMessage = parentDiv.querySelector(".thankYouMessage");

        if (this === yesButton) {
          noButton.style.display = "none";
          noValue.style.display = "none";
        } else {
          yesButton.style.display = "none";
          yesValue.style.display = "none";
        }

        thankYouMessage.style.display = "inline";

        markReviewAsVoted(reviewId);

        // Make an AJAX call to save the vote to the server
        saveVoteToServer(reviewId, this === yesButton ? 1 : 0);
      }
    });
  });
});