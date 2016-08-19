var SERVICE_URL = 'services/';
var listLibraries = [];
var overlayArray = [];

function addLibraryClickEvent(span, id) {
  $(span).click(function(event) {
    listLibraries = [];
    if (!$(span).hasClass('selected')) {
      $(span).find('i').removeClass('fa-plus');
      $(span).find('i').addClass('fa-minus');
      $(span).addClass('selected');
    } else {
      $(span).find('i').removeClass('fa-minus');
      $(span).find('i').addClass('fa-plus');
      $(span).removeClass('selected');
    }
    $('.libraryItem').each(function(index, item) {
      if ($(item).hasClass('selected')) {
        listLibraries.push($(item).data('id'));
      }
    });
  });
}

function getSupportedLibraries() {
  $.ajax({
    method:'GET',
    dataType:'json',
    url: SERVICE_URL + 'listLibraries.php',
    success: function(data) {
      $(data).each(function(index, item) {
        var span = $('<span data-id="' + item.libraryID + '" title="Current version: ' + item.currentVersion + '" class="libraryItem"></span>');
        var i = $('<i class="fa fa-plus" aria-hidden="true"></i></span>');
        $(span).append(i);
        $(span).append(item.name);
        $($('.tabber').find('section')[0]).find('main').append(span);
        addLibraryClickEvent(span, item.libraryID);
      });
    }
  });
}

function setupTabber() {
  $('.tabber header h3').each(function(index, item) {
    $(item).click(function() {
      $('.tabber').find('header h3').removeClass('active');
      $(item).addClass('active');
      $('.tabber').find('section').css('display', 'none');
      $('.tabber').find('#tab-' + $(item).data('tab')).css('display', 'block');
    });
  });
  $($('.tabber header h3')[0]).trigger('click');
}

function requestALibrary() {
  $('#submitForm .formError').html('&nbsp;');
  if (!$('#libName').val().trim().length || !$('#libURL').val().trim().length) {
    $('#submitForm .formError').text('Fill out both fields.');
    return;
  }
  $.ajax({
    method:'POST',
    data: {libName: $('#libName').val(), libURL: $('#libURL').val()},
    url: SERVICE_URL + 'requestLibrary.php',
    statusCode: {
      200: function(data) {
        $('#libName').val('');
        $('#libURL').val('');
        new DictumAlertBox('Request received. We\'ll look into it. No promises, though.');
      }
    }, error: function() {
      new DictumAlertBox('There was an error submitting your request. Please try again later.');
    }
  });
}

function createNewAccount() {
  $('.tabber footer .formError').html('&nbsp;');
  if (!listLibraries.length) {
    $('.tabber footer .formError').text('Choose at least one library to track.');
    return;
  }
  if (!$('#g-recaptcha-response').val().trim().length) {
    $('.tabber footer .formError').text('Verify that you\'re a human.');
    return;
  }
  if (!$('#userEmail').val().trim().length) {
    $('.tabber footer .formError').text('Enter your email address.');
    return;
  }
  $.ajax({
    method:'POST',
    data: {email: $('#userEmail').val(), recaptcha: $('#g-recaptcha-response').val(), libs:listLibraries},
    url: SERVICE_URL + 'createAccount.php',
    dataType: 'json',
    statusCode: {
      200: function(data) {
        grecaptcha.reset();
        var message;
        if (data.needsVerification == true) {
          message = 'You\'ve been signed up to receive updates on your selected libraries, but your email address must first be verified.<br><br>';
          message += 'An email has been sent to ' + $('#userEmail').val() + '. Click on the link, and your account will then be activated.';
        } else {
          message = 'You\'re preferences have been updated.';
        }
        new DictumAlertBox(message);
      }
    }, error: function(response) {
      if (response.status == 400) {
        $('.tabber footer .formError').text('Invalid email address.');
      } else if (response.status == 409) {
        $('.tabber footer .formError').text('We have decided you aren\'t human.');
        $('#emailForm').remove();
        $('#captcha').remove();
        $('#createBtn').remove();
      }
    }
  });
}

$(window).ready(function() {
  setupTabber();
  getSupportedLibraries();
  $('#userEmail').keyup(function(event) {
    if (event.keyCode == 13) {
      createNewAccount();
    }
  });
  $('#createBtn').click(function(event) {
    createNewAccount();
  });
  $('#submitBtn').click(function(event) {
    requestALibrary();
  });
});