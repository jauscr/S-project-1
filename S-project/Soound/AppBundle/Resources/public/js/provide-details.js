/**
 * User: Moved for Jauscr
 * Date: 7/29/13
 */
var _languages = [
    'Wu', 'Gan', 'Jin', 'Zulu', 'Akan', 'Igbo', 'Fula', 'Urdu', 'Thai', 'Chewa', 'Greek', 'Khmer', 'Hindi', 'Dutch', 'Mossi', 'Shona', 'Hmong', 'Uzbek', 'Hakka', 'Tamil', 'Czech', 'Oriya', 'Hausa', 'Xiang', 'Oromo', 'Xhosa', 'Uyghur', 'Awadhi', 'Pashto', 'Yoruba', 'French', 'Sindhi', 'Korean', 'Arabic', 'Polish', 'Telugu', 'Nepali', 'Zhuang', 'Somali', 'Magahi', 'German', 'Deccan', 'Kazakh', 'Italian', 'Amharic', 'Konkani', 'Spanish', 'Balochi', 'Turkish', 'Min Nan', 'Persian', 'Cebuano', 'Bengali', 'Kurdish', 'Swedish', 'Saraiki', 'Burmese', 'Kirundi', 'Quechua', 'Marathi', 'English', 'Marwari', 'Tagalog', 'Ilokano', 'Deutsch', 'Russian', 'Swahili', 'Punjabi', 'Kannada', 'Min Bei', 'Sylheti', 'Gujarati', 'Maithili', 'Min Dong', 'Malagasy', 'Javanese', 'Assamese', 'Madurese', 'Mandarin', 'Bhojpuri', 'Romanian', 'Japanese', 'Haryanvi', 'Sinhalese', 'Sundanese', 'Hungarian', 'Cantonese', 'Malayalam', 'Ukrainian', 'Dhundhari', 'Portuguese', 'Belarusian', 'Hiligaynon', 'Vietnamese', 'Kinyarwanda', 'Azerbaijani', 'Chittagonian', 'Chhattisgarhi', 'Serbo-Croatian', 'Haitian Creole', 'Indonesian (Malay)'
];
function countChar(val) {
    var len = val.val().length;
    var charUsed = $("#characterUsed")
    if (len <= 100) {
        charUsed.css({'opacity': (120-len)/100, 'color': '#e74c3c'});
    } else {
        charUsed.css({'opacity': 1, 'color': '#2ecc71'});
    }
    charUsed.html(len)
};

function createReference(pos){
    pos = pos +1;
    var newLink='<div class="highlight-input input-provide-details">'+
        '<input type="text" name="project_reference['+pos+'][link]" id="link_preference_'+pos+'" class="link_preference" placeholder="i.e. http://myLinkReference"  onfocus="this.placeholder = \'\'" onblur="this.placeholder = \'i.e. http://myLinkReference\'"/>'+
        '<div class="highlight-input_deco highlight-reference" style="float:left">Enter a SoundCloud or YouTube link or upload a reference</div>'+
        '<div class="project_input_img gray-button upload_reference" style="width:35px !important;">'+
            '<img class="upload_logo" src="/bundles/sooundapp/css/images/upload_img.png">'+
        '</div>'+
        '<input type="file" class="reference_file"  name="project_reference['+pos+']" style="visibility:hidden"/>'+
    '</div>';

    var newDesc='<div id="ref_description_'+pos+'" style="display:none" class="desc_container">'+
                    '<div class="link-box-arrow"></div>'+
                    '<div class="link-box">'+
                        '<div class="link-description">'+
                            '<textarea onblur="this.placeholder = \'Point out the things you like in this reference that could help create your sound\'" onfocus="this.placeholder = \'\'" placeholder="Point out the things you like in this reference that could help create your sound" rows="5" class="reference_text" id="reference_text_'+pos+'" name="project_reference['+pos+'][desc]"></textarea>'+
                        '</div>'+
                    '</div>'+
                '</div>';
    var separator='<div class="separator" style="margin: 10px 0 10px 0;"></div>';
    var html = separator+newLink+newDesc;
    return html;
}

function createProjectFile(pos){
    pos = pos +1;
    var newLink='<div class="highlight-input input-provide-details">'+
        '<input type="text" name="project_files['+pos+'][link]" id="link_files_'+pos+'" class="link_project_file" placeholder="i.e. http://myLinkProjectFile"  onfocus="this.placeholder = \'\'" onblur="this.placeholder = \'i.e. http://myLinkProjectFile\'"/>'+
        '<div class="highlight-input_deco highlight-reference" style="float:left">Upload a file you\'d like to be used in the production of your project</div>'+
        '<div class="project_input_img gray-button upload_project_file  project-file-clickable'+pos+'" id="project-files-dropzone'+pos+'" style="width:35px !important;">'+
            '<img class="upload_logo project-file-clickable'+pos+'" src="/bundles/sooundapp/css/images/upload_img.png">'+
        '</div>'+
        '<input type="hidden" id="files_hasfile'+pos+'" name="project_files['+pos+'][hasFile]" value="0">'+
    '</div>';

    var newDesc='<div id="file_description_'+pos+'" style="display:none" class="desc_container">'+
                    '<div class="link-box-arrow"></div>'+
                    '<div class="link-box">'+
                        '<div class="link-description">'+
                            '<textarea onblur="this.placeholder = \'Point out the things you like in this reference that could help create your sound\'" onfocus="this.placeholder = \'\'" placeholder="Point out the things you like in this reference that could help create your sound" rows="5" class="file_text" id="file_text_'+pos+'" name="project_files['+pos+'][desc]"></textarea>'+
                        '</div>'+
                    '</div>'+
                '</div>';
    var separator='<div class="separator" style="margin: 10px 0 10px 0;"></div>';
    var html = separator+newLink+newDesc;
    return html;
}

function verifyCombo(val,combo){
    $('.'+combo+' ul li').removeAttr('class');
    $('.'+combo+' ul li').each(function(){
        if($(this).text() == val){
            $(this).attr('class','selected');
        }
    });
}

function showDesc($this, type){
    var pos = extractNumber($this.attr("id"));
    var fileInput = $this.next().next().next().val();
    if(type == 'file'){
        if(checkURL($this.val()))
            $("#"+type+"_description_"+pos).slideDown();
        else
            $("#"+type+"_description_"+pos).slideUp();            
    }
    else{
        if(checkURL($this.val()) || fileInput != "")
            $("#"+type+"_description_"+pos).slideDown();
        else
            $("#"+type+"_description_"+pos).slideUp();    
    }
}

function projectFileZone(pos){
    var projectFilesZone = new Dropzone("#project-files-dropzone"+pos, {

        url: "uploadProjectFile",
        maxFilesize: 5, //MB
        uploadMultiple: false,
        clickable: '.project-file-clickable'+pos,
        init: function(){
            this.on("addedfile", function(file) { 
                $("#link_files_"+pos).val(file.name).attr("readonly","true");
                $("#file_description_"+pos).slideDown();
                $("#files_hasfile"+pos).val("1");
            });
            this.on("error", function(file, message){
            });
            this.on("success", function(file, data){
                //$('#upload-progress-status').text("completed");
                var response = $.parseJSON(data);
                console.log(response);
                if(response.msg === "ok"){
                }
                
            });
        }
    });
}

$(function(){

    $("#form_vocalLan").tagit({
        availableTags: _languages,
        allowSpaces: true,
        autocomplete: {delay: 0, minLength: 2},
        placeholderText: 'i.e. English, Spanish',
        beforeTagAdded: function(event, ui) {
            if($.inArray(ui.tagLabel, _languages)==-1) return false;
        }
    });

    tagsDeco();

    projectFileZone(1);
    $(".references").on("click", ".upload_reference", function(){
        $(this).next().trigger("click");
    });
    $(".link_preference").each(function(index){
        showDesc($(this),'ref');
    });

    $(".references").on("keyup", ".link_preference" ,function(){
        showDesc($(this), 'ref');
    });

    $(".references").on("change", ".reference_file", function(){
        var linkInput = $(this).prev().prev().prev();
        var fileName = $(this).val();
        fileName=fileName.substr(fileName.lastIndexOf("\\")+1,fileName.length);
        linkInput.val(fileName).attr("readonly","true");
        showDesc(linkInput,'ref');
    })

    $(".link_project_file").each(function(index){
        showDesc($(this),'file');
    });

    $(".project_files").on("keyup", ".link_project_file" ,function(){
        showDesc($(this), 'file');
    });

    $(".section_title").on("click", function(){
        var $this = $(this);
        if(!$this.data("open")){
            $this.data("open",true).find("span").text("-");
            $("."+$this.attr("id")).slideDown();
        }
        else{
            $this.data("open",false).find("span").text("+");
            $("."+$this.attr("id")).slideUp();
        }
        return false;
    });
    $('#form_projectdetails').keyup(function(){
        countChar($(this));
    });
    $("#form_dominantsound").tagit({
        tagLimit: 2,
        placeholderText: 'i.e. Piano, Jaz guitar'
    });

    var aWeights = new Array('', 'none', 'ballad', 'mid-tempo', 'up-tempo', 'none');
    $('#tempo').slider({
        value:1,
        min: 1,
        max: 5,
        step: 1,
        slide: function(event, ui) {
            var sFontWeight = aWeights[ui.value];
            $('#form_projecttempo').val(sFontWeight);
            $('ul.tempo-option li').each(function(){
                if('op'+ui.value == $(this).attr('class')){
                    $(this).css({'color':'#e74c3c !important'});
                }else{
                    $(this).css({'color':'#bec3c7'});
                }
            });
        }
    });

    //Verify tempo
    switch($('#form_projecttempo').val())
    {
        case "ballad":
            $('.ui-slider-handle.ui-state-default.ui-corner-all').removeAttr('style');
            $('.ui-slider-handle.ui-state-default.ui-corner-all').attr('style','left:25%');
            $('ul.tempo-option .op2').css({'color':'#e74c3c !important'});
            break;
        case "mid-tempo":
            $('.ui-slider-handle.ui-state-default.ui-corner-all').removeAttr('style');
            $('.ui-slider-handle.ui-state-default.ui-corner-all').attr('style','left:50%');
            $('ul.tempo-option .op3').css({'color':'#e74c3c !important'});
            break;
        case "up-tempo":
            $('.ui-slider-handle.ui-state-default.ui-corner-all').removeAttr('style');
            $('.ui-slider-handle.ui-state-default.ui-corner-all').attr('style','left:75%');
            $('ul.tempo-option .op4').css({'color':'#e74c3c !important'});
            break;
    }


    $('.project_submit').click(function(){
        clearAlert();
        var error=0;

        if (!isEmpty('#form_projectdetails')){
            $('#form_projectdetails').attr('style','border: solid #E74C3C !important');

            $('#form_projectdetails').focus(function(){ $(this).removeAttr('style')});
            $('#form_projectdetails').next().text('Please write more details about project');
            //showAlert('Please write more details about project',2,'inline');
            error++;
        }
        else if ($('#form_projectdetails').val().length <=100){
            $('#form_projectdetails').attr('style','border: solid #E74C3C !important');

            $('#form_projectdetails').focus(function(){
                $(this).removeAttr('style')
            });
            $('#form_projectdetails').next().text('The description must be at least 100 characters');
            //showAlert('The description must be at least 100 characters',2,'inline');
            error++;

        }
        
        if(error>0){
            $('#form_projectdetails').focus();
            return false;
        }
    });
    $('html').click(function() {
        $('.project_properties_1').removeClass('active');
        $('.project_properties_2').removeClass('active');
        $('.project_properties_3').removeClass('active');
        $('.project_properties_4').removeClass('active');
        $('.project_properties_5').removeClass('active');
    });

    $('.project_properties_1, .project_properties_2, .project_properties_3, .project_properties_4, .project_properties_5').click(function(event){
        event.stopPropagation();
    });

    // Create Reference
    $('.project_reference_content').click(function(){
        var flag = 0;
        $('.reference_text, .link_preference').each(function(){
            if (!isEmpty(this)){
                $(this).attr('placeholder','Complete this field to add one or more references.');
                $(this).attr('style','border: solid #E74C3C !important');
                flag++;
            }else{
                $(this).removeAttr('style');
            }
        });

        if(flag ==0){
            $( ".project_reference_content.gray-button" ).prev().before( createReference(extractNumber($('.project_reference_content').prev().prev().find('textarea').attr('id'))) );
        }

        //clean red border field
        $('.reference_text, .link_preference').each(function(){
            $(this).focus(function(){
                $(this).removeAttr('style');
            })
        });
    });

    // Create Project File
    $('.project_file_content').click(function(){
        var flag = 0;
        $('.file_text, .link_project_file').each(function(){
            if (!isEmpty(this)){
                $(this).attr('placeholder','Complete this field to add one or more project files');
                $(this).attr('style','border: solid #E74C3C !important');
                flag++;
            }else{
                $(this).removeAttr('style');
            }
        });

        if(flag == 0){
            var pos = extractNumber($('.project_file_content').prev().prev().find('textarea').attr('id'));
            $( ".project_file_content.gray-button" ).prev().before( createProjectFile(pos) );
            projectFileZone(pos+1);
        }

        //clean red border field
        $('.file_text, .link_project_file').each(function(){
            $(this).focus(function(){
                $(this).removeAttr('style');
            })
        });
    });

    $('.project_properties_1 li').hover( function() {
        $(this).animate({ color: "#000" });
    },function() {
        $(this).animate({ color: "#787878" });
    });

    $('.project_properties_2 li').hover( function() {
        $(this).animate({ color: "#000" });
    },function() {
        $(this).animate({ color: "#787878" });
    });

    $('.project_properties_3 li').hover( function() {
        $(this).animate({ color: "#000" });
    },function() {
        $(this).animate({ color: "#787878" });
    });

    $('.project_properties_4 li').hover( function() {
        $(this).animate({ color: "#000" });
    },function() {
        $(this).animate({ color: "#787878" });
    });
    $('.project_properties_5 li').hover( function() {
        $(this).animate({ color: "#000" });
    },function() {
        $(this).animate({ color: "#787878" });
    });

    tagsDeco();

    uniqueCheckboxes("custom-checkbox_input");

});