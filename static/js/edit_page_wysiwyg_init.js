tinymce.init({
    selector: "textarea.wysiwyg",
    menubar:false,
    statusbar: false,
   plugins: [
        "advlist autolink lists link image anchor code"
    ],
   target_list:false,
    
   toolbar: "undo redo | styleselect | bold italic | bullist numlist | link unlink | image | code",
   content_css : "./static/css/wysiwyg.css"

});
