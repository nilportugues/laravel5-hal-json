# Laravel 5 HAL+JSON 

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/nilportugues/laravel5-hal-json-transformer/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/nilportugues/laravel5-hal-json-transformer/?branch=master) [![SensioLabsInsight](https://insight.sensiolabs.com/projects/93029d8e-7052-42e0-a7db-fabbd2e566d5/mini.png?)](https://insight.sensiolabs.com/projects/93029d8e-7052-42e0-a7db-fabbd2e566d5) 
[![Latest Stable Version](https://poser.pugx.org/nilportugues/laravel5-haljson/v/stable?)](https://packagist.org/packages/nilportugues/laravel5-haljson) 
[![Total Downloads](https://poser.pugx.org/nilportugues/laravel5-haljson/downloads?)](https://packagist.org/packages/nilportugues/laravel5-haljson) 
[![License](https://poser.pugx.org/nilportugues/laravel5-haljson/license?)](https://packagist.org/packages/nilportugues/laravel5-haljson) 
[![Donate](https://www.paypalobjects.com/en_US/i/btn/btn_donate_SM.gif)](https://paypal.me/nilportugues)

1. [Installation](#1-installation)
2. [Configuration](#2-configuration)
3. [Mapping](#3-mapping)
    - 3.1 [Mapping with arrays](#31-mapping-with-arrays)
    - 3.2 [Mapping with Mapping class](#32-mapping-with-mapping-class)
4. [HAL Serialization](#4-hal-serialization)
5. [HAL Paginated Resource](#5-hal-paginated-resource)
6. [PSR-7 Response Objects](#6-response-objects)

## 1. Installation

Use [Composer](https://getcomposer.org) to install the package:

```
$ composer require nilportugues/laravel5-haljson
```

## 2. Configuration

Open up `config/app.php` and add the following line under `providers` array:

```php
'providers' => [

    //...
    \NilPortugues\Laravel5\HalJson\Laravel5HalJsonServiceProvider::class,
],
```

Also, enable Facades by uncommenting:

```php
$app->withFacades();
```

## 3. Mapping

For instance, lets say the following object has been fetched from a Repository , lets say `PostRepository` - this being implemented in Eloquent or whatever your flavour is:

```php
use Acme\Domain\Dummy\Post;
use Acme\Domain\Dummy\ValueObject\PostId;
use Acme\Domain\Dummy\User;
use Acme\Domain\Dummy\ValueObject\UserId;
use Acme\Domain\Dummy\Comment;
use Acme\Domain\Dummy\ValueObject\CommentId;

//$postId = 9;
//PostRepository::findById($postId); 

$post = new Post(
  new PostId(9),
  'Hello World',
  'Your first post',
  new User(
      new UserId(1),
      'Post Author'
  ),
  [
      new Comment(
          new CommentId(1000),
          'Have no fear, sers, your king is safe.',
          new User(new UserId(2), 'Barristan Selmy'),
          [
              'created_at' => (new \DateTime('2015/07/18 12:13:00'))->format('c'),
              'accepted_at' => (new \DateTime('2015/07/19 00:00:00'))->format('c'),
          ]
      ),
  ]
);
```

We will have to map all the involved classes. This can be done as one single array, or a series of Mapping classes.

Also we will require to have routes. The routes must be named routes. 

For instance our `app/Http/routes.php` file contains the following routes: 

```php
Route::get(
  '/docs/rels/{rel}',
  ['as' => 'get_example_curie_rel', 'uses' => 'ExampleCurieController@getRelAction']
);


Route::get(
  '/post/{postId}',
  ['as' => 'get_post', 'uses' => 'PostController@getPostAction']
);

Route::get(
  '/post/{postId}/comments',
  ['as' => 'get_post_comments', 'uses' => 'CommentsController@getPostCommentsAction']
);

//...
```


### 3.1 Mapping with arrays

Create a `haljson.php` file in `config/` directory. This file should return an array returning all the class mappings.

And a series of mappings, placed in `bootstrap/haljson.php`, that require to use *named routes* so we can use the `route()` helper function:

```php
<?php
//bootstrap/haljson.php
return [
    [
        'class' => 'Acme\Domain\Dummy\Post',
        'alias' => 'Message',
        'aliased_properties' => [
            'author' => 'author',
            'title' => 'headline',
            'content' => 'body',
        ],
        'hide_properties' => [

        ],
        'id_properties' => [
            'postId',
        ],
        'urls' => [
            'self' => ['name' => 'get_post'], //named route
            'comments' => ['name' => 'get_post_comments'],//named route
        ],
        'curies' => [
            'name' => 'example',
            'href' => "http://example.com/docs/rels/{rel}",
        ]
    ],
    [
        'class' => 'Acme\Domain\Dummy\ValueObject\PostId',
        'alias' => '',
        'aliased_properties' => [],
        'hide_properties' => [],
        'id_properties' => [
            'postId',
        ],
        'urls' => [
            'self' => ['name' => 'get_post'],//named route
        ],
        'curies' => [
            'name' => 'example',
            'href' => "http://example.com/docs/rels/{rel}",
        ]
    ],
    [
        'class' => 'Acme\Domain\Dummy\User',
        'alias' => '',
        'aliased_properties' => [],
        'hide_properties' => [],
        'id_properties' => [
            'userId',
        ],
        'urls' => [
            'self' => ['name' => 'get_user'],//named route
            'friends' => ['name' => 'get_user_friends'],//named route
            'comments' => ['name' => 'get_user_comments'],//named route
        ],
        'curies' => [
            'name' => 'example',
            'href' => "http://example.com/docs/rels/{rel}",
        ]
    ],
    [
        'class' => 'Acme\Domain\Dummy\ValueObject\UserId',
        'alias' => '',
        'aliased_properties' => [],
        'hide_properties' => [],
        'id_properties' => [
            'userId',
        ],
        'urls' => [
            'self' => ['name' => 'get_user'],//named route
            'friends' => ['name' => 'get_user_friends'],//named route
            'comments' => ['name' => 'get_user_comments'],//named route
        ],
        'curies' => [
            'name' => 'example',
            'href' => "http://example.com/docs/rels/{rel}",
        ]
    ],
    [
        'class' => 'Acme\Domain\Dummy\Comment',
        'alias' => '',
        'aliased_properties' => [],
        'hide_properties' => [],
        'id_properties' => [
            'commentId',
        ],
        'urls' => [
            'self' => ['name' => 'get_comment'],//named route
        ],
        'curies' => [
            'name' => 'example',
            'href' => "http://example.com/docs/rels/{rel}",
        ]
    ],
    [
        'class' => 'Acme\Domain\Dummy\ValueObject\CommentId',
        'alias' => '',
        'aliased_properties' => [],
        'hide_properties' => [],
        'id_properties' => [
            'commentId',
        ],
        'urls' => [
            'self' => ['name' => 'get_comment'],//named route
        ],
        'curies' => [
            'name' => 'example',
            'href' => "http://example.com/docs/rels/{rel}",
        ]
    ],
];

```

### 3.2 Mapping with Mapping class

In order to map with Mapping class, you need to create a new class for each involved class.

This mapping fashion scales way better than using an array. Place each mapping in a separate file.

All Mapping classes will extend the `\NilPortugues\Api\Mappings\HalMapping` interface. 

```php
<?php

class PostMapping implements \NilPortugues\Api\Mappings\HalMapping {

    public function getClass()
    {
        return 'Acme\Domain\Dummy\Post';
    }

    public function getAlias()
    {
        return 'Message';
    }

    public function getAliasedProperties()
    {
        return [
            'author' => 'author',
            'title' => 'headline',
            'content' => 'body',
        ];
    }

    public function getHideProperties()
    {
        return [];
    }

    public function getIdProperties()
    {
        return ['postId'];
    }

    public function getUrls()
    {
        return [
            'self' => ['name' => 'get_post'], //named route
            'comments' => ['name' => 'get_post_comments'],//named route
        ];
    }

    public function getCuries()
    {
        return [
            'name' => 'example',
            'href' => "http://example.com/docs/rels/{rel}",
        ];
    }
}

class PostIdMapping implements \NilPortugues\Api\Mappings\HalMapping{

    public function getClass()
    {
        return 'Acme\Domain\Dummy\ValueObject\PostId';
    }

    public function getAlias()
    {
        return '';
    }

    public function getAliasedProperties()
    {
        return [];
    }

    public function getHideProperties()
    {
        return [];
    }

    public function getIdProperties()
    {
        return ['postId'];
    }

    public function getUrls()
    {
        return [
            'self' => ['name' => 'get_post'],//named route
        ];
    }

    public function getCuries()
    {
        return [
            'name' => 'example',
            'href' => "http://example.com/docs/rels/{rel}",
        ];
    }
}

class UserMapping implements \NilPortugues\Api\Mappings\HalMapping{

    public function getClass()
    {
        return 'Acme\Domain\Dummy\User';
    }

    public function getAlias()
    {
        return '';
    }

    public function getAliasedProperties()
    {
        return [];
    }

    public function getHideProperties()
    {
        return [];
    }

    public function getIdProperties()
    {
        return ['userId'];
    }

    public function getUrls()
    {
        return [
            'self' => ['name' => 'get_user'],//named route
            'friends' => ['name' => 'get_user_friends'],//named route
            'comments' => ['name' => 'get_user_comments'],//named route
        ];
    }

    public function getCuries()
    {
        return [
            'name' => 'example',
            'href' => "http://example.com/docs/rels/{rel}",
        ];
    }
}

class UserIdMapping implements \NilPortugues\Api\Mappings\HalMapping{    
    
    public function getClass()
    {
        return 'Acme\Domain\Dummy\ValueObject\UserId';
    }

    public function getAlias()
    {
        return '';
    }

    public function getAliasedProperties()
    {
        return [];
    }

    public function getHideProperties()
    {
        return [];
    }

    public function getIdProperties()
    {
        return ['userId'];
    }

    public function getUrls()
    {
        return [
            'self' => ['name' => 'get_user'],//named route
            'friends' => ['name' => 'get_user_friends'],//named route
            'comments' => ['name' => 'get_user_comments'],//named route
        ];
    }

    public function getCuries()
    {
        return [
            'name' => 'example',
            'href' => "http://example.com/docs/rels/{rel}",
        ];
    }
}

class CommentMapping implements \NilPortugues\Api\Mappings\HalMapping{

    public function getClass()
    {
        return 'Acme\Domain\Dummy\Comment';
    }

    public function getAlias()
    {
        return '';
    }

    public function getAliasedProperties()
    {
        return [];
    }

    public function getHideProperties()
    {
        return [];
    }

    public function getIdProperties()
    {
        return ['commentId'];
    }

    public function getUrls()
    {
        return [
            'self' => ['name' => 'get_comment'],//named route
        ];
    }

    public function getCuries()
    {
        return [
            'name' => 'example',
            'href' => "http://example.com/docs/rels/{rel}",
        ];
    }
}

class CommentIdMapping implements \NilPortugues\Api\Mappings\HalMapping{

    public function getClass()
    {
        return 'Acme\Domain\Dummy\ValueObject\CommentId';
    }

    public function getAlias()
    {
        return '';
    }

    public function getAliasedProperties()
    {
        return [];
    }    

    public function getHideProperties()
    {
        return [];
    }

    public function getIdProperties()
    {
        return ['commentId'];
    }

    public function getUrls()
    {
        return [
            'self' => ['name' => 'get_comment'],//named route
        ];
    }

    public function getCuries()
    {
        return [
            'name' => 'example',
            'href' => "http://example.com/docs/rels/{rel}",
        ];
    }
}    
```

All the mappings will be contained in the array in the `bootstrap/haljson.php`, but this time the fully qualified class name is required instead.

```php
<?php
//bootstrap/haljson.php
return [
    "\Acme\Mappings\PostMapping",
    "\Acme\Mappings\PostIdMapping",
    "\Acme\Mappings\UserMapping",
    "\Acme\Mappings\UserIdMapping",
    "\Acme\Mappings\CommentMapping",
    "\Acme\Mappings\CommentIdMapping",
];
```

## 3. HAL Serialization

All of this set up allows you to easily use the `Serializer` service as follows:

```php
<?php

namespace App\Http\Controllers;

use Acme\Domain\Dummy\PostRepository;
use NilPortugues\Laravel5\HalJson\HalJsonResponseTrait;


class PostController extends \App\Http\Controllers\Controller
{
    use HalJsonResponseTrait;
       
   /**
    * @var PostRepository
    */
   protected $postRepository;

   /**
    * @var HalJson
    */
   protected $serializer;

   /**
    * @param PostRepository $postRepository
    * @param HalJson $HalJson
    */
   public function __construct(PostRepository $postRepository, HalJson $HalJson)
   {
       $this->postRepository = $postRepository;
       $this->serializer = $HalJson;
   }

   /**
    * @param int $postId
    *
    * @return \Symfony\Component\HttpFoundation\Response
    */
   public function getPostAction($postId)
   {
       $post = $this->postRepository->findById($postId);

       return $this->response($this->serializer->serialize($post));
   }
}
```

**Output:**

```
HTTP/1.1 200 OK
Cache-Control: protected, max-age=0, must-revalidate
Content-type: application/hal+json
```

```json
{
    "post_id": 9,
    "headline": "Hello World",
    "body": "Your first post",
    "_embedded": {
        "author": {
            "user_id": 1,
            "name": "Post Author",
            "_links": {
                "self": {
                    "href": "http://example.com/users/1"
                },
                "example:friends": {
                    "href": "http://example.com/users/1/friends"
                },
                "example:comments": {
                    "href": "http://example.com/users/1/comments"
                }
            }
        },
        "comments": [
            {
                "comment_id": 1000,
                "dates": {
                    "created_at": "2015-08-13T22:47:45+02:00",
                    "accepted_at": "2015-08-13T23:22:45+02:00"
                },
                "comment": "Have no fear, sers, your king is safe.",
                "_embedded": {
                    "user": {
                        "user_id": 2,
                        "name": "Barristan Selmy",
                        "_links": {
                            "self": {
                                "href": "http://example.com/users/2"
                            },
                            "example:friends": {
                                "href": "http://example.com/users/2/friends"
                            },
                            "example:comments": {
                                "href": "http://example.com/users/2/comments"
                            }
                        }
                    }
                },
                "_links": {
                    "example:user": {
                        "href": "http://example.com/users/2"
                    },
                    "self": {
                        "href": "http://example.com/comments/1000"
                    }
                }
            }
        ]
    },
    "_links": {
        "curies": [
            {
                "name": "example",
                "href": "http://example.com/docs/rels/{rel}",
                "templated": true
            }
        ],
        "self": {
            "href": "http://example.com/posts/9"
        },
        "example:author": {
            "href": "http://example.com/users/1"
        },
        "example:comments": {
            "href": "http://example.com/posts/9/comments"
        }
    }
}
```


## 5. HAL Paginated Resource

A pagination object to easy the usage of this package is provided. 

For both XML and JSON output, use the `HalPagination` object to build your paginated representation of the current resource.
 
Methods provided by `HalPagination` are as follows: 

 - `setSelf($self)`
 - `setFirst($first)`
 - `setPrev($prev)`
 - `setNext($next)`
 - `setLast($last)`
 - `setCount($count)`
 - `setTotal($total)`
 - `setEmbedded(array $embedded)`
 
In order to use it, create a new HalPagination instance, use the setters and pass the instance to the `serialize($value)` method of the serializer. 

Everything else will be handled by serializer itself. Easy as that!
 
```php
use NilPortugues\Api\Hal\HalPagination; 
use NilPortugues\Api\Hal\HalSerializer; 
use NilPortugues\Api\Hal\JsonTransformer; 

// ...
//$objects is an array of objects, such as Post::class.
// ...
 
$page = new HalPagination();

//set the amounts
$page->setTotal(20);
$page->setCount(10);

//set the objects
$page->setEmbedded($objects);

//set up the pagination links
$page->setSelf('/post?page=1');
$page->setPrev('/post?page=1');
$page->setFirst('/post?page=1');
$page->setLast('/post?page=1');

$output = $serializer->serialize($page);

``` 

## 6. Response objects

The following `HalJsonResponseTrait` methods are provided to return the right headers and HTTP status codes are available:

```php
    protected function errorResponse($json);
    protected function resourceCreatedResponse($json);
    protected function resourceDeletedResponse($json);
    protected function resourceNotFoundResponse($json);
    protected function resourcePatchErrorResponse($json);
    protected function resourcePostErrorResponse($json);
    protected function resourceProcessingResponse($json);
    protected function resourceUpdatedResponse($json);
    protected function response($json);
    protected function unsupportedActionResponse($json);
```    

## Quality

To run the PHPUnit tests at the command line, go to the tests directory and issue phpunit.

This library attempts to comply with [PSR-1](http://www.php-fig.org/psr/psr-1/), [PSR-2](http://www.php-fig.org/psr/psr-2/), [PSR-4](http://www.php-fig.org/psr/psr-4/) and [PSR-7](http://www.php-fig.org/psr/psr-7/).

If you notice compliance oversights, please send a patch via [Pull Request](https://github.com/nilportugues/laravel5-hal-json/pulls).

## Contribute

Contributions to the package are always welcome!

* Report any bugs or issues you find on the [issue tracker](https://github.com/nilportugues/laravel5-hal-json/issues/new).
* You can grab the source code at the package's [Git repository](https://github.com/nilportugues/laravel5-hal-json).


## Support

Get in touch with me using one of the following means:

 - Emailing me at <contact@nilportugues.com>
 - Opening an [Issue](https://github.com/nilportugues/laravel5-hal-json/issues/new)

## Authors

* [Nil Portugués Calderó](http://nilportugues.com)
* [The Community Contributors](https://github.com/nilportugues/laravel5-hal-json/graphs/contributors)


## License
The code base is licensed under the [MIT license](LICENSE).
