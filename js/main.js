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
        $($('.tabber').find('content')[0]).find('main').append(span);
        addLibraryClickEvent(span, item.libraryID);
      });
    }
  });
}

function setupTabber() {
  $('.tabber header h3').each(function(index, item) {
    $(item).click(function() {
      $('.tabber').find('content').css('display', 'none');
      $('.tabber').find('#tab-' + $(item).data('tab')).css('display', 'block');
    });
  });
  $($('.tabber header h3')[0]).trigger('click');
}

function createNewAccount() {
  $('#formError').html('&nbsp;');
  if (!listLibraries.length) {
    $('#formError').text('Choose at least one library to track.');
    return;
  }
  if (!$('#g-recaptcha-response').val().trim().length) {
    $('#formError').text('Verify that you\'re a human.');
    return;
  }
  if (!$('#userEmail').val().trim().length) {
    $('#formError').text('Enter your email address.');
    return;
  }
  console.log('pre');
  $.ajax({
    method:'POST',
    dataType:'json',
    data: {email: $('#userEmail').val(), recaptcha: $('#g-recaptcha-response').val(), libs:listLibraries},
    url: SERVICE_URL + 'createAccount.php',
    statusCode: {
      200: function(data) {
        console.log(data);
        new DictumAlertBox('success');
      }
    }, error: function(response) {
      if (response.status == 400) {
        $('#formError').text('Invalid email address.');
      } else if (response.status == 409) {
        $('#formError').text('We have decided you aren\'t human.');
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
});