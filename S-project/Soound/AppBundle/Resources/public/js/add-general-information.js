function projectTypeDesc(){
    var message="";
    $('.custom-radio input[type="radio"]').click(function(){
        var helper = $('.project_type .highlight-input_deco');
        helper.css({
            'opacity': '0'
        });
        switch($(this).val())
        {
            case "CompleteSongs":
                message="Get the perfect song created for your next EP, TV or movie scene";
                break;
            case "Production":
                message="Get music produced for that song in your head";
                break;
            case "Songwriting":
                message="You have music, now get the perfect song written for it";
                break;
            case "Musician":
                message="Get live drums, a killer guitar solo, or rare tribal elements added to an existing song";
                break;
            case "Vocal":
                message="Audition the right voice for your song, voice over, or advertising need";
                break;
            case "Engineering":
                message="Take your sound to the next level with the perfect mix or mastering";
                break;
        }
       setTimeout( function() {
        helper.css({
            'filter': 'none',
            'max-height': '60px',
            'opacity': '1',
            'visibility': 'visible',
            'display': 'block'
        }).text(message);
       }, 200);
    })
}

var _temp="";
$(document).ready(function(e) {

    var _genres = [
            'Bop', 'CCM', 'Dub', 'EDM', 'Emo', 'IDM', 'Pop', 'R&B', 'Rap', 'Ska', 'Acid', 'Data', 'Enka', 'Folk', 'Funk', 'Jazz', 'Punk', 'Rock', 'Soul', 'Surf', 'Trap', 'Anime', 'Asian', 'Blues', 'Cajun', 'Chant', 'Crunk', 'Dance', 'Disco', 'Drone', 'Games', 'Gypsy', 'House', 'Indie', 'J-Pop', 'J-Ska', 'Japan', 'K-Pop', 'Latin', 'Lo-Fi', 'Metal', 'Noise', 'Opera', 'Piano', 'Polka', 'Ragga', 'Salsa', 'Samba', 'Swing', 'Vocal', 'World', 'Bounce', 'Celtic', 'Choral', 'Comedy', 'Disney', 'Easter', 'Europe', 'France', 'French', 'Fusion', 'Gabber', 'Garage', 'German', 'Gospel', 'Grunge', 'Hi-NRG', 'J-Punk', 'J-Rock', 'Jungle', 'Lounge', 'Motown', 'Nature', 'Poetry', 'Praise', 'Raíces', 'Reggae', 'Techno', 'Tejano', 'Thrash', 'Trance', 'Travel', 'Zydeco', 'African', 'Ambient', 'Baroque', 'Chicano', 'Country', 'Doo Wop', 'Dubstep', 'Electro', 'Erotica', 'Gangsta', 'Grinder', 'Healing', 'Hip Hop', 'Holiday', 'J-Synth', 'Karaoke', 'Klezmer', 'Latino ', 'Mexican', 'Minimal', 'New Age', 'New Mex', 'Norteno', 'Novelty', 'Oceania', 'Qawwali', 'Ragtime', 'Spanish', 'Stories', 'Strings', 'Tex-Mex', '3rd Wave', 'Afro-Pop', 'Art Rock', 'Art-Folk', 'Ballroom', 'Big Band', 'Big Beat', 'Brit Pop', 'Broadway', 'Chanukah', 'Conjunto', 'Darkwave', 'Exercise', 'Flamenco', 'Hard Bop', 'Hardcore', 'Hawaiian', 'Illbient', 'Medieval', 'Merengue', 'Musicals', 'Neo Soul', 'New Wave', 'Politics', 'Pop Punk', 'Pop Rock', 'Ranchero', 'Romantic', 'Shoegaze', 'Teen Pop', 'Trip Hop', 'Tropical', 'Acid Jazz', 'Afro-Beat', 'Americana', 'Australia', 'Blue Note', 'Bluegrass', 'Breakbeat', 'Caribbean', 'Christmas', 'Classical', 'Dancehall', 'Didjeridu', 'Dixieland', 'Downtempo', 'Dream Pop', 'Ensembles', 'Eurodance', 'Folk Rock', 'Funk Rock', 'Glam Rock', 'Goth Rock', 'Halloween', 'Hard Rock', 'Indie Pop', 'Jam Bands', 'Kayokyoku', 'Latin Pop', 'Latin Rap', 'Lullabies', 'Portugese', 'Post Punk', 'Power Pop', 'Prog Rock', 'Québécois', 'Queercore', 'Rap Metal', 'Reggaeton', 'Religious', 'Soft Rock', 'Standards', 'Steampunk', 'Surf Rock', 'Trad Jazz', 'Vocal Pop', 'Worldbeat', 'Acid House', 'Adult Film', 'Afro-Cuban', 'Arena Rock', 'Audio Book', 'Avant Jazz', 'Avant Rock', 'Barbershop', 'Blues Rock', 'Bossa Nova', 'Brazillian', 'Club Dance', 'Deep House', 'Electronic', 'Ethio-jazz', 'Fairytales', 'Film Score', 'French Pop', 'German Pop', 'Hair Metal', 'Hard Dance', 'Honky Tonk', 'Horrorcore', 'Indian Pop', 'Indie Rock', 'Industrial', 'Latin Rock', 'Love Songs', 'Meditation', 'Minimalism', 'Mood Music', 'Orchestral', 'Pop Latino', 'Pop Vocals', 'Rave Music', 'Relaxation', 'Riot Grrrl', 'Rockabilly', 'Roots Rock', 'Sing-Along', 'Soundtrack', 'Vocal Jazz', 'Alternative', 'Avant-Garde', 'Black Metal', 'Celtic Folk', 'Dark Techno', 'Death Metal', 'Delta Blues', 'Dirty South', 'Drum & Bass', 'Early Music', 'Electronica', 'Foreign Rap', 'Gangsta Rap', 'General Pop', 'General R&B', 'General Rap', 'German Folk', 'Happy House', 'Hard Trance', 'Heavy Metal', 'Jazz Vocals', 'Latin Jazz ', 'Middle East', 'Progressive', 'Psychedelic', 'Quiet Storm', 'Renaissance', 'Rock & Roll', 'Rock Steady', 'Ska Revival', 'Smooth Jazz', 'Speed Metal', 'Spoken Word', 'Tech Trance', 'Turntablism', 'Acoustic Pop', 'Applications', 'Bachelor Pad', 'Bass Assault', 'Classic Rock', 'College Rock', 'Contemporary', 'European Pop', 'Experimental', 'General Data', 'General Folk', 'General Jazz', 'General Punk', 'General Rock', 'Gothic Metal', 'Hardcore Rap', 'Instrumental', 'Irish Celtic', 'Jackin House', 'Japanese Pop', 'New Acoustic', 'Roots Reggae', 'Scandinavian', 'South Africa', 'Southern Rap', 'Thanksgiving', 'Tribal House', 'World Fusion', 'Central Asian', 'Chamber Music', 'Chicago Blues', 'Christian Pop', 'Christian Rap', 'Classic Blues', 'Country Blues', 'Freestyle Rap', 'General Blues', 'General Books', 'General Dance', 'General House', 'General Latin', 'General Metal', 'General World', 'Hardcore Punk', 'Impressionist', 'Japanese Enka', 'Japanese Folk', 'Japanese Jazz', 'Japanese Rock', 'Marching Band', 'North America', 'Short Stories', 'South America', 'Southern Rock', 'Swing Revival', 'TV Soundtrack', 'Wedding Music', 'Acoustic Blues', 'Ambient Trance', 'Big Band Swing', 'Christian Rock', 'Christmas: Pop', 'Christmas: R&B', 'Country Gospel', 'Crossover Jazz', 'Detroit Techno', 'Drinking Songs', 'East Coast Rap', 'Easy Listening', 'Electric Blues', 'Foreign Cinema', 'General Celtic', 'General Reggae', 'General Spoken', 'General Techno', 'General Trance', 'Hardcore Metal', 'High Classical', 'Japanese Blues', 'Jewish/Israeli', 'Old School Rap', 'Original Score', 'Outlaw Country', 'Standup Comedy', 'Unclassifiable', 'West Coast Rap', 'Alternative Rap', 'Ambient New Age', 'Christian Metal', 'Christmas: Jazz', 'Christmas: Rock', 'Film Soundtrack', 'General Country', 'General Hip Hop', 'General Holiday', 'General New Age', 'Hardcore Techno', 'Japanese Fusion', 'Mainstream Jazz', 'Native American', 'Old School Punk', 'Renaissance Era', 'Southern Gospel', 'Traditional Pop', 'Underground Rap', 'Urban Crossover', 'West Coast Jazz', 'Alternative Folk', 'Alternative Punk', 'Alternative Rock', 'Avant-Garde Jazz', 'British Invasion', 'Children’s Music', 'Classical Guitar', 'Classical Indian', 'Contemporary R&B', 'Eastern European', 'Electronic Rock ', 'Industrial Dance', 'Japanese Karaoke', 'Meditation Music', 'New Orleans Jazz', 'Progressive Rock', 'Psychedelic Rock', 'Television Score', 'Traditional Folk', 'Western European', 'Adult Alternative', 'Alternative Metal', 'Baladas y Boleros', 'Christmas: Modern', 'Classic Christian', 'Contemporary Folk', 'Contemporary Jazz', 'Experimental Rock', 'General Christian', 'General Classical', 'General Religious', 'Instrumental Rock', 'Operating Systems', 'Progressive House', 'Progressive Metal', 'Regional Mexicano', 'Singer-Songwriter', 'Adult Contemporary', 'American Trad Rock', 'Christmas: Classic', 'Contemporary Blues', 'Contemporary Latin', 'General Electronic', 'General Industrial', 'General Soundtrack', 'Japanese Classical', 'Modern Composition', 'Old School Hip Hop', 'Straight Edge Punk', 'Traditional Celtic', 'Traditional Gospel', 'Alternative Country', 'Ambient Electronica', 'Classical Crossover', 'Contemporary Celtic', 'Contemporary Gospel', 'Environmental Music', 'General Alternative', 'Hardcore Industrial', 'Indian Subcontinent', 'Traditional Country', 'Christmas: Classical', 'Christmas: Religious', 'Conjunto Progressive', 'Contemporary Country', 'Aboriginal Australian', 'Christmas: Children’s', 'Old School Industrial', 'Television Soundtrack', 'Traditional Bluegrass', 'Contemporary Bluegrass', 'General Easy Listening', 'General Unclassifiable', 'South/Central American', 'Japanese Unclassifiable', 'Minimalist Experimental', 'General Children’s Music', 'Japanese General Soundtrack', 'Japanese Traditional (Minzoku)', 'Japanese Children’s Song (Doyo)'
        ];
    //If textfield click remove red border
    $('input[type="text"]').focus(function(){ $(this).removeAttr('style')});
    // step2 validation
    $('.project_submit').click(function(){
        clearAlert();

        var error=0;
        $('.ui-widget-content.ui-autocomplete-input').click(function(){
            $('.tagit').removeAttr('style');
        });
        var radio_buttons = $("input[type='radio']:checked").val();
        var input_to_check = $('.project_type');

        if (!isEmpty('#form_projectname')){
            $('#form_projectname').attr('style','border: solid #E74C3C !important');

            showAlert('Please write the project title <br />',1);
            error++;
        }

        if(!$('.project_type input[type=radio]:checked').val()){
            showAlert('Please select what type of project is ... <br />',3);
            error++;
        }

        if (!isEmpty('.step_three_input input')){
            $('ul.tagit.ui-widget-content').attr('style','border-color:red !important');

            showAlert('Please write what type of Sound/Genre should it be <br />',4);
            error++;
        }

        if(error>0){
            return false;
        }
    });

    $('.project_uploaded_img_wrapper').click(function(){
        /* event.preventDefault(); */ /* Event is not defined */
        $('#fileInput').click();
    });

    var projectPicZone = new Dropzone("#userfile-dropzone", {

        url: "new/upload",
        previewsContainer: '#project-upload-preview',
        maxFilesize: 5, //MB
        acceptedFiles: ".jpg,.jpeg,.png,.gif",
        clickable: '.dz-clickable',
        init: function(){
            this.on("error", function(file, message){ 
                if( message === "You can't upload files of this type."){
                    console.log(message);
                }
                else
                    $('#userfile-errors').text(message);
            });
            this.on("sending", function(file){
                //console.log("sending file");
            });
            this.on("uploadprogress", function(file, progress){
                //progress = Math.floor(progress);
            });
            this.on("success", function(file, data){
                //$('#upload-progress-status').text("completed");
                var response = $.parseJSON(data);
                if(response.msg === "ok"){
                    $("#project-picture-thumb").empty().attr("style","background: url("+response.picture+") no-repeat; border:none");
/*                    
                    $('#project-picture-thumb').empty().css({
                        'background': 'url(/uploads/projectPics/'+response.picture+') no-repeat',
                        'border': 'none'
                    });
*/                    
                }
                
            });
        }
    });

    $('#userfile-inner-cont').css('cursor', 'pointer');

    $('.form1').css('height','200px');
    $('ul.tagit .tagit-choice.ui-widget-content.ui-state-default.ui-corner-all.tagit-choice-editable').hover(function(){
        $('ul.tagit .ui-state-default .ui-icon').css({'background-image':'url(../images/ui-icons_256x240_red.png) !important;'});
    },
    function(){
        $('ul.tagit .ui-state-default .ui-icon').css({'background-image':'url(../images/ui-icons_256x240_black.png) !important;'});
    });

    $("#form_projectgenre").tagit({
        availableTags: _genres,
        allowSpaces: true,
        autocomplete: {delay: 0, minLength: 2},
        placeholder: 'i.e. Rock, Pop',
        beforeTagAdded: function(event, ui) {
            if($.inArray(ui.tagLabel, _genres)==-1) return false;
        }
    });

    tagsDeco();

    projectTypeDesc();
});
