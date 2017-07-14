(function(tinymce){
  tinymce.PluginManager.add('prizmaspotlight', function(editor){
    var toolbar;

    var html = '<div class="prizma-spotlight-container" >' +
      '<label><span>Video URL</span><input type="text" id="prizma-spotlight-video-url" name="prizma-spotlight-video-url"></label>' +
      '<label><span>Video Title</span><input type="text" id="prizma-spotlight-video-title" name="prizma-spotlight-video-title"></label>' +
      '</div>';


    var close = function(win){
      jQuery('#prizma-spotlight-video-url').removeClass("prizma-spotlight-error")
      jQuery('#prizma-spotlight-video-url').val('');
      jQuery('#prizma-spotlight-video-title').val('');
      win.close();
    }

    var add = function(win){
      var url = jQuery('#prizma-spotlight-video-url').val();
      if(!url){
        jQuery('#prizma-spotlight-video-url').addClass("prizma-spotlight-error");
        return false;
      }

      var title = jQuery('#prizma-spotlight-video-title').val();
      title = title ? ' title="' + title + '"' : '';
      var command = '[prizma-spotlight url="' + url + '"' + title + '][/prizma-spotlight]';

      editor.execCommand('mceInsertContent', false, command);
      close(win);
    }


    editor.addCommand('Prizma_Spotlight', function(){

      win = editor.windowManager.open({
        title: "Add Prizma Spotlight",
        spacing: 10,
        padding: 10,
        items: [
          {
            type: 'container',
            direction: 'column',
            align: 'center',
            minHeight: "85",
            html: html
          }
        ],
        onkeypress: function(e){
          if(13 === e.keyCode){
            add(win);
          }
        },
        buttons: [
          {
            text: "Cancel",
            left: 10,
            onclick: function(){
              close(win);
            }
          },
          {
            classes: 'widget btn primary first abs-layout-item',
            text: "Add",
            onclick: function(){
              add(win);
            }
          }
        ]
      });
    });

    editor.addButton('wp_prizma_spotlight_add', {
      tooltip: 'Add Prizma Spotlight ', // trailing space is needed, used for context
      icon: "icon prizma-spotlight-tinymce-icon",
      cmd: 'Prizma_Spotlight'
    });

  });
})(window.tinymce);
