function addCallback(obj) {
  var callback = obj.callback;
  if (obj.params && obj.params.length) {
    return callback.apply(null, obj.params)
  } else {
    return callback();
  }
}

function DictumAlertBox(content, destroyVars, confirmVars, cancelVars) {
  var box = this;
  this.bg = $('<div></div>').addClass('alertBoxBG');
  this.fg = $('<div></div>').addClass('alertBoxFG');
  //$(fg).append('<header>' + title + '</header>');
  var header = $(this.fg).append('<header><span class="fa fa-lg fa-times-circle-o closer"></span></header>');
  var message = $('<div class="message"></div>');
  $(message).append(content);
  $(this.fg).append(message);
  
  //confirmbox
  if (confirmVars || cancelVars) {
    var buttonBox = $('<div class="buttons"></div>');
    var btn = document.createElement('button');
    $(btn).text(language.genericCommands.cancel);
    $(btn).click(function(event) {
      if (cancelVars && Object.keys(cancelVars).length) {
        addCallback(cancelVars);
      }
      box.destroy();
    });
    $(buttonBox).append(btn);

    btn = document.createElement('button');
    $(btn).text(language.genericCommands.ok);
    $(btn).click(function(event) {
      var success = true;
      if (confirmVars && Object.keys(confirmVars).length) {
        success = addCallback(confirmVars);
      }
      if (success || typeof success == 'undefined') {
        box.destroy();
      }
    });
    $(buttonBox).append(btn);
    $(this.fg).append(buttonBox);
  }
  
  this.updateText = function(text) {
    $(box.fg).find('.message').html(text);
  }
  
  $(header).find('.closer').click(function() {
    if (destroyVars && Object.keys(destroyVars).length) {
      box.destroy(destroyVars);
    } else {
      box.destroy();
    }
  });

  $(this.bg).click(function(event) {
    if (destroyVars && Object.keys(destroyVars).length) {
      box.destroy(destroyVars);
    } else {
      box.destroy();
    }
  });
  $(document.body).append(this.bg);
  $(document.body).append(this.fg);
  overlayArray.push(this);
  
  setTimeout(function() {
    $(box.fg).fadeTo('fast', 1);
  }, 50);
  
  this.destroy = function(callbackVars) {
    var box = this;
    $(box.fg).remove();
    $(box.bg).fadeTo('fast', 0, function() {
      $(box.bg).remove();
      overlayArray.splice(overlayArray.indexOf(box), 1);
      if (callbackVars && Object.keys(callbackVars).length) {
        addCallback(callbackVars);
      }
      box = null;
    });
  }
}