(function($){
    $(window).on("load", function(){
        var mediaUploader;
        var menu_images = $('.image-preview-wrapper');
        
        // MEDIA LIBRARY JS
        menu_images.each(function(index, value){
            var image       = $(this).children('.image-preview');
            var url         = image.attr('src');
            var checkbox    = $(this).prev().children( $('.field-check-image') );
            
            if(url){
               var url_input   = $(this).find('.image-url');
               url_input.val(url);
               $(this).children('.delete-image').fadeIn(300);
               checkbox.prop("checked", true);
               $(this).css("display", 'block');
            }
        });

        $('.upload-button').click(function(e) {
          var _this = $(this);
          e.preventDefault();
          // If the uploader object has already been created, reopen the dialog
            if (mediaUploader) {
            mediaUploader.open();
            return;
          }
          // Extend the wp.media object
          mediaUploader = wp.media.frames.file_frame = wp.media({
            title: 'Choose Image',
            button: {
            text: 'Choose Image'
          }, multiple: false });

          // When a file is selected, grab the URL and set it as the text field's value
          mediaUploader.on('select', function() {
            var attachment = mediaUploader.state().get('selection').first().toJSON();
            _this.siblings('.image-preview').attr('src', attachment.url);
            _this.prev( $('.image-url') ).val(attachment.url);
            _this.next( $('.delete-image') ).fadeIn(300);
          });
          // Open the uploader dialog
          mediaUploader.open();
        });
        // END MEDIA LIBRARY JS
        
        $('.delete-image').click(function(){
           var _this = $(this);
           _this.siblings('.image-preview').attr("src", '');
           _this.siblings('.image-url').val('');
           _this.siblings('.image-dimensions').children( $('.image-width').val('') );
           _this.siblings('.image-dimensions').children( $('.image-height').val('') );
           $(this).fadeOut(300);
        });
        
        $('.field-check-image').on('change', function(){
            if(this.checked){
                $(this).closest( 'label').next('.image-preview-wrapper').fadeIn(300) ;
            } else {
                $(this).closest( 'label').next('.image-preview-wrapper').fadeOut(300) ;
            }
        });
        
    });
  
})(jQuery);

