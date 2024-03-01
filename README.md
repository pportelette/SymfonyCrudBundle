CrudBundle
==============

This bundle provides basic CRUD endpoints for one given Doctrine entity:

- GET `/entity` returns a paginated result
- GET `/entity/list` returns an array of objects
- GET `/entity/{id}` returns an object
- POST `/entity` creates an entity
- PUT `/entity/{id}` updates an entity
- DELETE `'/entity/{id}` delete an entity


Installation
------------

Open a command console, enter your project directory and execute:

```console
$ composer require pportelette/crud-bundle
```

Applications that don't use Symfony Flex
----------------------------------------

Then, enable the bundle by adding it to the list of registered bundles
in the `config/bundles.php` file of your project:

```php
// config/bundles.php

return [
    Pportelette\CrudBundle\PporteletteCrudBundle::class => ['all' => true],
];
```

Usage
-----

In the following sections we will consider that we have a simple Doctrine Entity 'Category' with its Doctrine repository 'CategoryRepository'.

```php
namespace App\Entity;

use App\Repository\CategoryRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CategoryRepository::class)]
class Category
{ 
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private $id;

    #[ORM\Column(type: "string", length: 100)]
    private $name;

    #[ORM\Column(type: "datetime_immutable")]
    private $created_at;

    /*
        GETTERS AND SETTERS
    */
}
```

#### Create ViewModel
First of all is to create a ViewModel object that will represent our entity in the front.

```php
namespace App\Model;

use Pportelette\CrudBundle\Model\ViewModel;
use App\Entity\Category;

class CategoryVM extends ViewModel {
    public $id;
    public $name;
    private $createdAt;

    public function fromEntity(Category $category = null): void {
        if(!$category) {
            return;
        }
        $this->id = $category->getId();
        $this->name = $category->getName();
        $this->createdAt = $category->getCreatedAt();
    }

    public function toEntity(Category $category = new Category()): Category {
        $category->setName($this->name);
        $category->setCreatedAt($this->createdAt);

        return $category;
    }

    /**
     * Customize the response for the GET /category endpoint
     * Optional
     */
    public function getAll(): array {
        return [
            'id' => $this->id,
            'name' => $this->name
        ];
    }

    /**
     * Customize the response for the GET /category/list endpoint
     * Optional
     */
    public function getList(): array {
        return [
            'name' => $this->name,
        ];
    }
}
```
#### Extend Repository
```php
namespace App\Repository;

// ...
use Pportelette\CrudBundle\Repository\CrudRepository;

class CategoryRepository extends CrudRepository
{
    // ...
}
```

#### Extend Controller
```php
namespace App\Controller;

// ...
use Pportelette\CrudBundle\Controller\CrudController;

class WordController extends CrudController
{
    public function __construct(SerializerInterface $serializer, CategoryRepository $repository)
    {
        $this->configure(
            $serializer,
            $repository, 
            CategoryVM::class
        );
    }
}
```

#### Configure routes
Drive all requests starting by `/category` to CategoryController.

```yaml
category:
    resource: ../src/Controller/CategoryController.php
    type: attribute
    prefix:   /category
    trailing_slash_on_root: false
```

At this point the 6 CRUD endpoints are available.

#### Custom Service
It is possible to override the service methods.

For this create a service `CategoryService.php` that extends `CrudService` and override a method that complies with the Pportelette\CrudBundle\Service\CrudServiceInterface such as:
```php
public function getAll(int $page, array $filters = []): Pageable;
public function getList(array $filters = []): array;
public function getEntity(int $id): ViewModel;
public function createEntity(array $properties): ViewModel;
public function updateEntity(int $id, array $properties): ViewModel;
public function deleteEntity(int $id): void;
```

```php
// src/Service/CategoryService.php
// ...
use Pportelette\CrudBundle\Service\CrudService;
use Pportelette\PageableBundle\Model\Pageable;

class WordService extends CrudService
{
    public function __construct(CategoryRepository $wordRepository)
    {
        parent::__construct($wordRepository, CategoryVM::class);
    }

    public function getAll(int $page, array $params = []): Pageable
    {    
        // Your custom code
    }
}
```

#### Custom Repository
It is possible to override the repository methods.

Your repository already extends the CrudRepository. Simply add a method that complies with the Pportelette\CrudBundle\Repository\CrudRepositoryInterface:
```php
public function getAll(int $page, array $filters = []): Pageable;
public function getList(array $filters = []): array;
```


```php
// src/Repository/CategoryRepository.php

// ...

public function getAll(int $page, array $filters = []): Pageable
{
    $qb = $this->createQueryBuilder('w');

    // Your custom code

    return $this->getPage(
        $qb,
        $page
    );
}
```
