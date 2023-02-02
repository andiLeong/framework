![example workflow](https://github.com/andiLeong/framework/actions/workflows/php.yml/badge.svg)

# a simple mvc framework

a very simple php mvc framework that contains some well-known laravel components :

##### facades
##### cache ( include file , redis and array driver )
##### model - mini eloquent ( things like local scope , query builder, basic aggregate , paginator , attribute mutator and accessor, familiar interface (create,update,delete) are included )
##### configuration and env variable supported
##### database but only support mysql driver
##### service container
##### router
##### auto method/constructor injection
##### logger ( monolog )
##### simple exception handling
##### console command like ( php awesome serve )
##### testing - make fake request to own endpoint , make use of mysql transaction in test
##### validation and collection ( through my own package)
##### some arr and str helpers
##### pipeline and middleware
##### Authentication guard ( base token , jwt token)
##### Jwt token generator

of course, I rely on some third party package to get it done, like
symfony console.
symfony response
symfony finder
monolog

compare to laravel there are many components are missing just to name few
db migration
queue
file system

For a typical blog, simple features are all that you need.
The Reasons why I build this is over the times I have built my own laravel component why not I decided to put together.
not intent to use this in big project, but considering using for my blog haha
I did have tests to guide me when building but its not 100% test coverage