RESTcomic web
=============
Frontend to the RESTcomic API. Doesn't actually use the `GET` routes though, because I found that to be really slow.

##Credits
* [Slim framework](http://www.slimframework.com/) for route handling
* [52framework](http://www.52framework.com/) for HTML/CSS templates
* [Simple Little Table CSS3](http://johnsardine.com/freebies/dl-html-css/simple-little-tab/) for the table CSS
* [JSColor](http://jscolor.com/) for group-name color picker

##Dependencies
* PHP>=5.3
* mySQL
* Apache with mod_rewrite enabled

##Installation
* Get RESTcomic working (see `api/README.md`)
* Upload to server
* Navigate over to `index.php` and log in as admin (assuming default RESTcomic setup, "admin"/"password")

##Considerations
I don't expect anyone to actually use this for any real applications, but consider the following:

* Everything in RESTcomic's `README.md` applies here as well
* The frontend is incredibly disorganized. If I had to do this over, I'd use a templating engine.
* Signed OAuth request URLs are made available in clickable links on the rendered site. Since RESTcomic doesn't consider nonces, this is even more of a security issue.
* Spaghetti code everywhere
    * Perhaps I'll come back to this at some point and do it properly, when it's not for a school project

