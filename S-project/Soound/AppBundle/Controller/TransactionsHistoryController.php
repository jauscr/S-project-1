<?php

namespace Soound\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Soound\AppBundle\Document\Project;
use Soound\AppBundle\Document\Transaction;
use Soound\AppBundle\Document\User;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * 
 */
class TransactionsHistoryController extends Controller
{
	/**
	 * [indexAction description]
	 * @param  [type] $filename [description]
	 * @return [type]           [description]
	 */
	public function indexAction(){
		$startDate = date_create()->sub(new \DateInterval("P1M"));
		$endDate = date_create();
		$user = $this->user();

		//$this->createTestTransactons(10);

		return $this->render(
			"SooundAppBundle:Html:transactions-history.html.twig",
			array(
				"earned" => $user->getTotalEarned(),
				"spent" => $user->getTotalSpent(),
				"months" => $this->getTransactionsBetween($startDate, $endDate),
				"start" => date_format($startDate, "j / n / Y"),
				"end" => date_format($endDate, "j / n / Y")
			)
		);
		
	}

	private function user(){
		$user = $this->get('security.context')->getToken()->getUser();
		return $user;
	}

	private function createTestTransactons($howMany){
		$dm = $this->get('doctrine_mongodb')->getManager();

		$outgoing = true;

		$user = $this->user();
		$user->setTotalSpent(0);
		$user->setTotalEarned(0);
		$project = $dm->find('SooundAppBundle:Project', '523de0893590357002000000');

		for($i=0; $i<$howMany; $i++){

			$transaction = new Transaction();

			$transaction->setProject($project);
			$transaction->addUser($user);
			if($outgoing){
				$user->addTotalSpent( ($i+1)*100);
				$transaction->setFromUser($user);
				$outgoing = false;
			}
			else{
				$user->addTotalEarned(($i+1)*100);
				$transaction->setToUser($user);
				$outgoing = true;
			}
			
			$transaction->setAmount(($i+1)*100);
			$interval = new \DateInterval( "P".(7*$i)."D" );
			$transaction->setDate( date_sub( date_create(), $interval ) );

			$dm->persist($transaction);
		}
		$dm->flush();
	}

	private function getTransactionsBetween( $startDate, $endDate ){
		$dm = $this->get('doctrine_mongodb')->getManager();
		$user = $this->user();

		$query = $dm->createQueryBuilder('SooundAppBundle:Transaction')
					->field("users")->includesReferenceTo($user)
					->field("date")->gte( $startDate )
					->field("date")->lte( $endDate )
					->sort("date", "asc")
					->getQuery()
					->execute();

		$count = 0;
		$months = array();
		$currMonth = array("date" => "0", "transactions" => array());

		foreach ($query as $transaction) {
			if( $transaction->getFromUser() === $user ){
				$outgoing = true;
				//$otherUserName = $transaction->getToUser()->getFullName();
			}
			else{
				$outgoing = false;
				//$otherUserName = $transaction->getFromUser()->getFullName();
			}

			$date = date_format( $transaction->getDate(), "F'y" ); 

			$transaction = array(
				"outgoing" => $outgoing,
				//"otherUser" => $otherUserName,
				"amount" => $transaction->getAmount(),
				"date" => date_format( $transaction->getDate(), "l, jS" ),
				"projectTitle" => $transaction->getProject()->getProjectName()
			);

			if( $date != $currMonth["date"] ){
				if( $count != 0 ){
					array_push( $months, $currMonth );
					$count = 0;
				}
				$currMonth = array( "date" => $date, "transactions" => array() );
			}
			array_push( $currMonth["transactions"], $transaction );
			$count++;
		}

		if( $count > 0 )
			array_push( $months, $currMonth );

		return $months;
	}

	public function getTransactionsAction(){
		$startDate = date_create( $_POST['startDate'] );
		$endDate = date_create( $_POST['endDate'].' 23:59:59' );
		$transactions = $this->getTransactionsBetween( $startDate, $endDate );

		return new Response( json_encode($transactions) );
	}

}
