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

*/wp-json/lm-sf-rest-api/v1.0.0/like/add* | POST

*/wp-json/lm-sf-rest-api/v1.0.0/like/remove* | POST


*/wp-json/lm-sf-rest-api/v1.0.0/saved/add* | POST

*/wp-json/lm-sf-rest-api/v1.0.0/saved/remove* | POST


*/wp-json/lm-sf-rest-api/v1.0.0/follower/add* | POST

*/wp-json/lm-sf-rest-api/v1.0.0/follower/remove* | POST

*/wp-json/lm-sf-rest-api/v1.0.0/followers* | GET

*/wp-json/lm-sf-rest-api/v1.0.0/followings* | GET

*/wp-json/lm-sf-rest-api/v1.0.0/followers/count* | GET

*/wp-json/lm-sf-rest-api/v1.0.0/followings/count* | GET


*/wp-json/lm-sf-rest-api/v1.0.0/wall* | GET



##/wp-json/lm-sf-rest-api/v1.0.0/like/add
Add user like to a specific post

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

##/wp-json/lm-sf-rest-api/v1.0.0/like/remove
Remove user like to a specific post

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

##/wp-json/lm-sf-rest-api/v1.0.0/like/toggle
Toggle like preferece to a specific post
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

##/wp-json/lm-sf-rest-api/v1.0.0/saved/add
Save post as favourite for an user

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


##/wp-json/lm-sf-rest-api/v1.0.0/saved/remove
Remove post as favourite for an user

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


##/wp-json/lm-sf-rest-api/v1.0.0/saved/toggle
Toogle favourite preference for a post

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


##/wp-json/lm-sf-rest-api/v1.0.0/follower/add
Add an user A as follower of user B

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


##/wp-json/lm-sf-rest-api/v1.0.0/follower/remove
Remove an user A as follower of user B

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

##/wp-json/lm-sf-rest-api/v1.0.0/follower/toggle
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


##/wp-json/lm-sf-rest-api/v1.0.0/followers
Return list of users following a specific user

Parameters mandatory are:
- following_id

Optional parameters
- page (default = 20)
- item_per_page (default = 1)

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
 "item_per_page":20
}
```

```
{
 "status":false
}
```

##/wp-json/lm-sf-rest-api/v1.0.0/followings
Return list of users followed by specific user

Parameters mandatory are:
- follower_id

Optional parameters
- page (default = 20)
- item_per_page (default = 1)

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
 "item_per_page":20
}
```

```
{
 "status":false
}
```

##/wp-json/lm-sf-rest-api/v1.0.0/followers/count
Parameters mandatory are:
- following_id

##/wp-json/lm-sf-rest-api/v1.0.0/followings/count
Parameters mandatory are:
- follower_id


##/wp-json/lm-sf-rest-api/v1.0.0/wall
Parameters NOT mandatory are:
- item_per_page
- page
- categories
- authors
