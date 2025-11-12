# 📝 Project Notes & API Demo Setup

## 🔑 Demo Access and Roles

| User Type | Email | Password | Role | Notes |
| :--- | :--- | :--- | :--- | :--- |
| **Admin** | `admin@example.com` | `admin123` | `admin` | Has the ability to **edit or delete categories**. |
| **Standard User** | *(Various)* | *(Various)* | `user` | Standard user role in the database. |

## 📦 Demo Data

* The database **seeders** were used to populate the application with **demo data** including **posts, comments, and tags**.

---

## 🚀 API Access & Testing

### 🔑 Get Your API Token

A form is available to help users obtain an **API Token** for authenticated requests:

* **Endpoint:** `http://localhost:8000/api-auth.html`

### 💻 API Endpoint Test Code (PHP/cURL) - You can find the API endpoints documentation at this file: API-DOCUMENTATION.html

The following PHP script demonstrates how to interact with the API endpoints using `cURL`. **Remember to replace the placeholder token** with one generated from the link above.

```php
<?php
$apiBase = 'http://localhost:8000/api';
$token = 'YOUR_API_TOKEN'; // Replace with your login token

function callApi($url, $method='GET', $data=null, $token=null){
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    $headers = ['Content-Type: application/json','Accept: application/json'];
    if($token) $headers[] = 'Authorization: Bearer ' . $token;
    
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

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
        $responseData = json_decode($response, true);
        print_r($responseData);
    }
    curl_close($ch);
}

/**
 ** TEST ENDPOINTS
 ** uncomment the desired function call for testing
 **/

//**list all posts
$url = $apiBase . '/posts';
//callApi($url, 'GET', null, $token);


//**create a new post
$data = [
    'title' => 'My New Post',
    'slug' => 'my-new-post',
    'content' => 'This is the content of the post.',
    'category_id' => 1,
    'tags' => [3,4] // array of tag IDs
];
//callApi($apiBase.'/posts', 'POST', $data, $token);

//**get a post by id and slug
$postId = 2;
$slug = 'itaque-libero-iusto-et-possimus-vel-deleniti-voluptatem-361';
//callApi($apiBase."/posts/{$postId}/{$slug}", 'GET', null, $token);

//** update a post by id
$postId = 1;
$data = [
    'title' => 'Updated Post Title',
    'content' => 'Updated content here.',
    'tags' => [5] // updated tags
];
//callApi($apiBase."/posts/{$postId}", 'PUT', $data, $token);

//**delete a post by id
$postId = 3;
//callApi($apiBase."/posts/{$postId}", 'DELETE', null, $token);

//**create a new comment for post by id
$postId = 36;
$data = [
    'content' => 'This is a comment on the post.'
];
//callApi($apiBase."/posts/{$postId}/comments", 'POST', $data, $token);

//**list posts by user id
$userId = 1;
//callApi($apiBase."/users/{$userId}/posts", 'GET', null, $token);

//**list comments by user id
$userId = 18;
//callApi($apiBase."/users/{$userId}/comments", 'GET', null, $token);

//** return all categories
callApi($apiBase."/categories", 'GET', null, $token);