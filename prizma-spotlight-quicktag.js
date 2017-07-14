initPrizmaSpotlightQuicktag = function(){
  QTags.PrizmaSpotlight = function(){
    QTags.TagButton.call(this, 'prizma-spotlight', 'prizma-spotlight', '', '', '', '', '', {ariaLabel: 'Prizma Spotlight'});
  };
  QTags.PrizmaSpotlight.prototype = new QTags.TagButton();
  QTags.PrizmaSpotlight.prototype.callback = function(e, c, ed, defaultValue){
    if(!defaultValue){
      defaultValue = 'http://';
    }
    var url = prompt("Enter the URL of the video", defaultValue), alt;
    if(url){
      var title = prompt("Enter a title of the video", '');
      title = title ? ' title="' + title + '"' : '';
      this.tagStart = '[prizma-spotlight url="' + url + '"' + title + '][/prizma-spotlight]';
      QTags.TagButton.prototype.callback.call(this, e, c, ed);
    }
  };

// add button after 
  edButtons[71] = new QTags.PrizmaSpotlight();
  QTags._buttonsInit()
}
