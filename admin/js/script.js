console.log("connected");

$(document).ready(function () {
  $("#summernote").summernote({
    height: 200,
  });
});

$(document).ready(function () {
  // to check all the check-boxes at once
  $("#selectAllBoxes").click(function () {
    if (this.checked) {
      $(".checkBoxes").each(function () {
        this.checked = true;
      });
    } else {
      $(".checkBoxes").each(function () {
        this.checked = false;
      });
    }
  });

  // loader
  var div_box = "<div id='load-screen'><div id='loading'></div></div>";
  $("body").prepend(div_box);

  $("#load-screen")
    .delay(700)
    .fadeOut(600, function () {
      $(this).remove();
    });
});

// to get online users count using javascript AJAX call
// don't have to refresh the page with javascript
function loadUserOnline() {
  // making AJAX call
  $.get("functions.php?onlineusers=result", function (data) {
    $(".usersonline").text(data);
  });
}

// calling function to retrieve user count function regularly
setInterval(function () {
  loadUserOnline();
}, 500);
