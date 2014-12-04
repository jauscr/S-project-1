<?php

namespace Soound\AppBundle\Services;

use Symfony\Component\Process\Process,
	Symfony\Component\Process\ProcessBuilder;

use \Exception;
use \RuntimeException;

class FFMpeg
{
	private $binary;
	private $timeout;
	private $uploadsDir;
	
	public function __construct($binary, $s3, $s3_bucket, $rootDir, $timeout = 60, $sampling_rate=2000, $bit_rate=8) {
		/*
		if (!is_executable($binary)) {
			throw new Exception(sprintf('`%s` is not a valid binary', $binary));
		}
		*/
		//$this->binary = $binary; //For Windows
		$this->binary = 'ffmpeg';//For Linux
		$this->s3 = $s3;
		$this->s3_bucket = $s3_bucket;
		$this->timeout = $timeout;
		$this->sampling_rate = $sampling_rate;
		$this->bit_rate = $bit_rate;
		$this->uploadsDir = $rootDir.'/../web/uploads/';
	}

	public function saveWaveform($path, $wavePath, $s3Path, $small = true) {
		//$wavFile = __DIR__.'/tmp/' . uniqid() . '.wav';
		$wavFile = $this->uploadsDir.uniqid().'.wav';
		$builder = ProcessBuilder::create(array(
			$this->binary,
			'-y',
			'-i', $path,
			'-ar', $this->sampling_rate,
			'-ab', $this->bit_rate,
			$wavFile
		));

		$process = $builder->getProcess();
		$process->setTimeout($this->timeout);

		$process->run();

		if (!$process->isSuccessful()) {
			throw new RuntimeException(sprintf('Encoding failed: %s', $process->getErrorOutput()));
		}

		$default_width = 740; //700
		$default_width2 = 700; //second one is for revisions
		$default_height = 84;
		$detail = 100;		//This majorly effects the outputed svg file (higher # = lower quality)
		$relative = true;
		$min_height = 15;

		$svg = "<svg version='1.1' baseProfile='full' width='100%' height='100%' xmlns='http://www.w3.org/2000/svg'>";

		$handle = fopen($wavFile, "r");
		$heading[] = fread($handle, 4);
		$heading[] = bin2hex(fread($handle, 4));
		$heading[] = fread($handle, 4);
		$heading[] = fread($handle, 4);
		$heading[] = bin2hex(fread($handle, 4));
		$heading[] = bin2hex(fread($handle, 2));
		$heading[] = bin2hex(fread($handle, 2));
		$heading[] = bin2hex(fread($handle, 4));
		$heading[] = bin2hex(fread($handle, 4));
		$heading[] = bin2hex(fread($handle, 2));
		$heading[] = bin2hex(fread($handle, 2));
		$heading[] = fread($handle, 4);
		$heading[] = bin2hex(fread($handle, 4));

		$peek = hexdec(substr($heading[10], 0, 2));
		$byte = $peek/8;

		$channel = hexdec(substr($heading[6], 0, 2)); 
		$ratio = ($channel == 2 ? 40 : 80);

		$data_size = floor((filesize($wavFile) - 44) / ($ratio + $byte) + 1);
		$data_point = 0;

		$svg .= "<path d=\"M0 40";
		$svg2 = $svg;
		$inverse = "";
		$inverse2 = "";

		$max = 255;

		if($relative){
			$rewind = ftell($handle);
			$max = 0;

			while(!feof($handle) && $data_point < $data_size){
				if($data_point++ % $detail == 0){
					$bytes = array();
					for($i = 0; $i< $byte; $i++)
						$bytes[$i] = fgetc($handle);

					switch($byte){
						case 1: 
							$data = $findValues($bytes[0], $bytes[1]);
							break;
						case 2: 
							if(ord($bytes[1]) & 128)
							$temp = 0;
						else
							$temp = 128;
							$temp = chr((ord($bytes[1]) & 127) + $temp);
							$data = floor($this->findValues($bytes[0], $temp) / 256);
							break;
					}

				fseek($handle, $ratio, SEEK_CUR);
				if($data > $max)
					$max = $data;
				}
			 else {
				fseek($handle, $ratio + $byte, SEEK_CUR);
			}
		}
		fseek($handle, $rewind, SEEK_SET);
		$data_point = 0;
	  }


		while(!feof($handle) && $data_point < $data_size){
			if ($data_point++ % $detail == 0) {
				$bytes = array();

				for ($i = 0; $i < $byte; $i++)
					$bytes[$i] = fgetc($handle);

				switch($byte){
					case 1:
						$data = $this->findValues($bytes[0], $bytes[1]);
						break;
					case 2:
						if(ord($bytes[1]) & 128)
							$temp = 0;
						else
							$temp = 128;
							$temp = chr((ord($bytes[1]) & 127) + $temp);
							$data = floor($this->findValues($bytes[0], $temp) / 256);
							break;
				}

				fseek($handle, $ratio, SEEK_CUR);
				$x1 = number_format($data_point / $data_size * $default_width, 2);
				$x2 = number_format($data_point / $data_size * $default_width2, 2);
				if( $data > floor($max/2) )
					$data = $max - $data;

				$y1 = number_format( $data / $max * ($default_height-$min_height), 2);

				$svg .= " L{$x1} ".($y1 - $min_height/2)."";
				$inverse = " L{$x1} ".( ($default_height+$min_height/2) - $y1)."".$inverse;

				$svg2 .= " L{$x2} ".($y1 - $min_height/2)."";
				$inverse2 = " L{$x2} ".( ($default_height+$min_height/2) - $y1)."".$inverse;
			}
			else       
				fseek($handle, $ratio + $byte, SEEK_CUR);
		}
		$svg .= $inverse." Z\" /></svg>";
		$svg2 .= $inverse2." Z\" /></svg>";

		fclose($handle); 
		unlink($wavFile);

		$svg_file = $wavePath;
		$small_svg = str_replace('.svg','-small.svg',$svg_file);
		$handle = fopen($svg_file,'w');
		$handle2 = fopen($small_svg,'w');

		fwrite($handle , $svg);
		fwrite($handle2, $svg2);
		fclose($handle);
		fclose($handle2);

		if($s3Path){
			//upload to s3
			$this->s3->create_object($this->s3_bucket,$s3Path.'.svg',array('fileUpload' => $svg_file));
			if($small)
				$this->s3->create_object($this->s3_bucket,$s3Path.'-small.svg',array('fileUpload' => $small_svg));

			unlink($svg_file);
			unlink($small_svg);
		}
	}
	function findValues($byte1, $byte2)
	{
		$byte1 = hexdec(bin2hex($byte1));                        
		$byte2 = hexdec(bin2hex($byte2));                        
		return ($byte1 + ($byte2*256));
	}
	public function getMetadata($path) {
		
		//$metadataFile = __DIR__.'/tmp/' . uniqid().'.txt';
		$metadataFile = $this->uploadsDir.uniqid().'.txt';
	
		$builder = ProcessBuilder::create(array(
			$this->binary,
			'-y',
			'-i', $path,
			'-f', 'ffmetadata',
			$metadataFile
		));
		
		$process = $builder->getProcess();
		$process->setTimeout($this->timeout);

		$process->run();

		if (!$process->isSuccessful()) {
			throw new RuntimeException(sprintf('Metadata extraction failed: %s', $process->getErrorOutput()));
		}

		$metadata = explode("\n", file_get_contents($metadataFile));
		
		$artist = "Unknown Artist";
		$title = "Untitled";

		foreach($metadata as $value) 
		{
			$dataPair = explode("=", $value);
			if($dataPair[0] == "title")
				$title = $dataPair[1];
			else if($dataPair[0] == "artist")
				$artist = $dataPair[1];
		}

		unlink($metadataFile);  //Commented out for debugging
	
		$builder = ProcessBuilder::create(array(
			$this->binary,
			'-y',
			'-i', $path
		));
		$process = $builder->getProcess();
		$process->setTimeout($this->timeout);

		$process->run();
		$output = $process->getErrorOutput();
		$temp;
		preg_match( "/(?<=Duration: ).*?(?=,)/", $output, $temp);
		$temp = explode(":", str_replace(".", ":", $temp[0]) );
		$duration = ( intval($temp[1])*60 + intval($temp[2]) )*1000 + intval($temp[3]);

		return array(
			'artist' => $artist,
			'title' => $title,
			'duration' => $duration
		);
	}
}
