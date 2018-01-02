LM Social Functions Rest API 
===============

Wordpress plugin to enable basic social function throught rest-api: like e dislike. save post as favourite, follower and following. 

Just rest-api no front-end :). backoffice is improved showing like and saved counter in the post list. 

If you want to protect your api think about a JWT layer or modify access to rest-api only for logged users.

## Requirements

### WP REST API V2

This plugin was conceived to extend the [WP REST API V2](https://github.com/WP-API/WP-API) plugin features and, of course, was built on top of it.

So, to use the **wp-api-jwt-auth** you need to install and activate [WP REST API](https://github.com/WP-API/WP-API).

## Namespace and Endpoints

When the plugin is activated, a new namespace is added.


```
/lm-sf-rest-api/v1.0.0
```


New endpoints are added to this namespace.


Endpoint | HTTP Verb
--- | ---

*[/wp-json/lm-sf-rest-api/v1.0.0/like/add](#like-add)* | POST

*[/wp-json/lm-sf-rest-api/v1.0.0/like/remove](#like-remove)* | POST

*[/wp-json/lm-sf-rest-api/v1.0.0/like/toggle](#like-toggle)* | POST

*[/wp-json/lm-sf-rest-api/v1.0.0/posts/{post-id}/likes](#like-users)* | GET


*[/wp-json/lm-sf-rest-api/v1.0.0/saved/add](#saved-add)* | POST

*[/wp-json/lm-sf-rest-api/v1.0.0/saved/remove](#saved-remove)* | POST

*[/wp-json/lm-sf-rest-api/v1.0.0/saved/toggle](#saved-toggle)* | POST

*[/wp-json/lm-sf-rest-api/v1.0.0/posts/{post-id}/saved](#saved-users)* | GET


*[/wp-json/lm-sf-rest-api/v1.0.0/follower/add](#follower-add)* | POST

*[/wp-json/lm-sf-rest-api/v1.0.0/follower/remove](#follower-remove)* | POST

*[/wp-json/lm-sf-rest-api/v1.0.0/follower/toggle](#follower-toggle)* | POST


*[/wp-json/lm-sf-rest-api/v1.0.0/followers](#followers)* | GET

*[/wp-json/lm-sf-rest-api/v1.0.0/followings](#followings)* | GET

*[/wp-json/lm-sf-rest-api/v1.0.0/followers/count](#followers-count)* | GET

*[/wp-json/lm-sf-rest-api/v1.0.0/followings/count](#followings-count)* | GET


*[/wp-json/lm-sf-rest-api/v1.0.0/wall](#wall)* | GET

*[/wp-json/lm-sf-rest-api/v1.0.0/wall](#wall-post-create)* | POST

*[/wp-json/lm-sf-rest-api/v1.0.0/wall/{post-id}](#wall-post)* | GET


## <a name="like-add"></a> /wp-json/lm-sf-rest-api/v1.0.0/like/add
Add user like to a specific post

METHOD: POST

Parameters mandatory are:
- user_id
- post_id

JSON Response:
```
{
 "status":true
}
```
```
{
 "status":false
}
```

## <a name="like-remove"></a> /wp-json/lm-sf-rest-api/v1.0.0/like/remove
Remove user like to a specific post

METHOD: POST

Parameters mandatory are:
- user_id
- post_id

JSON Response:
```
{
 "status":true
}
```

```
{
 "status":false
}
```

## <a name="like-toggle"></a> /wp-json/lm-sf-rest-api/v1.0.0/like/toggle
Toggle like preferece to a specific post

METHOD: POST

Parameters mandatory are:
- user_id
- post_id

JSON Response:
```
{
 "status":true,
 "data":0
}
```
*data=0 if like was removed, data=1 if like was added

```
{
 "status":false
}
```

## <a name="like-users"></a> /wp-json/lm-sf-rest-api/v1.0.0/posts/{post_id}/likes
users list like a post

METHOD: GET

Parameters mandatory are:
- post_id

JSON Response:
```
{
  "status": true,
  "data": [
    {
      "ID": "1",
      "user_login": "playdoc-admin",
      "display_name": "playdoc-admin",
      "user_email": "testtest@gmail.com",
      "user_registered": "2017-10-05 07:10:31",
      "user_status": "0",
      "user_picture": "http:\/\/playdoc.dev\/cn\/uploads\/axe-accounts-profile\/1\/ab1fff92440ebe1791c03ad02af1119b.jpg"
    },
    {
      "ID": "3",
      "user_login": "playdoc-editor",
      "display_name": "playdoc editor",
      "user_email": "playdoc.editor@gmail.com",
      "user_registered": "2017-10-16 15:26:18",
      "user_status": "0",
      "user_picture": "http:\/\/playdoc.dev\/cn\/uploads\/axe-accounts-profile\/34eaf2d938447267bcd801e89a167226.png"
    }
  ]
}
```


## <a name="saved-add"></a> /wp-json/lm-sf-rest-api/v1.0.0/saved/add
Save post as favourite for an user

METHOD: POST

Parameters mandatory are:
- user_id
- post_id

JSON Response:
```
{
 "status":true
}
```

```
{
 "status":false
}
```


## <a name="saved-remove"></a> /wp-json/lm-sf-rest-api/v1.0.0/saved/remove
Remove post as favourite for an user

METHOD: POST

Parameters mandatory are:
- user_id
- post_id

JSON Response:
```
{
 "status":true
}
```

```
{
 "status":false
}
```


## <a name="saved-toggle"></a> /wp-json/lm-sf-rest-api/v1.0.0/saved/toggle
Toogle favourite preference for a post

METHOD: POST

Parameters mandatory are:
- user_id
- post_id

JSON Response:
```
{
 "status":true,
 "data":1
}
```
*data=0 if saved was removed, data=1 if saved was added

```
{
 "status":false
}
```

## <a name="saved-users"></a> /wp-json/lm-sf-rest-api/v1.0.0/posts/{post_id}/saved
users list saved a post

METHOD: GET

Parameters mandatory are:
- post_id

JSON Response:
```
{
  "status": true,
  "data": [
    {
      "ID": "1",
      "user_login": "playdoc-admin",
      "display_name": "playdoc-admin",
      "user_email": "testtest@gmail.com",
      "user_registered": "2017-10-05 07:10:31",
      "user_status": "0",
      "user_picture": "http:\/\/playdoc.dev\/cn\/uploads\/axe-accounts-profile\/1\/ab1fff92440ebe1791c03ad02af1119b.jpg"
    },
    {
      "ID": "3",
      "user_login": "playdoc-editor",
      "display_name": "playdoc editor",
      "user_email": "playdoc.editor@gmail.com",
      "user_registered": "2017-10-16 15:26:18",
      "user_status": "0",
      "user_picture": "http:\/\/playdoc.dev\/cn\/uploads\/axe-accounts-profile\/34eaf2d938447267bcd801e89a167226.png"
    }
  ]
}
```


## <a name="follower-add"></a> /wp-json/lm-sf-rest-api/v1.0.0/follower/add
Add an user A as follower of user B

METHOD: POST

Parameters mandatory are:
- follower_id
- following_id

JSON Response:
```
{
 "status":true
}
```

```
{
 "status":false
}
```


## <a name="follower-remove"></a> /wp-json/lm-sf-rest-api/v1.0.0/follower/remove
Remove an user A as follower of user B

METHOD: POST

Parameters mandatory are:
- follower_id
- following_id

JSON Response:
```
{
 "status":true
}
```

```
{
 "status":false
}
```

## <a name="follower-toggle"></a> /wp-json/lm-sf-rest-api/v1.0.0/follower/toggle
Toggle following preference for user A as follower of user B

Parameters mandatory are:
- follower_id
- following_id

JSON Response:
```
{
 "status":true,
 "data":1
}
```
*data=0 if follower was removed, data=1 if follower was added

```
{
 "status":false
}
```


## <a name="followers"></a> /wp-json/lm-sf-rest-api/v1.0.0/followers
Return list of users following a specific user

METHOD: GET

Parameters mandatory are:
- following_id

Optional parameters
- page (default = 1)
- item_per_page (default = 20)
- before (default = null, e.g. 2018-12-31 23:34:12)

JSON Response
```
{
 "status":true,
 "data":[
   {
     "ID":"1",
     "user_login":"playdoc-admin",
     "display_name":"playdoc-admin",
     "user_email":"lcmaroni77@gmail.com",
     "user_registered":"2017-10-05 07:10:31",
     "user_status":"0"
   },
   {
     "ID":"2",
     "user_login":"playdoc-test",
     "display_name":"playdoc-test",
     "user_email":"lcmaron.i77@gmail.com",
     "user_registered":"2017-10-05 09:10:31",
     "user_status":"0"
   }   
 ],
 "total":"2",
 "page":1,
 "item_per_page":20,
 "time_server":"2018-12-31 23:34:12" 
}
```

```
{
 "status":false
}
```

## <a name="followings"></a> /wp-json/lm-sf-rest-api/v1.0.0/followings
Return list of users followed by specific user

METHOD: GET

Parameters mandatory are:
- follower_id

Optional parameters
- page (default = 1)
- item_per_page (default = 20)
- before (default = null, e.g. 2018-12-31 23:34:12)

JSON Response
```
{
 "status":true,
 "data":[
   {
     "ID":"1",
     "user_login":"playdoc-admin",
     "display_name":"playdoc-admin",
     "user_email":"lcmaroni77@gmail.com",
     "user_registered":"2017-10-05 07:10:31",
     "user_status":"0"
   },
   {
     "ID":"2",
     "user_login":"playdoc-test",
     "display_name":"playdoc-test",
     "user_email":"lcmaron.i77@gmail.com",
     "user_registered":"2017-10-05 09:10:31",
     "user_status":"0"
   }   
 ],
 "total":"2",
 "page":1,
 "item_per_page":20,
 "time_server":"2018-12-31 23:34:12"
}
```

```
{
 "status":false
}
```

## <a name="followers-count"></a> /wp-json/lm-sf-rest-api/v1.0.0/followers/count
Return the number of user follwing an user

METHOD: GET

Parameters mandatory are:
- following_id

JSON Response
```
{
  "status": true,
  "data": "1"
}
```

## <a name="followings-count"></a> /wp-json/lm-sf-rest-api/v1.0.0/followings/count
Return the number of user followed by an user

METHOD: GET

Parameters mandatory are:
- follower_id

JSON Response
```
{
  "status": true,
  "data": "1"
}
```

## <a name="wall"></a> /wp-json/lm-sf-rest-api/v1.0.0/wall
Return posts published on wall

METHOD: GET

Parameters NOT mandatory are:
- item_per_page
- page
- categories
- authors
- before
- q

JSON Response
```
{
  "status": true,
  "data": [
    {
      "ID": 12,
      "post_author": "1",
      "post_date": "2017-10-16 17:25:41",
      "post_date_gmt": "2017-10-16 15:25:41",
      "post_content": "questo \u00e8 un post per un game!!",
      "post_title": "prova gioco",
      "post_excerpt": "",
      "post_status": "publish",
      "comment_status": "open",
      "ping_status": "open",
      "post_password": "",
      "post_name": "prova-gioco",
      "to_ping": "",
      "pinged": "",
      "post_modified": "2017-10-16 17:25:41",
      "post_modified_gmt": "2017-10-16 15:25:41",
      "post_content_filtered": "",
      "post_parent": 0,
      "guid": "http:\/\/playdoc.luc\/?p=12",
      "menu_order": 0,
      "post_type": "post",
      "post_mime_type": "",
      "comment_count": "0",
      "filter": "raw",
      "post_content_rendered": "<p>questo \u00e8 un post per un game!!<\/p>\n",
      "post_excerpt_rendered": "",
      "author": {
        "ID": "1",
        "user_login": "playdoc-admin",
        "display_name": "playdoc-admin",
        "user_email": "lcmaroni77@gmail.com",
        "user_registered": "2017-10-05 07:10:31",
        "user_status": "0"
      },
      "latest_comment": [],
      "categories": [],
      "wall_categories": [
        {
          "term_id": 3,
          "name": "Game",
          "slug": "game",
          "term_group": 0,
          "term_taxonomy_id": 3,
          "taxonomy": "category",
          "description": "",
          "parent": 0,
          "count": 1,
          "filter": "raw"
        }
      ],
      "lm_like_counter": 0,
      "lm_saved_counter": 0,
      "featured_image": false,
      "liked": false,
      "saved": false
    },
    {
      "ID": 1,
      "post_author": "1",
      "post_date": "2017-10-05 09:10:31",
      "post_date_gmt": "2017-10-05 07:10:31",
      "post_content": "Benvenuto in WordPress. Questo \u00e8 il tuo primo articolo. Modificalo o eliminalo, e inizia a creare il tuo blog!",
      "post_title": "Ciao mondo!",
      "post_excerpt": "",
      "post_status": "publish",
      "comment_status": "open",
      "ping_status": "open",
      "post_password": "",
      "post_name": "ciao-mondo",
      "to_ping": "",
      "pinged": "",
      "post_modified": "2017-10-12 09:20:00",
      "post_modified_gmt": "2017-10-12 07:20:00",
      "post_content_filtered": "",
      "post_parent": 0,
      "guid": "http:\/\/playdoc.luc\/?p=1",
      "menu_order": 0,
      "post_type": "post",
      "post_mime_type": "",
      "comment_count": "6",
      "filter": "raw",
      "post_content_rendered": "<p>Benvenuto in WordPress. Questo \u00e8 il tuo primo articolo. Modificalo o eliminalo, e inizia a creare il tuo blog!<\/p>\n",
      "post_excerpt_rendered": "",
      "author": {
        "ID": "1",
        "user_login": "playdoc-admin",
        "display_name": "playdoc-admin",
        "user_email": "lcmaroni77@gmail.com",
        "user_registered": "2017-10-05 07:10:31",
        "user_status": "0"
      },
      "latest_comment": [
        {
          "comment_ID": "4",
          "comment_post_ID": "1",
          "comment_author": "playdoc-admin",
          "comment_author_email": "lcmaroni77@gmail.com",
          "comment_author_url": "",
          "comment_author_IP": "127.0.0.1",
          "comment_date": "2017-10-23 12:32:35",
          "comment_date_gmt": "2017-10-23 10:32:35",
          "comment_content": "quarto commento. riciao!",
          "comment_karma": "0",
          "comment_approved": "1",
          "comment_agent": "Mozilla\/5.0 (Macintosh; Intel Mac OS X 10_12_6) AppleWebKit\/537.36 (KHTML, like Gecko) Chrome\/61.0.3163.100 Safari\/537.36",
          "comment_type": "",
          "comment_parent": "0",
          "user_id": "1"
        },
        {
          "comment_ID": "5",
          "comment_post_ID": "1",
          "comment_author": "playdoc-admin",
          "comment_author_email": "lcmaroni77@gmail.com",
          "comment_author_url": "",
          "comment_author_IP": "127.0.0.1",
          "comment_date": "2017-10-23 12:32:44",
          "comment_date_gmt": "2017-10-23 10:32:44",
          "comment_content": "quinto ... quanti commenti!",
          "comment_karma": "0",
          "comment_approved": "1",
          "comment_agent": "Mozilla\/5.0 (Macintosh; Intel Mac OS X 10_12_6) AppleWebKit\/537.36 (KHTML, like Gecko) Chrome\/61.0.3163.100 Safari\/537.36",
          "comment_type": "",
          "comment_parent": "0",
          "user_id": "1"
        },
        {
          "comment_ID": "6",
          "comment_post_ID": "1",
          "comment_author": "playdoc-admin",
          "comment_author_email": "lcmaroni77@gmail.com",
          "comment_author_url": "",
          "comment_author_IP": "127.0.0.1",
          "comment_date": "2017-10-23 12:32:54",
          "comment_date_gmt": "2017-10-23 10:32:54",
          "comment_content": "e ora il sesto! ciao %&\/()=P\u00e7L;:; Luca",
          "comment_karma": "0",
          "comment_approved": "1",
          "comment_agent": "Mozilla\/5.0 (Macintosh; Intel Mac OS X 10_12_6) AppleWebKit\/537.36 (KHTML, like Gecko) Chrome\/61.0.3163.100 Safari\/537.36",
          "comment_type": "",
          "comment_parent": "0",
          "user_id": "1"
        }
      ],
      "categories": [],
      "wall_categories": [
        {
          "term_id": 2,
          "name": "Bacheca",
          "slug": "bacheca",
          "term_group": 0,
          "term_taxonomy_id": 2,
          "taxonomy": "category",
          "description": "",
          "parent": 0,
          "count": 2,
          "filter": "raw"
        }
      ],
      "lm_like_counter": 0,
      "lm_saved_counter": "1",
      "featured_image": "http:\/\/playdoc.luc\/wp\/..\/cn\/uploads\/2017\/10\/girasole-r100.jpg",
      "liked": false,
      "saved": true
    }
  ]
}
```

## <a name="wall-post-create"></a> /wp-json/lm-sf-rest-api/v1.0.0/wall
Create a new post

METHOD: POST

Parameters mandatory are:
- content: {contenuto del commento}
- author: {user id}
- status: publish
- categories: Bacheca (la categoria viene indicata con una stringa)
- format: standard (standard/image/video/link)

Parameters NOT mandatory are:
- shared_post: {post id} (id del post che stiamo condividendo)

JSON Response: se il post viene creato correttamente il json contiene le informazioni del post appena creato.


## <a name="wall-post"></a> /wp-json/lm-sf-rest-api/v1.0.0/wall/{post-id}
Return details for a single post with all the comments linked to the post

METHOD: GET

There are no parameters associated to this endpoint

JSON Response
```
{
  "status": true,
  "data": {
    "ID": 1,
    "post_author": "1",
    "post_date": "2017-10-05 09:10:31",
    "post_date_gmt": "2017-10-05 07:10:31",
    "post_content": "Benvenuto in WordPress. Questo \u00e8 il tuo primo articolo. Modificalo o eliminalo, e inizia a creare il tuo blog!",
    "post_title": "Ciao mondo!",
    "post_excerpt": "",
    "post_status": "publish",
    "comment_status": "open",
    "ping_status": "open",
    "post_password": "",
    "post_name": "ciao-mondo",
    "to_ping": "",
    "pinged": "",
    "post_modified": "2017-10-12 09:20:00",
    "post_modified_gmt": "2017-10-12 07:20:00",
    "post_content_filtered": "",
    "post_parent": 0,
    "guid": "http:\/\/playdoc.luc\/?p=1",
    "menu_order": 0,
    "post_type": "post",
    "post_mime_type": "",
    "comment_count": "1",
    "filter": "raw",
    "post_content_rendered": "<p>Benvenuto in WordPress. Questo \u00e8 il tuo primo articolo. Modificalo o eliminalo, e inizia a creare il tuo blog!<\/p>\n",
    "post_excerpt_rendered": "",
    "author": {
      "ID": "1",
      "user_login": "playdoc-admin",
      "display_name": "playdoc-admin",
      "user_email": "lcmaroni77@gmail.com",
      "user_registered": "2017-10-05 07:10:31",
      "user_status": "0"
    },
    "latest_comment": [
      {
        "comment_ID": "6",
        "comment_post_ID": "1",
        "comment_author": "playdoc-admin",
        "comment_author_email": "lcmaroni77@gmail.com",
        "comment_author_url": "",
        "comment_author_IP": "127.0.0.1",
        "comment_date": "2017-10-23 12:32:54",
        "comment_date_gmt": "2017-10-23 10:32:54",
        "comment_content": "e ora il sesto! ciao %&\/()=P\u00e7L;:; Luca",
        "comment_karma": "0",
        "comment_approved": "1",
        "comment_agent": "Mozilla\/5.0 (Macintosh; Intel Mac OS X 10_12_6) AppleWebKit\/537.36 (KHTML, like Gecko) Chrome\/61.0.3163.100 Safari\/537.36",
        "comment_type": "",
        "comment_parent": "0",
        "user_id": "1"
      }
    ],
    "categories": [],
    "wall_categories": [
      {
        "term_id": 2,
        "name": "Bacheca",
        "slug": "bacheca",
        "term_group": 0,
        "term_taxonomy_id": 2,
        "taxonomy": "category",
        "description": "",
        "parent": 0,
        "count": 2,
        "filter": "raw"
      }
    ],    
    "lm_like_counter": 0,
    "lm_saved_counter": "1",
    "featured_image": "http:\/\/playdoc.luc\/wp\/..\/cn\/uploads\/2017\/10\/girasole-r100.jpg",
    "liked": false,
    "saved": true
  }
}
```