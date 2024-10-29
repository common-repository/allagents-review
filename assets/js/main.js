jQuery(document).ready(function ($) {
  // Function to scroll left
  $(".scroll-arrow.left").click(function () {
    var scrollContainer = $(".scroll-container");
    var scrollAmount = 200; // Adjust scroll amount as needed
    scrollContainer.animate({ scrollLeft: "-=" + scrollAmount }, 300);
  });

  var reviews = []; // Array to store all reviews
  const lastCount = parseInt($("#itemLast").val());
  const perPage = parseInt($("#perPage").val());
  let page = parseInt($("#currentPage").val()); // Convert to integer

  function fetchReviews(page) {
    // Perform AJAX request to fetch all reviews
    $.ajax({
      url: ajax_object.ajax_url,
      type: "POST",
      data: {
        action: "allagentsreviews_load_more_reviews_action",
        nonce: ajax_object.ajax_nonce,
      },
      dataType: "JSON",
      beforeSend: function () {
        // Show loading spinner and hide load more button
        $("#loading-spinner-allAgents").removeClass("d-none");
        $("#load-more-btn, .scroll-arrow.right").addClass("d-none");
      },
      success: function (response) {
        if (response && response.length > 0) {
          // Check if response is not empty
          // Store all reviews;
          reviews = response;
          // Display reviews for the current page
          displayReviews(page);
        }
      },
      complete: function () {
        $("#load-more-btn, .scroll-arrow.right").removeClass("d-none");
        $("#loading-spinner-allAgents").addClass("d-none");
      },
    });
  }

  function displayRatingIcons(rating) {
    var maxRating = 5; // Maximum rating value
    var fullStar =
      '<img width="20" alt="full star" src="/wp-content/plugins/allagents-review/assets/images/full-star.svg"/>'; // HTML for full star icon
    var halfStar =
      '<img width="20" alt="half star" src="/wp-content/plugins/allagents-review/assets/images/half-star.svg"/>'; // HTML for half star icon
    var emptyStar =
      '<img width="20" alt="empty star" src="/wp-content/plugins/allagents-review/assets/images/empty-star.svg"/>'; // HTML for empty star icon

    var output = "";

    // Calculate the number of full stars
    var fullStars = Math.floor(rating);

    // Calculate whether there's a half star
    var hasHalfStar = rating - fullStars >= 0.5;

    // Add full stars
    for (var i = 0; i < fullStars; i++) {
      output += fullStar;
    }

    // Add half star if necessary
    if (hasHalfStar) {
      output += halfStar;
      fullStars++; // Increment full stars count to adjust for half star
    }

    // Add empty stars to reach maximum rating
    var emptyStars = maxRating - fullStars;
    for (var i = 0; i < emptyStars; i++) {
      output += emptyStar;
    }

    return output;
  }

  function timeAgo(date) {
    var timestamp = new Date(date).getTime();
    var current_time = new Date().getTime();
    var time_diff = current_time - timestamp;
    var seconds = time_diff / 1000;
    var minutes = Math.round(seconds / 60);
    var hours = Math.round(seconds / 3600);
    var days = Math.round(seconds / 86400);
    var weeks = Math.round(seconds / 604800);
    var months = Math.round(seconds / 2629440);
    var years = Math.round(seconds / 31553280);

    if (seconds <= 60) {
      return "Just now";
    } else if (minutes <= 60) {
      if (minutes == 1) {
        return "1 minute ago";
      } else {
        return minutes + " minutes ago";
      }
    } else if (hours <= 24) {
      if (hours == 1) {
        return "1 hour ago";
      } else {
        return hours + " hours ago";
      }
    } else if (days <= 7) {
      if (days == 1) {
        return "Yesterday";
      } else {
        return days + " days ago";
      }
    } else if (weeks <= 4.3) {
      if (weeks == 1) {
        return "1 week ago";
      } else {
        return weeks + " weeks ago";
      }
    } else if (months <= 12) {
      if (months == 1) {
        return "1 month ago";
      } else {
        return months + " months ago";
      }
    } else {
      if (years == 1) {
        return "1 year ago";
      } else {
        return years + " years ago";
      }
    }
  }

  function getTitle(review) {
    var maxLength = 40; // Maximum length of the review text
    var maxTitleLength = 20; // Maximum length of the title

    // Get the substring of the review text up to maxLength characters
    var substring = review.substring(0, maxLength);

    // Find the position of the last space within the first 15 characters
    var lastSpacePos = substring.lastIndexOf(" ", maxTitleLength);

    var title = "";

    // If a space was found, extract the substring up to that position
    if (lastSpacePos !== -1) {
      title = substring.substring(0, lastSpacePos);
    } else {
      // If no space was found, use the first 15 characters as the title
      title = substring.substring(0, maxTitleLength);
    }

    return title;
  }

  // function viewMore(btns, truncatedTexts) {
  //   for (var i = 0; i < btns.length; i++) {
  //     var btn = btns[i];
  //     var truncatedText = truncatedTexts[i];
  //
  //
  //     if (truncatedText && truncatedText.classList.contains("expanded")) {
  //       // Toggle expanded class only for the corresponding text element
  //       if (truncatedText.classList.contains("expanded")) {
  //         // If text is expanded, truncate it again
  //         truncatedText.classList.remove("expanded");
  //         btn.innerText = "Read More";
  //       } else {
  //         // If text is truncated, expand it
  //         truncatedText.classList.add("expanded");
  //         btn.innerText = "Read Less";
  //       }
  //     }
  //   }
  // }

  // Function to display reviews for the specified page
  function displayReviews(page) {
    const startIndex = parseInt(lastCount * page);
    const endIndex = startIndex + perPage;
    const pageReviews = reviews.slice(startIndex, endIndex);

    // Append reviews to a container on your page
    // For example:
    $.each(pageReviews, function (index, review) {
      let html = "";

      if ($(".allAgents-shortcode").length > 0) {
        html = `<div class="scroll-item " style="color: ${ajax_object.card_text_color}">
        <div class="card border border-0 d-block w-100"  style="width: 400px; min-width: 400px; height: auto user-select: none; background: ${ajax_object.card_bg_color}">
            <div class="card-header bg-transparent border border-0 d-flex justify-content-between align-items-baseline">
                <div class="rating">
                   ${displayRatingIcons(review?.rating)}
                </div>
                <div class="allAgents-review-date "  style="color:${ajax_object.card_text_color}">
                    <?php echo $class->${timeAgo(review?.date_added)}
                </div>
            </div>
            <div class="card-body m-0">
                <div class="header">${getTitle(review?.review)} </div>
                <div class="text truncated-text">
                ${review?.review} 
                </div>
                <a target="_blank" href="https://www.allagents.co.uk/review/${review?.rid}" class="btn btn-link btn-sm view-more-btn p-0" style="font-size: 12px; color:${ajax_object.card_text_color}">Read More</a>
                <div class="date-and-user-info-wrapper mt-2">
                    <div class="name secondary-text">${review?.name} </div>
                     <div class="secondary-text fw-light">${review?.capacityString ?? "No Service Used"}</div>
                </div>
            </div>
        </div>
    </div>`;
      $(".allAgents-shortcode .scroll-container").append(html);
      }

      if ($(".allAgents-widget").length > 0) {
        html = `<div class="scroll-item " style="color: ${ajax_object.widget_text_color}">
            <div class="card d-block w-100"  style="user-select: none; background: ${ajax_object.widget_bg_color}">
                <div class="card-header bg-transparent border border-0 d-flex justify-content-between align-items-baseline">
                    <div class="rating">
               ${displayRatingIcons(review?.rating)}
                    </div>
                    <div class="allAgents-review-date "  style="color:${ajax_object.widget_text_color}">
              ${timeAgo(review?.date_added)}
                    </div>
                </div>
                <div class="card-body m-0">
                    <div class="header">${getTitle(review?.review)}</div>
                    <div class="text truncated-text">${review?.review} 
                    </div>
                           <a target="_blank" href="https://www.allagents.co.uk/review/${review?.rid}" class="btn btn-link btn-sm view-more-btn p-0 " style="font-size: 12px; color:${ajax_object.widget_text_color}">Read More</a>
                    <div class="date-and-user-info-wrapper mt-2">
                        <div class="name secondary-text">${review?.name}</div>
                         <div class="fw-light secondary-text">${review?.capacityString ?? "No Service Used"}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>`;
        $(".allAgents-widget .scroll-container").append(html);
      }
      // Add a click event listener to the button
      $(".scroll-container").on("click", ".view-more-btn", function () {
        // Handle click event here
        // Get the truncated-text element for this review
        var truncatedText = $(".text.truncated-text").eq(index);

        // Get the view-more-btn element for this review
        var btn = $(".view-more-btn").eq(index);

        // Call the viewMore function for this review
        // viewMore(btn, truncatedText);
      });
    });
  }

  function checkAndScrollContainerRight() {
    var container = $(".scroll-container");
    var lastVisibleItem = $(".scroll-item").last();
    var containerWidth = container.width();

    var lastVisibleItemWidth = lastVisibleItem.outerWidth(true);
    var containerScrollLeft = container.scrollLeft();
    var lastVisibleItemOffset =
      lastVisibleItem.offset().left - container.offset().left;

    if (lastVisibleItemOffset + lastVisibleItemWidth > containerWidth) {
      // Calculate the amount to scroll
      // var scrollAmount = lastVisibleItemOffset + lastVisibleItemWidth - containerWidth;
      var scrollAmount = 200; // Adjust scroll amount as needed
      // Adjust the scrollLeft value as needed
      container.animate({ scrollLeft: "+=" + scrollAmount }, 300);
    } else {
      // If at the end, scroll back to the left and fetch more content
      container.animate({ scrollLeft: 0 }, 300);
      // Increment page here
      page++;
      // Display reviews for the next page
      fetchReviews(page);
    }
  }

  var autoScrollInterval = 5000; // Adjust as needed

  // Start auto-scrolling
  var autoScrollTimer = setInterval(function () {
    page++; // Increment page here
    // Display reviews for the next page
    checkAndScrollContainerRight();
    fetchReviews(page);
  }, autoScrollInterval);

  // Stop auto-scrolling when the button or arrow is clicked
  $("#load-more-btn, .scroll-arrow.right").on("click", function () {
    page++; // Increment page here
    // Display reviews for the next page
    checkAndScrollContainerRight();
    fetchReviews(page);
  });
});
