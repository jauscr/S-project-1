<?php

namespace Soound\AppBundle\Controller;

use Doctrine\Common\Util\Debug;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Soound\AppBundle\Document\Project;
use Soound\AppBundle\Document\ProjectReference;
use Soound\AppBundle\Document\ProjectFile;
use Soound\AppBundle\Document\User;
use Soound\AppBundle\Document\CreditCard;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Util\SecureRandom;
use Symfony\Component\Form\FormError;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class ProjectsController extends Controller
{
	public function addGeneralInformationAction(Request $request) {
        $generator = new SecureRandom();
        $random = $generator->nextBytes(10);

        $generalInfo = new Project();
        $form = $this->createFormBuilder($generalInfo, array('validation_groups' => array('step2')))

            ->add('projectname', 'text',array(
                'attr' => array(
                    'id' => 'project_name',
                    'placeholder'=> "Project Title",
                    "onfocus" => "this.placeholder = ''",
                    "onblur" => "this.placeholder = 'Project Title'"
                )
            ))
            ->add('projectchecktype', 'choice', array(
                'choices' => array(
                    'CompleteSongs' => 'Complete Song',
                    'Production' => 'Production',
                    'Songwriting' => 'Songwriting',
                    'Musician' => 'Musician',
                    'Vocal' => 'Vocal',
                    'Engineering' => 'Engineering'
                ),
                'required'  => true,
                'multiple' => false,
                'expanded' => true
            ))
            ->add('projectgenre', 'text',array(
                'attr' => array(
                    'placeholder'=> "i.e. Rock, Pop",
                    "onfocus" => "this.placeholder = ''",
                    "onblur" => "this.placeholder = 'i.e. Rock, Pop'"
                )
            ))
            ->getForm();

        if ($request->getMethod() == 'POST') {
            $form->bind($request);
            if ($form->isValid()) {

                $this->updateDraft($request, 2);
                $session = $this->getRequest()->getSession();
                $fullProjectData =  $session->get('project_draft');

                return $this->redirect(
				    $this->generateUrl('projects_provideDetails')
			    );
            }
            else{
                return $this->render(
                    "SooundAppBundle:Projects:add-general-information.html.twig",array(
                    'form' => $form->createView(),
                    'isNew' => true,
                    'editId' => $random
                ));
            }
		}

        #create a safe random number from our seed
        mt_srand((int)$random);

		return $this->render(
			"SooundAppBundle:Projects:add-general-information.html.twig", array(
            'form' => $form->createView(),
            'isNew' => true,
            'editId' => mt_rand(0,100000),
            'existingFiles' => array()
        ));
	}

    public function uploadPictureAction(){

        if(isset($_FILES['file'])){
            $name = $_FILES['file']['name'];
            $fileSize = $_FILES['file']['size'];
            $uploadedFile = $_FILES['file']['tmp_name'];
        }

        $maxSize = 5;//In MB
        $allowedExts = array("jpg", "jpeg", "png", "gif");
        $temp = explode(".", $name);
        $extension = end($temp);

        $allowed = false;

        //Check file type and size
        if( in_array($extension, $allowedExts)){
            if( $fileSize < $maxSize*1000000){
                $allowed = true;
            }
        }

        if($allowed){
            //$dimensions = $this->container->getParameter('picture_dimensions')['project'];
            $dimensions = array(120, 200, 300);

            $session = $this->getRequest()->getSession();
            $fullProjectData =  $session->get('project_draft');

            $pictureId = md5( date_create()->getTimestamp() ); //This really should be the id of the project...
            $base = $this->get('kernel')->getRootDir() . '/../web/uploads/';
            $picturePath = $base.'projectPics/'.$pictureId.'.'.$extension;

            if(!empty($fullProjectData[2]["projectImage"])){
                foreach ($dimensions as $dim) {
                    unlink($base.'projectPics/'.$fullProjectData[2]["projectImage"].'-'.$dim.'.'.$fullProjectData[2]["projectExt"]);
                }
            }

            //Store the file in uploaded songs
            //move_uploaded_file($uploadedFile, $picturePath);
            
            foreach ($dimensions as $dim) {
                $this->square_crop($uploadedFile, $base.'projectPics/'.$pictureId.'-'.$dim.'.'.$extension, $dim, 100);
            }

            $path = $base.'projectPics/'.$pictureId.'-120.'.$extension;
            $type = pathinfo($path, PATHINFO_EXTENSION);
            $data = file_get_contents($path);
            $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);

            $fullProjectData[2]["projectImage"] = $pictureId;
            $fullProjectData[2]["projectExt"] = $extension;
            $fullProjectData[2]["projectImageBase64"] = $base64;
            $session->set('project_draft', $fullProjectData);

            $response = array(
                "msg" => "ok",
                "picture" => $base64
            );
        }
        else {
            $response = array(
                "msg" => "error"
            );
        }

        return new Response( json_encode($response));

    }


    public function uploadFileAction(){

        if(isset($_FILES['file'])){
            $name = $_FILES['file']['name'];
            $fileSize = $_FILES['file']['size'];
            $uploadedFile = $_FILES['file']['tmp_name'];
        }

        $maxSize = 5;//In MB
        $temp = explode(".", $name);
        $extension = end($temp);

        $allowed = false;

        if( $fileSize < $maxSize*1000000){
            $allowed = true;
        }

        if($allowed){
            $session = $this->getRequest()->getSession();
            $fullProjectData =  $session->get('project_draft');

            $fileId = md5( date_create()->getTimestamp() ); 
            $base = $this->get('kernel')->getRootDir() . '/../web/uploads/';
            $filePath = $base.'projectPics/'.$fileId.'.'.$extension;

            move_uploaded_file($uploadedFile, $filePath);

            $fullProjectData[3]['files'][] = array("name" => $fileId, "ext" => $extension);
            $session->set('project_draft', $fullProjectData);

            $response = array(
                "msg" => "ok",
                "filename" => $name
            );
        }
        else {
            $response = array(
                "msg" => "error"
            );
        }

        return new Response( json_encode($response));

    }

    private function square_crop($src_image, $dest_image, $thumb_size = 64, $jpg_quality = 90) {

        // Get dimensions of existing image
        $image = getimagesize($src_image);

        // Check for valid dimensions
        if( $image[0] <= 0 || $image[1] <= 0 ) return false;

        // Determine format from MIME-Type
        $image['format'] = strtolower(preg_replace('/^.*?\//', '', $image['mime']));

        // Import image
        switch( $image['format'] ) {
            case 'jpg':
            case 'jpeg':
                $image_data = imagecreatefromjpeg($src_image);
            break;
            case 'png':
                $image_data = imagecreatefrompng($src_image);
            break;
            case 'gif':
                $image_data = imagecreatefromgif($src_image);
            break;
            default:
                // Unsupported format
                return false;
            break;
        }

        // Verify import
        if( $image_data == false ) return false;

        // Calculate measurements
        if( $image[0] > $image[1] ) {
            // For landscape images
            $x_offset = ($image[0] - $image[1]) / 2;
            $y_offset = 0;
            $square_size = $image[0] - ($x_offset * 2);
        } else {
            // For portrait and square images
            $x_offset = 0;
            $y_offset = ($image[1] - $image[0]) / 2;
            $square_size = $image[1] - ($y_offset * 2);
        }

        // Resize and crop
        $canvas = imagecreatetruecolor($thumb_size, $thumb_size);
        if( imagecopyresampled(
            $canvas,
            $image_data,
            0,
            0,
            $x_offset,
            $y_offset,
            $thumb_size,
            $thumb_size,
            $square_size,
            $square_size
        )) {

            // Create thumbnail
            switch( strtolower(preg_replace('/^.*\./', '', $dest_image)) ) {
                case 'jpg':
                case 'jpeg':
                    return imagejpeg($canvas, $dest_image, $jpg_quality);
                break;
                case 'png':
                    return imagepng($canvas, $dest_image);
                break;
                case 'gif':
                    return imagegif($canvas, $dest_image);
                break;
                default:
                    // Unsupported format
                    return false;
                break;
            }

        } else {
            return false;
        }
    }

	public function provideDetailsAction(Request $request) {
        $session = $this->getRequest()->getSession();
        $fullProjectData =  $session->get('project_draft');
        //print_r($fullProjectData);exit;
        $provideDetails = new Project();
        switch ($fullProjectData[2]['form']['projectchecktype']) {
            case "CompleteSongs":
                $form = $this->createFormBuilder($provideDetails)
                    ->add('projectdetails', 'textarea',array(
                        'attr' => array(
                            'rows' => 5,
                            'placeholder'=> "Short project description",
                            "onfocus" => "this.placeholder = ''",
                            "onblur" => "this.placeholder = 'Short project description'",
                        )
                    ))
                    ->add('projecttempo', 'text',array(
                        'attr' => array(
                            'readonly'=> 'readonly',
                            'class'=> 'project_property_selected'
                        ),
                        'required' => false
                    ))
                    ->add('keysong', 'text',array(
                        'attr' => array(
                            'id' => 'key_song',
                            'readonly'=> 'readonly',
                            'class'=> 'project_property_selected'
                        ),
                        'required' => false
                    ))
                    ->add('moodsong', 'text',array(
                        'attr' => array(
                            'readonly'=> 'readonly',
                            'class'=> 'project_property_selected'
                        ),
                        'required' => false
                    ))
                    ->add('projectstyle', 'choice', array(
                        'choices' => array(
                            'Radio Friendly' => 'Radio Friendly',
                            'Art-Piece' => 'Art-Piece',
                        ),
                        'empty_value' => false,
                        'required'  => false,
                        'multiple' => false,
                        'expanded' => true
                    ))
                    ->add('drumspref', 'text',array(
                        'attr' => array(
                            'readonly'=> 'readonly',
                            'class'=> 'project_property_selected'
                        ),
                        'required' => false
                    ))
                    ->add('dominantsound', 'text',array(
                        'attr' => array(
                            'placeholder'=> "i.e. Piano, Jaz guitar",
                            "onfocus" => "this.placeholder = ''",
                            "onblur" => "this.placeholder = 'i.e. Piano, Jaz guitar'"
                        ),
                        'required' => false
                    ))
                    ->add('instrumentRef', 'text',array(
                        'attr' => array(
                            'readonly'=> 'readonly',
                            'class'=> 'project_property_selected'
                        ),
                        'required' => false
                    ))
                    ->add('songTopic', 'textarea',array(
                        'attr' => array(
                            'rows' => 5,
                            'placeholder'=> "Write your topics here",
                            "onfocus" => "this.placeholder = ''",
                            "onblur" => "this.placeholder = 'Write your topics here'"
                        )
                    ))
                    ->add('songMention', 'textarea',array(
                        'attr' => array(
                            'rows' => 5,
                            'placeholder'=> "Write your text here",
                            "onfocus" => "this.placeholder = ''",
                            "onblur" => "this.placeholder = 'Write your text here'"
                        )
                    ))
                    ->getForm();
                break;
            case "Production":
                $form = $this->createFormBuilder($provideDetails)
                    ->add('projectdetails', 'textarea',array(
                        'attr' => array(
                            'rows' => 5,
                            'placeholder'=> "Short project description",
                            "onfocus" => "this.placeholder = ''",
                            "onblur" => "this.placeholder = 'Short project description'",
                        )
                    ))
                    ->add('projecttempo', 'text',array(
                        'attr' => array(
                            'readonly'=> 'readonly',
                            'class'=> 'project_property_selected'
                        ),
                        'required' => false
                    ))
                    ->add('keysong', 'text',array(
                        'attr' => array(
                            'id' => 'key_song',
                            'readonly'=> 'readonly',
                            'class'=> 'project_property_selected'
                        ),
                        'required' => false
                    ))
                    ->add('moodsong', 'text',array(
                        'attr' => array(
                            'readonly'=> 'readonly',
                            'class'=> 'project_property_selected'
                        ),
                        'required' => false
                    ))
                    ->add('projectstyle', 'choice', array(
                        'choices' => array(
                            'Radio Friendly' => 'Radio Friendly',
                            'Art-Piece' => 'Art-Piece',
                        ),
                        'empty_value' => false,
                        'required'  => false,
                        'multiple' => false,
                        'expanded' => true
                    ))
                    ->add('drumspref', 'text',array(
                        'attr' => array(
                            'readonly'=> 'readonly',
                            'class'=> 'project_property_selected'
                        ),
                        'required' => false
                    ))
                    ->add('dominantsound', 'text',array(
                        'attr' => array(
                            'placeholder'=> "i.e. Piano, Jaz guitar",
                            "onfocus" => "this.placeholder = ''",
                            "onblur" => "this.placeholder = 'i.e. Piano, Jaz guitar'"
                        ),
                        'required' => false
                    ))
                    ->add('instrumentRef', 'text',array(
                        'attr' => array(
                            'readonly'=> 'readonly',
                            'class'=> 'project_property_selected'
                        ),
                        'required' => false
                    ))
                    ->getForm();
                break;
            case "Songwriting":
                $form = $this->createFormBuilder($provideDetails)
                    ->add('projectdetails', 'textarea',array(
                        'attr' => array(
                            'rows' => 5,
                            'placeholder'=> "Short project description",
                            "onfocus" => "this.placeholder = ''",
                            "onblur" => "this.placeholder = 'Short project description'",
                        )
                    ))
                    ->add('songTopic', 'textarea',array(
                        'attr' => array(
                            'rows' => 5,
                            'placeholder'=> "Write your topics here",
                            "onfocus" => "this.placeholder = ''",
                            "onblur" => "this.placeholder = 'Write your topics here'"
                        )
                    ))
                    ->add('songMention', 'textarea',array(
                        'attr' => array(
                            'rows' => 5,
                            'placeholder'=> "Write your text here",
                            "onfocus" => "this.placeholder = ''",
                            "onblur" => "this.placeholder = 'Write your text here'"
                        )
                    ))
                ->getForm();
                break;
            case "Musician":
                $form = $this->createFormBuilder($provideDetails)
                    ->add('projectdetails', 'textarea',array(
                        'attr' => array(
                            'rows' => 5,
                            'placeholder'=> "Short project description",
                            "onfocus" => "this.placeholder = ''",
                            "onblur" => "this.placeholder = 'Short project description'",
                        )
                    ))
                    ->add('musicianType', 'text',array(
                        'attr' => array(
                            'readonly'=> 'readonly',
                            'class'=> 'project_property_selected'
                        ),
                        'required' => false
                    ))
                    ->add('musicianTech', 'text',array(
                        'attr' => array(
                            'readonly'=> 'readonly',
                            'class'=> 'project_property_selected'
                        ),
                        'required' => false
                    ))
                    ->getForm();
                break;
            case "Vocal":
                $form = $this->createFormBuilder($provideDetails)
                    ->add('projectdetails', 'textarea',array(
                        'attr' => array(
                            'rows' => 5,
                            'placeholder'=> "Short project description",
                            "onfocus" => "this.placeholder = ''",
                            "onblur" => "this.placeholder = 'Short project description'",
                        )
                    ))
                    ->add('genderVocal', 'text',array(
                        'attr' => array(
                            'readonly'=> 'readonly',
                            'class'=> 'project_property_selected'
                        ),
                        'required' => false
                    ))
                    ->add('vocalRange', 'text',array(
                        'attr' => array(
                            'readonly'=> 'readonly',
                            'class'=> 'project_property_selected'
                        ),
                        'required' => false
                    ))
                    ->add('vocalLan', 'text',array(
                        'attr' => array(
                            'id' => 'project_genre',
                            'placeholder'=> "i.e. English, Spanish",
                            "onfocus" => "this.placeholder = ''",
                            "onblur" => "this.placeholder = 'i.e. English, Spanish'"
                        )
                    ))
                    ->getForm();
                break;
            case "Engineering":
                $form = $this->createFormBuilder($provideDetails)
                    ->add('projectdetails', 'textarea',array(
                        'attr' => array(
                            'rows' => 5,
                            'placeholder'=> "Short project description",
                            "onfocus" => "this.placeholder = ''",
                            "onblur" => "this.placeholder = 'Short project description'",
                        )
                    ))
                    ->add('engTempo', 'text',array(
                        'attr' => array(
                            'placeholder'=> "i.e. 95, 120.5",
                            "onfocus" => "this.placeholder = ''",
                            "onblur" => "this.placeholder = 'i.e. 95, 120.5'"
                        )
                    ))
                    ->add('keysong', 'text',array(
                        'attr' => array(
                            'id' => 'key_song',
                            'readonly'=> 'readonly',
                            'class'=> 'project_property_selected'
                        ),
                        'required' => false
                    ))
                    ->getForm();
                break;
        }


		if ($request->getMethod() == 'POST') {
            $form->bind($request);
            if ($form->isValid() || !$form->isValid()) {
                $this->updateDraft($request, 3);
                if($_FILES['project_reference'])
                    $this->saveReferenceFile();
                return $this->redirect(
                    $this->generateUrl('projects_setBudgetAndDeadline')
                );
		    }
        }

        return $this->render(
            "SooundAppBundle:Projects:provide-details.html.twig", array(
            'form' => $form->createView()
        ));

	}
    public function saveReferenceFile(){
        $base = $this->get('kernel')->getRootDir() . '/../web/uploads/';
        foreach( $_FILES['project_reference'] as $key => $all ){
            foreach( $all as $i => $val ){
                $files[$i][$key] = $val;    
            }    
        }
        $maxSize = 10;//In MB
        $session = $this->getRequest()->getSession();
        $dm = $this->get('doctrine_mongodb')->getManager();
        $ffmpeg = $this->get('soound_app.ffmpeg');
        $fullProjectData =  $session->get('project_draft');

        foreach ($files as $key=>$file) {
            //Check if file could be uploaded successfully and maximum size
            if($file['tmp_name'] && $file['size'] < $maxSize*1000000){

                $path_parts = pathinfo($file['name']);
                $extension = $path_parts['extension'];
                $filename = $path_parts['filename'];
                $audioExts = array("wav", "m4a", "mp3", "aac");

                //Check file type for audio
                if( in_array($extension, $audioExts)){
                    $audioPath = $base . 'references/audio/' . $filename . '.' . $extension;

                    //Store the file in uploaded songs
                    move_uploaded_file($file['tmp_name'], $audioPath);
                    $metadata = $ffmpeg->getMetadata($audioPath);
                    $waveFormPath = $base.'references/waveforms/'.$filename.'.svg';
                    $ffmpeg->saveWaveform($audioPath, $waveFormPath, null);
                    $fullProjectData[3]["references"][$key] = array("desc"=>$_POST["project_reference"][$key]["desc"], "isAudio"=>true, "extension"=>$extension ,"audio"=>$audioPath, "waveform"=>$waveFormPath, "duration"=>$metadata['duration'], "title"=>$metadata['title']);
                }
            }
        }
        $session->set('project_draft', $fullProjectData);
        return;          
    }
    public function detailsSongwritingAction(Request $request){
        $detailsSongwriting = new Project();
        $form = $this->createFormBuilder($detailsSongwriting)
           ->getForm();

        if ($request->getMethod() == 'POST') {
            $form->bind($request);
            if ($form->isValid() || !$form->isValid()) {
                $this->updateDraft($request, 3);
                return $this->redirect(
                    $this->generateUrl('projects_setBudgetAndDeadline')
                );
            }
        }

        return $this->render(
            "SooundAppBundle:Projects:detailsSongwriting.html.twig", array(
            'form' => $form->createView()
        ));
    }

	public function setBudgetAndDeadlineAction(Request $request) {
        $BudgetAndDeadline = new Project();
        $form = $this->createFormBuilder($BudgetAndDeadline, array('validation_groups' => array('step4')))
            ->add('budget', 'number',array(
                'attr' => array(
                    'placeholder'=> "How much are you willing to spend?",
                    "onfocus" => "this.placeholder = ''",
                    "onblur" => "this.placeholder = 'How much are you willing to spend?'"
                )
            ))
            ->add('deadLine', 'text',array(
                'attr' => array(
                    'placeholder'=> "mm / dd / yyyy",
                    "onfocus" => "this.placeholder = ''",
                    "onblur" => "this.placeholder = 'mm / dd / yyyy'"
                )
            ))
            ->add('isFeatured', 'choice', array(
                'choices' => array(
                    'private' => 'Private / Invite only project',
                    'featured' => 'Featured project'
                ),
                'empty_value' => false,
                'required'  => false,
                'multiple' => false,
                'expanded' => true
            ))
            ->getForm();
        if ($request->getMethod() == 'POST') {
            $form->bind($request);
            if ($form->isValid()) {
                $this->updateDraft($request, 4);
                return $this->redirect(
                    $this->generateUrl('projects_reviewAndConfirm')
                );
            }
            else{
                return $this->render(
                    "SooundAppBundle:Projects:budget-and-deadline.html.twig",array(
                    'form' => $form->createView()
                ));
            }
		}
        return $this->render(
            "SooundAppBundle:Projects:budget-and-deadline.html.twig",array(
            'form' => $form->createView()
        ));
	}

	public function reviewAndConfirmAction(Request $request) {

        $session = $this->getRequest()->getSession();
        $fullProjectData =  $session->get('project_draft');
        $securityContext =$this->get('security.context');
        $user = $securityContext->getToken()->getUser();
      
        $cardChoices = array();
        if($user != 'anon.'){
            $storedCards = $user->getStoredCards();
            if($storedCards && count($storedCards) != 0 ){
                foreach ($storedCards as $card) {
                    $cardChoices[$card->getId()] = $card->getName().' '.$card->getType().' ending in '.$card->getMaskedNumber().' expires on ('.$card->getExpiration().')';
                }
                $cardChoices["new"] = "Use Other Card";
            }
            else
                $cardChoices["new"] = "Add a Card";
        } else {
            $cardChoices["new"] = "Add a Card"; 
        }

        $form = $this->createFormBuilder(array(), array('validation_groups' => array('step5')))
            ->add('payMethod', 'choice', array(
                'choices' => $cardChoices,
                'required'  => true,
                'multiple' => false,
                'expanded' => true
            ))
            ->add('creditCard', 'text',array(
                'attr' => array(
                    'placeholder'=> "16 digit number on the front of the card",
                    "onfocus" => "this.placeholder = ''",
                    "onblur" => "this.placeholder = '16 digit number on the front of the card'"
                )
            ))
            ->add('cvc', 'text',array(
                'attr' => array(
                    'placeholder'=> "i.e. 999",
                    "onfocus" => "this.placeholder = ''",
                    "onblur" => "this.placeholder = 'i.e. 999'",
                    "maxlength" => 3
                )
            ))
            ->add('expirationMonth', 'text',array(
                'attr' => array(
                    'placeholder'=> "mm",
                    'readonly'=> 'readonly',
                    'class'=> 'project_property_selected',
                    "onfocus" => "this.placeholder = ''",
                    "onblur" => "this.placeholder = 'mm'",
                    "maxlength" => 2
                )
            ))
            ->add('expirationYear', 'text',array(
                'attr' => array(
                    'placeholder'=> "yyyy",
                    'readonly'=> 'readonly',
                    'class'=> 'project_property_selected',
                    "onfocus" => "this.placeholder = ''",
                    "onblur" => "this.placeholder = 'yyyy'",
                    "maxlength" => 2
                )
            ))
            ->add('cardName', 'text',array(
                'attr' => array(
                    'placeholder'=> "i.e. John Smith",
                    "onfocus" => "this.placeholder = ''",
                    "onblur" => "this.placeholder = 'i.e. John Smith'",
                    "maxlength" => 200
                )
            ))
            ->add('billingZip', 'text',array(
                'attr' => array(
                    'placeholder'=> "i.e. 20505",
                    "onfocus" => "this.placeholder = ''",
                    "onblur" => "this.placeholder = 'i.e. 20505'",
                    "maxlength" => 9
                )
            ));

        if (!$securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
                $form
                    ->add('emailAddress', 'text', array(
                        'attr' => array(
                            'placeholder'=> "i.e. johnny@gmail.com",
                            "onfocus" => "this.placeholder = ''",
                            "onblur" => "this.placeholder = 'i.e. johnny@gmail.com'"
                        )
                    ))
                    ->add('firstName', 'text', array(
                        'required' => true,
                        'attr' => array(
                            'placeholder'=> "i.e. John",
                            "onfocus" => "this.placeholder = ''",
                            "onblur" => "this.placeholder = 'i.e. John'"
                        )
                    ))
                    ->add('lastName', 'text', array(
                        'required' => true,
                        'attr' => array(
                            'placeholder'=> "i.e. Doe",
                            "onfocus" => "this.placeholder = ''",
                            "onblur" => "this.placeholder = 'i.e. Doe'"
                        )
                    ))
                    ->add('plainPassword', 'repeated', array(
                        'type' => 'password',
                        'invalid_message' => 'The password fields must match.',
                        'options' => array('attr' => array('class' => 'password-field')),
                        'required' => true,
                        'first_options'  => array('attr'=> array(
                            'placeholder'=> "6+ characters (a-Z, 09)",
                            "onfocus" => "this.placeholder = ''",
                            "onblur" => "this.placeholder = '6+ characters (a-Z, 09)'"
                        )),
                        'second_options' => array('attr' => array(
                            'placeholder'=> "Retype your password",
                            "onfocus" => "this.placeholder = ''",
                            "onblur" => "this.placeholder = 'Retype your password'"
                        )),
                    ));
        }
        $form = $form->getForm();

		if ($request->getMethod() == 'POST') {
            $form->bind($request);
            $data = $form->getData();
            $logged = false;
            $creditCardErrors = array();

            //if ($data['payMethod'] == 'cc'){

                $fullProjectData =  $session->get('project_draft');

                if ($securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')){
                    $user = $securityContext->getToken()->getUser();
                    $logged = true;
                }else{
                    //Create the user with all fos dependences
                    $userManager = $this->container->get('fos_user.user_manager');
                    $user = $userManager->createUser();

                    $user->setEnabled(true);
                    $user->setEmail($data['emailAddress']);
                    $user->setFullname($data['firstName'].' '.$data['lastName']);
                    //$user->setUsername($data['emailAddress']);
                    $encoder = $this->get('security.encoder_factory')->getEncoder($user);
                    $encodedPass = $encoder->encodePassword($data['plainPassword'], $user->getSalt());
                    $user->setPassword($encodedPass);

                    /* Confirmation Email */
                    $mailer = $this->container->get('fos_user.mailer.custom');
                    $token = sha1(uniqid(mt_rand(), true));
                    $user->setConfirmationToken($token);
                    $mailer->sendConfirmationEmailMessage($user);
                    /* Confirmation Email */
                    
                    $userManager->updateUser($user);

                    $token = new UsernamePasswordToken(
                        $user, 
                        $user->getPassword(), 
                        'main',
                        array(
                            'ROLE_USER',
                            'IS_AUTHENTICATED_REMEMBERED'
                        )
                    );

                    $securityContext->setToken($token);
                    $logged = true;

                    //Now send a public notification about this user's signup
                    /*
                    $activity = array(
                        'to' => 'public',
                        'type' => 'people',
                        'date' => date_format( date_create(), "m-d-Y" ),
                        'private' => false, //Optional, defaults to false
                        'content' => array(
                            0 => array(
                                'ref' => $user,
                                'text' => ' joined Soound!'
                            )
                        )
                    );
                    $this->get('soound_app.activity')->send($activity);
                    */
                }
/*
                $factory = $this->get('comet_cult_braintree.factory');

                //Check if customer is in braintree, if not, create customer in braintree
                if(!$user->getOnBraintree()){
                    $customerFactory = $factory->get('customer');
                    $customer = $customerFactory::create(array(
                        'id' => $user->getId()
                    ));
                    $user->setOnBraintree(true);
                }
*/
                /*
                if( $data['payMethod'] == 'new' ){
                    $creditCardFactory = $factory->get('creditCard');
                    $result = $creditCardFactory::create(array(
                        'customerId' => $user->getId(),
                        'cardholderName' => $data['cardName'],
                        'cvv' => $data['cvc'],
                        'number' => $data['creditCard'],
                        'expirationMonth' => $data['expirationMonth'],
                        'expirationYear' => $data['expirationYear'],
                        'billingAddress' => array(
                            'postalCode' => $data['billingZip']
                        ),
                        'options' => array(
                            'verifyCard' => true
                        )
                    ));

                    if( $result->success ){
                        $creditCard = new CreditCard();
                        $user->addStoredCard($creditCard);
                        $creditCard->setUser($user);
                        $creditCard->setToken( $result->creditCard->token );
                        $creditCard->setMaskedNumber('***'.$result->creditCard->last4);
                        $creditCard->setType($result->creditCard->cardType);
                        $creditCard->setName($result->creditCard->cardholderName);
                        $creditCard->setExpiration($result->creditCard->expirationDate);

                        $dm = $this->get('doctrine_mongodb')->getManager();
                        $dm->persist($creditCard);
                        $dm->flush();

                        $cardToken = $result->creditCard->token;
                    } 
                    else {
                        foreach (($result->errors->deepAll()) as $error) {
                            $form->addError(new FormError($error->message . "{$error->code}"));
                        }
                    }
                }
                else {
                    $cardToken = $user->getStoredCard($data['payMethod'])->getToken();
                }
                */
/*
                if(isset($cardToken)){
                    $transactionFactory = $factory->get('transaction');
                    $transaction = $transactionFactory::sale(array(
                        'amount' => $fullProjectData[4]['form']['budget'],
                        'paymentMethodToken' => $cardToken,
                        'customerId' => $user->getId(),
                        'options' => array(
                            'storeInVaultOnSuccess' => true
                        )
                    ));
                    if ($transaction->success) { //Card verified
                        //Create the transaction
                        //echo("Success! Transaction ID: " . $result->token->id);
                        $session->set('transaction', $transaction->transaction->id);
                    }
                    //else if ($creditCard->transaction) {
                    //    $form->addError(new FormError(
                    //        "Error: " . $result->message . "<br />" .
                    //        "Code: " . $result->transaction->processorResponseCode
                    //    ));
                    //} 
                    else {
                        foreach (($transaction->errors->deepAll()) as $error) {
                            $form->addError(new FormError($error->message . "{$error->code}"));
                        }
                    }
                }
                
                /*
                $transaction = $transactionFactory::sale(array(
                    'amount' => $fullProjectData[4]['form']['budget'],
                    'creditCard' => array(
                        'cardholderName' => $data['cardName'],
                        'cvv' => $data['cvc'],
                        'number' => $data['creditCard'],
                        'expirationMonth' => $data['expirationMonth'],
                        'expirationYear' => $data['expirationYear']
                    ),
                    'billing' => array(
                        'postalCode' => $data['billingZip']
                    ),
                    'customer' => array(
                        'id' => $user->getId()
                    ),
                    'options' => array(
                        'storeInVaultOnSuccess' => true,
                        'addBillingAddressToPaymentMethod' => true
                    )
                ));
                */

            //}


            if ($form->isValid()) {
                $this->updateDraft($request, 5);

                //if user is logged no need step 6
                if ($logged) {
                    $project = $this->saveProjectData($user);
                    return $this->redirect(
                        $this->generateUrl('projectSubmissions', array(
                            'publicId' => $project->getPublicId()
                        ))
                    );
                }
                else {
                    $project = $this->saveProjectData($user);
                    return $this->redirect(
                        $this->generateUrl('projects_projectDone')
                    );
                }
                
            }
            else{
                return $this->render(
                    "SooundAppBundle:Projects:review-and-confirm.html.twig",array(
                    'form' => $form->createView()
                ));
            }
        
		}

        return $this->render(
            "SooundAppBundle:Projects:review-and-confirm.html.twig",array(
            'form' => $form->createView()
        ));
	}

    public function projectDoneAction(Request $request) {
        $user = new User();
        $form = $this->createFormBuilder($user, array('validation_groups' => array('step6')))
            ->add('plainPassword', 'repeated', array(
                'type' => 'password',
                'invalid_message' => 'The password fields must match.',
                'options' => array('attr' => array('class' => 'password-field')),
                'required' => true,
                'first_options'  => array('attr'=> array(
                    'placeholder'=> "6+ characters (a-Z, 09)",
                    "onfocus" => "this.placeholder = ''",
                    "onblur" => "this.placeholder = '6+ characters (a-Z, 09)'"
                )),
                'second_options' => array('attr' => array(
                    'placeholder'=> "your password again",
                    "onfocus" => "this.placeholder = ''",
                    "onblur" => "this.placeholder = 'your password again'"
                )),
            ))
            ->getForm();

        if ($request->getMethod() == 'POST') {
            $form->bind($request);
            if ($form->isValid()) {
                $this->updateDraft($request, 6);

                //Update user password
                $userManager = $this->container->get('fos_user.user_manager');
                $user = $this->get('security.context')->getToken()->getUser();
                $encoder = $this->get('security.encoder_factory')->getEncoder($user);
                $encodedPass = $encoder->encodePassword($fullProjectData[6]['form']['plainPassword']['second'], $user->getSalt());
                $user->setPassword($encodedPass);

                $userManager->updateUser($user);

                $project = $this->saveProjectData();

                return $this->redirect(
                    $this->generateUrl('projectSubmissions', array(
                        'publicId' => $project->getPublicId()
                    ))
                );
            }
            else{
                return $this->render(
                    "SooundAppBundle:Projects:project-done.html.twig",array(
                    'form' => $form->createView()
                ));
            }
        }

        return $this->render(
            "SooundAppBundle:Projects:project-done.html.twig",array(
            'form' => $form->createView()
        ));
    }

	private function updateDraft($request, $step ) {
        $session = $this->getRequest()->getSession();
        $postData = array();
        foreach ($request->request as $key => $value) {
                $postData[$key] = $value;
        }

        $fullProjectData =  $session->get('project_draft');
        if($step === 2){
            $postData["projectImage"] = $fullProjectData[$step]["projectImage"];
            $postData["projectExt"] = $fullProjectData[$step]["projectExt"];
            $postData["projectImageBase64"] = $fullProjectData[$step]["projectImageBase64"];
        }        
        else if($step == 3){
            $postData['files'] = $fullProjectData[$step]['files'];
        }

        
        $fullProjectData[$step] = $postData;
        $session->set('project_draft', $fullProjectData);
	}

    private function saveProjectData($user){
        $logged = 0;
        $session = $this->getRequest()->getSession();
        $dm = $this->get('doctrine_mongodb')->getManager();
        $s3 = $this->get('aws_s3');
        $fullProjectData =  $session->get('project_draft');

        $genreArray = explode(",", strtolower($fullProjectData[2]['form']['projectgenre']));

        $project = new Project();
/*
        if(!$session->get('transaction'))
            throw new \Exception('Transaction not set');
        $project->setTransactionId( $session->get('transaction') );
*/
        $project->setProjectName($fullProjectData[2]['form']['projectname']);
        $project->setProjectDetails($fullProjectData[3]['form']['projectdetails']);
        $project->setProjectType(strtolower($fullProjectData[2]['form']['projectchecktype']));
        $project->setProjectgenre($genreArray);

        //Complete Songs
        if (!empty($fullProjectData[3]['form']['projecttempo']) && $fullProjectData[3]['form']['projecttempo'] != 'Select option');
            {$project->setProjecttempo($fullProjectData[3]['form']['projecttempo']);}
        if (!empty($fullProjectData[3]['form']['keysong']) && $fullProjectData[3]['form']['keysong'] != 'Select option')
            {$project->setKeysong($fullProjectData[3]['form']['keysong']);}
        if(!empty($fullProjectData[3]['form']['moodsong']) && $fullProjectData[3]['form']['moodsong'] != 'Select option')
            {$project->setMoodsong($fullProjectData[3]['form']['moodsong']);}
        if(!empty($fullProjectData[3]['form']['projectstyle']))
            {$project->setProjectstyle($fullProjectData[3]['form']['projectstyle']);}
        if(!empty($fullProjectData[3]['form']['drumspref']) && $fullProjectData[3]['form']['drumspref'] != 'Select option')
            {$project->setDrumspref($fullProjectData[3]['form']['drumspref']);}
        if(!empty($fullProjectData[3]['form']['instrumentRef']) && $fullProjectData[3]['form']['instrumentRef'] != 'Select option')
            {$project->setInstrumentRef($fullProjectData[3]['form']['instrumentRef']);}
        if(!empty($fullProjectData[3]['form']['songTopic']))
            {$project->setSongTopic($fullProjectData[3]['form']['songTopic']);}
        if(!empty($fullProjectData[3]['form']['songMention']))
            {$project->setSongMention($fullProjectData[3]['form']['songMention']);}
        if(!empty($fullProjectData[3]['form']['dominantsound']))
            {$project->setDominantsound(explode(",", $fullProjectData[3]['form']['dominantsound']));}

        //Musician
        if(!empty($fullProjectData[3]['form']['musicianType']))
            {$project->setMusicianType($fullProjectData[3]['form']['musicianType']);}
        if(!empty($fullProjectData[3]['form']['vocalRange']))
            {$project->setVocalRange($fullProjectData[3]['form']['vocalRange']);}

        //Vocal
        if(!empty($fullProjectData[3]['form']['genderVocal']))
            {$project->setGenderVocal($fullProjectData[3]['form']['genderVocal']);}
        if(!empty($fullProjectData[3]['form']['musicianTech']))
            {$project->setMusicianTech($fullProjectData[3]['form']['musicianTech']);}
        if(!empty($fullProjectData[3]['form']['vocalLan']))
            {$project->setVocalLan($fullProjectData[3]['form']['vocalLan']);}

        //Engineering
        if (!empty($fullProjectData[3]['form']['engTempo']))
            {$project->setEngTempo($fullProjectData[3]['form']['engTempo']);}

        
        if(!empty($fullProjectData[3]['project_reference'])){
            foreach ($fullProjectData[3]['project_reference'] as $key=>$ref) {
                if(strlen($ref['desc'])){
                    if(isset($fullProjectData[3]['references'][$key]))
                        continue;
                    $projectRef = new ProjectReference();
                    $projectRef->setLink($ref['link']);
                    $projectRef->setDescription($ref['desc']);
                    $dm->persist($projectRef);
                    $dm->flush();
                    $project->addReference($projectRef);
                }
            }
        }
        if(!empty($fullProjectData[4]['form']['isFeatured']))
            $project->setFeatured($fullProjectData[4]['form']['isFeatured']);
        $project->setBudget($fullProjectData[4]['form']['budget']);
        //$project->setDeadLine($fullProjectData[4]['form']['deadLine']);
        $project->setDueDate(new \DateTime($fullProjectData[4]['form']['deadLine']));
        $project->setUser($user);
        $project->publishProject();

        $dm->persist($project);
        $dm->flush();

        $base = $this->get('kernel')->getRootDir() . '/../web/uploads/';
        //We are doing these after persisting project because we need project's publicId
        if(!empty($fullProjectData[2]['projectImage'])) {
            //$dimensions = $this->container->getParameter('picture_dimensions')['project'];
            $dimensions = array(120, 200, 300);
            foreach ($dimensions as $dim) {
                $s3->create_object($this->container->getParameter('s3_bucket'),"Projects/".$project->getPublicId()."/image/".$project->getPublicId()."-".$dim.".".$fullProjectData[2]['projectExt'],array('fileUpload' => $base.'projectPics/'.$fullProjectData[2]['projectImage'].'-'.$dim.'.'.$fullProjectData[2]['projectExt']));
                unlink($base.'projectPics/'.$fullProjectData[2]['projectImage'].'-'.$dim.'.'.$fullProjectData[2]['projectExt']);
            }
            $project->setProjectExt($fullProjectData[2]['projectExt']);
        }
        if(!empty($fullProjectData[3]['references'])){
            foreach ($fullProjectData[3]['references'] as $ref) {
                if(strlen($ref['desc'])){
                    $projectRef = new ProjectReference();
                    $projectRef->setIsAudio($ref['isAudio']);
                    $projectRef->setExtension($ref['extension']);
                    $projectRef->setDescription($ref['desc']);
                    $projectRef->setDuration($ref['duration']);
                    $projectRef->setTitle($ref['title']);
                    $dm->persist($projectRef);

                    if($ref['isAudio']){
                        $s3->create_object($this->container->getParameter('s3_bucket'),"Projects/".$project->getPublicId()."/reference/audio/".$projectRef->getReferenceId().'.'.$ref['extension'],array('fileUpload' => $ref['audio']));
                        $s3->create_object($this->container->getParameter('s3_bucket'),"Projects/".$project->getPublicId()."/reference/image/".$projectRef->getReferenceId().'.svg',array('fileUpload' => $ref['waveform']));

                        unlink($ref['audio']);
                        unlink($ref['waveform']);
                    }

                    //$this->saveReferenceFile($projectRef->getReferenceId(), $ref);
                    $project->addReference($projectRef);
                }
            }
        }
        /*
        var_dump($fullProjectData[3]['files']);
        echo "files end";
        var_dump($fullProjectData[3]['project_files']);
        echo "project files end";
        exit;*/
        if(!empty($fullProjectData[3]['project_files'])){
            $i = -1;
            foreach ($fullProjectData[3]['project_files'] as $key => $file) {
                if(strlen($file['desc'])){
                    $projectFile = new ProjectFile();
                    $projectFile->setDescription($file['desc']);
                    $projectFile->setProject($project);
                    if($file['hasFile']=="1"){
                        $filePath = $base.'projectPics/'.$fullProjectData[3]['files'][$key+$i]['name'].'.'.$fullProjectData[3]['files'][$key+$i]['ext'];
                        $projectFile->setExtension($fullProjectData[3]['files'][$key+$i]['ext']);
                        $s3->create_object($this->container->getParameter('s3_bucket'),"Projects/".$project->getPublicId()."/file/".$projectFile->getFileId().'.'.$fullProjectData[3]['files'][$key+$i]['ext'],array('fileUpload' => $filePath));
                        //unlink($filePath);
                    }
                    else{
                        $i--;
                        $projectFile->setLink($file['link']);
                    }
                    $dm->persist($projectFile);
                    $dm->flush($projectFile);
                    $project->addFile($projectFile);
                }
            }
        }
        $dm->persist($project);
        $dm->flush($project);

        $activity = array(
            'type' => 'new-project',
            'date' => date_format( date_create(), "Y/m/d" ),
            'private' => ($project->getIsFeatured()=='private' ? true : false), //Optional, defaults to false
            'content' => array(
                0 => array(
                    'ref' => $project,
                    'text' => ' project created by '
                ),
                1 => array(
                    'ref' => $user
                )
            )
        );  
        $this->get('soound_app.activity')->send($activity);
        
        $session->clear();
        return $project;
    }
}