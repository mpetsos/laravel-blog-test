<?php
$apiBase = 'http://localhost:8000/api';
$token = '9|K4n2c84sS0jsH0sPAFNWIA5nQDUdzeeM9ZzNtOza577c41ac'; // Replace with login token

function callApi($url, $method='GET', $data=null, $token=null){
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    $headers = ['Content-Type: application/json','Accept: application/json'];
    if($token) $headers[] = 'Authorization: Bearer ' . $token;

    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
echo json_encode($data);
    if($method === 'POST'){
        curl_setopt($ch, CURLOPT_POST, true);
        if($data) curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    } elseif($method === 'PUT' || $method === 'PATCH'){
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        if($data) curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    } elseif($method === 'DELETE'){
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
    }

    $response = curl_exec($ch);
    if(curl_errno($ch)){
        echo 'Error:' . curl_error($ch);
    } else {
       // $responseData = json_decode($response, true);///php array
        echo $response;
    }
    curl_close($ch);
}

/**
** TEST ENDPOINTS
** uncomment the endpoint for test
**/

//**list all posts
$url = $apiBase . '/posts';
//callApi($url, 'GET', null, $token);


//**create a new post
$data = [
    'title' => 'My New Post test',
    'slug' => 'my-new-post-test',
    'content' => 'This is the content of the post.',
    'category_id' => 1,
    'tags' => [3,4] // array of tag IDs
];
//callApi($apiBase.'/posts', 'POST', $data, $token);

//**get a post by id and slug
$postId = 233;
$slug = 'itaque-libero-iusto-et-possimus-vel-deleniti-voluptatem-361';
//callApi($apiBase."/posts/{$postId}/{$slug}", 'GET', null, $token);

//** update a post by id
$postId = 7;
$data = [
    'title' => 'Updated Post Title new',
    'content' => 'Updated content here.',
    'tags' => [5] // updated tags
];
callApi($apiBase."/posts/{$postId}", 'PUT', $data, $token);

//**delete a post by id
$postId = 7;
//callApi($apiBase."/posts/{$postId}", 'DELETE', null, $token);

//**create a new comment for post by id
$postId = 1;
$data = [
    'content' => 'This is a comment on the post.'
];
//callApi($apiBase."/posts/{$postId}/comments", 'POST', $data, $token);

//**list posts by user id
$userId = 1888;
//callApi($apiBase."/users/{$userId}/posts", 'GET', null, $token);

//**list comments by user id
$userId = 1;
//callApi($apiBase."/users/{$userId}/comments", 'GET', null, $token);

//** return all categories
//callApi($apiBase."/categories", 'GET', null, $token);



