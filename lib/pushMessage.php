<?php

defined('ABSPATH') OR die('Sorry, you can\'t do what you want to do !!.');

class PushMessage {
    // notification post id
    private $post_id;

    // notification title
    private $title;

    // notification message 
    private $message;

    // notification image url 
    private $image;

    private $post_date;
    private $post_modified;

    // function __construct($title, $message, $image, $post_id) {
    //     $this->title = $title;
    //     $this->message = $message; 
    //     $this->image = empty($image) ? null : $image;
    //     $this->post_id = $post_id; 
    // }

    function __construct($data=array()) {
        $this->post_id       = $data['post_id'];
        $this->title         = $data['title'];
        $this->image         = empty($data['image']) ? null : $data['image'];
        $this->message       = $data['message']; 
        $this->post_date     = $data['post_date']; 
        $this->post_modified = $data['post_modified']; 
    }
    
    // getting the push notification
    public function getNotification() {
        $res = array();
        $res['data']['post_id']       = $this->post_id;
        $res['data']['title']         = $this->title;
        $res['data']['message']       = $this->message;
        $res['data']['image']         = $this->image;
        $res['data']['post_date']     = $this->post_date;
        $res['data']['post_modified'] = $this->post_modified;
        return $res;
    }
}