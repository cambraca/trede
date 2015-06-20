Introduction
============

The *Entity* component is used to represent any kind of object. Examples of these are:

- Blog posts
- Uploaded files
- Users
- Roles
- Words in a vocabulary
- Fields ?

On its own the Entity component is not a lot of fun, but through its API you can automatically do things like store
entities in a database, provide arbitrary fields, and quickly build "collections" of entities.

Here is an example of a component that extends *Entity* and uses some of its traits:

~~~ .php
namespace Blog;
class Post extends \Entity\Entity {
  use \Entity\Entity\SimpleDBMapper,  //Provides the ability to save to a database
      \Entity\Entity\Fieldable;       //Enables arbitrary fields on this entity

  protected function table() {        //Required by SimpleDBMapper
    return 'posts';
  }

  protected function properties() {
    return [
      'id' => [
        'type' => 'id',
      ],
      'items' => [
        'type' => 'collection',
        'entity' => 'KPIs\\Item',
      ],
    ];
  }

  protected function fields() {       //Required by Fieldable
    return [
      'title' => [
        'type' => 'Field\\Text',
        'label' => 'Title',
        'required' => TRUE,
        'storage' => 'internal',
      ],
      'content' => [
        'type' => 'Field\\LongText',
        'label' => 'Content',
      ],
    ];
  }

}
~~~

This entity can now be used like this:

~~~ .php
//Load a blog post from the database.
$my_post = Blog\Post::find('abc123');

//Retrieve and change the post title.
print $my_post->title;
$my_post->title .= ' copy';

//Save the post to the database.
$my_post->save();

//Create a new post from an array of data.
$my_new_post = Blog\Post::fromArray(['title' => 'My new blog post!']);
~~~
