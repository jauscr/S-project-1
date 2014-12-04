$(document).ready(function(){
var bar = $('.upload_bar');
var percent = $('.upload_percent');
var status = $('.upload_status');
var main = $('.upload_main');
var filename = $('.filename');
var main_app = $('.project_img_content');
function isBlank(str) {
    return (!str || /^\s*$/.test(str));
}

$("#fileInput").change(function(){
    $('#valid_msg').hide();
    validation();
});

function hasWhiteSpace(s) {
  return /\s/g.test(s);
}

$('.project_uploaded_img_wrapper').click(function(){
    $('file:input').click();
    console.log('clicked')
});

main.hide();
function validation(){
var filename = $('#fileInput').val().split('\\').pop();
// var types = filename.match(/\.([0-9a-z]+)(?:[\?#]|$)/i)[1];
var types = filename.split('.').pop();
var supported_types = Array('jpg','png','gif','jpeg','iso');
   if(hasWhiteSpace(filename)=== true){
        $('.upload_valid_msg').fadeIn(500).html('Whitespaces in '+ filename +' is not allowed');
        return false
   }
   else { 
       if($.inArray(types, supported_types) > -1){
             fileSizeValidation ();
        }
        else {
            $('.upload_valid_msg').fadeIn(500).html('File type not supported');
            return false
        }    
    }
}

function fileSizeValidation() {
    var file = fileInput.files[0].size / 1024; // kilobytes
    if (file > 1024 ) {
        $('.upload_valid_msg').fadeIn(500).html('Upload file size must not exceed 1MB');
        return false;
    }
    else
    {
        upload_file();
        $('.form1').submit();
        main.show();
     }
}

function upload_file() {
$('.form1').ajaxForm({    
    beforeSend: function(response) {
        var filename_a = $('#fileInput').val().split('\\').pop();
            if(isBlank(filename_a)) {
                alert('please choose a file');
                return false
            }
        $('.upload_progress').show();
        $('.upload_percent').show();
        bar.show();
        main.show();
        status.empty();
        var percentVal = '';
        bar.width(percentVal);
        percent.html(percentVal);
        
        
        },
    uploadProgress: function(event, position, total, percentComplete) {
        var percentVal = percentComplete ;
            bar.width(percentVal)
             var filename_a = $('#fileInput').val().split('\\').pop();
             var filename = filename_a.split('.')[0];
             percent.html("uploading <img src='/soound/web/bundles/sooundapp/css/images/trash.png'/>");  
             main.html(filename_a);
              $('.upload_valid_msg').hide();

        },
    complete: function(xhr) {
        if (xhr.responseText === "File already exists"){
                $('.upload_valid_msg').fadeIn(500).html('File already exists');
                $('.upload_progress').remove();
                main.hide();
                bar.hide();
                percent.hide();
                return false;    
        } else {

        //$('.upload_valid_msg').hide();
        var filename_a = $('#fileInput').val().split('\\').pop();
        var filename = filename_a.split('.')[0];
        var filepath = xhr.responseText;
        $('#step_form_step1 #project_cover').val(filepath);
        bar.width("67%");

        $('.upload_status').hide();
        percent.html("done"); 
        percent.html("done <img src='/soound/web/bundles/sooundapp/css/images/trash.png'/>");
       
        // main_app.append("<div class='generated "+ filename +"'><span>" + filename_a + "</span><img src='http://localhost/soound/web/bundles/sooundapp/css/images/trash.png'/><span>done</span></div>");
        // main.hide();
        // bar.hide();
        // percent.hide();
        // replace uploader AVATAR
        $('.project_uploaded_img_wrapper').css('background','url("/soound/web/bundles/sooundapp/upload/uploads/' + filename_a +'") no-repeat center');        
        $('.project_uploaded_img_wrapper').css('background-size','100%');
        $('.project_uploaded_img_wrapper').css('border','none');

        $('.upload_progress').fadeOut(500);
        status.html(xhr.responseText);
        // post form variables  to be store in DB

        // var var_post = $.ajax({
        //      url: 'http://localhost/soound/web/bundles/sooundapp/upload/vars.php',
        //     data: {var: filename},
        //     type: 'POST',
        //     async: false
        // });

        // $('.'+ filename + ' img').click(function(){ 
        // var file = $(this).siblings('span:first').text();
        $('.upload_percent img').click(function(){ 
        //var file = $(this).siblings('span:first').text();
            var file = $('.upload_main').text();
            if(confirm('Are you sure you wanna delete '+file+'?')){
                var ajax = $.ajax({ url: "/soound/web/bundles/sooundapp/upload/_delete.php",
                data: {file: file},
                type: 'POST',
                async: false
                });
                    if (ajax.responseText == "success") {
                        main.hide();
                        $('.upload_bar').hide();
                        $('.upload_percent').hide();
                        $('.project_uploaded_img_wrapper').css('background','url("/soound/web/bundles/sooundapp/css/images/plus.png") no-repeat center');
                        $('.project_uploaded_img_wrapper').css('border','2px dashed #ebebeb');


                        }
                    }
                    else {
                    }
                });
            } 
        }
    }); 
    }
});     