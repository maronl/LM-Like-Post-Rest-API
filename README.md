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


Also, four new endpoints are added to this namespace.


Endpoint | HTTP Verb
--- | ---

*/wp-json/lm-sf-rest-api/v1.0.0/like/add* | POST

*/wp-json/lm-sf-rest-api/v1.0.0/like/remove* | POST

*/wp-json/lm-sf-rest-api/v1.0.0/saved/add* | POST

*/wp-json/lm-sf-rest-api/v1.0.0/saved/remove* | POST

*/wp-json/lm-sf-rest-api/v1.0.0/following/add* | POST

*/wp-json/lm-sf-rest-api/v1.0.0/following/remove* | POST
