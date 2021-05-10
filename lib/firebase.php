<?php

defined('ABSPATH') or die('Sorry, you can\'t do what you want to do !!.');

class Firebase{

	public function send($registration_ids, $message){
		$fields = array(
			'registration_ids' => array($registration_ids),
			'data' => $message,
		);
		return $this->sendPushNotification($fields);
	}

	public function sendToTopicSubscribers($message, $topics=array()){
		// echo "<pre>";
		// print_r($topics);
		// die();
		if (count($topics) == 0)
			return;
		
		foreach ($topics as $topic) {
			$fields = array(
				'to' => '/topics/'.$topic,
				'data' => $message,
			);

			$this->sendPushNotification($fields);
		}
	}

	// curl request to firebase server
	private function sendPushNotification($fields){

		$testing_token = 'AAAARF4mq64:APA91bHIwWR3Vfuy4S-CYpXPv52bYMf95ygkvKXLXpG4CdRoKsicvVwtPg_oqvU5oE-_z3iSw5XCviXEqAFNbRrS3emWgIjPdGxylisV73vIVNVZ2Dms3ktsgaaH9vkaduBSMTIbROME';
		$fcm_key = esc_attr(get_option('FCM_ENVIRONMENT')) === 'production' ? esc_attr(get_option('FIREBASE_API_KEY')) : $testing_token;

		$args = array(
			'timeout'		=> 45,
			'redirection'	=> 5,
			'httpversion'	=> '1.1',
			'method' 		=> 'POST',
			'body' 			=> json_encode($fields),
			'sslverify'   	=> false,
			'headers' 		=> array(
				'Content-Type' => 'application/json',
				'Authorization' => 'key=' . $fcm_key,
			),
			'cookies' 		=> array()
		);


		$response = wp_remote_post('https://fcm.googleapis.com/fcm/send', $args);

		return $response;
	}
}