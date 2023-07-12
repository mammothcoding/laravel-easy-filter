
# laravel-easy-filter

Readme in other languages: [RU](https://github.com/mammothcoding/laravel-easy-filter/blob/master/README.ru.md)

![alt text](./filters.jpg "laravel-easy-filter")

[![Latest Stable Version](https://img.shields.io/packagist/v/mammothcoding/laravel-easy-filter)](https://packagist.org/packages/mammothcoding/laravel-easy-filter)
[![License](https://poser.pugx.org/mehdi-fathi/eloquent-filter/license)](https://packagist.org/packages/mehdi-fathi/eloquent-filter)
[![GitHub stars](https://img.shields.io/github/stars/mammothcoding/laravel-easy-filter)](https://github.com/mammothcoding/laravel-easy-filter/stargazers)
[![Monthly Downloads](https://img.shields.io/packagist/dm/mammothcoding/laravel-easy-filter?color=yellow)](https://packagist.org/packages/mammothcoding/laravel-easy-filter)
[![Github downloads](https://img.shields.io/github/downloads/mammothcoding/laravel-easy-filter/total.svg)](https://github.com/mammothcoding/laravel-easy-filter.git)


## Table of Content

- [Requirements](#electric_plug-Requirements)
- [Introduction](#microphone-Introduction)
- [Installation](#electric_plug-Installation)
- [Usage](#Usage)
  - [Simple Examples](#Simple-Examples)
  - [All filtering methods](#All-filtering-methods)
- [Methods](#Methods)
- [License](#License)

## :electric_plug: Requirements
- PHP 7.4+
- Laravel 6.0+

## :microphone: Introduction

Easy filter and sorter for indexlike requests and listings of models.
It is possible to use several methods of filtering in a query and sorting the result.

## :electric_plug: Installation

Run this Composer command to install the latest version

        $ composer require mammothcoding/laravel-easy-filter

## Usage
For usage you need to form the correct request to the server and
process it in our filtering service to get the result in a convenient form.

- #### Format of get request headers:

***for filtering:***
```
    http://{adress}/{path}?filter=[["{fieldname}","{filtering method}","{value}"]]
```
***for sorting:***
```
    http://{adress}/{path}?sort=[["{fieldname}","{asc/desc}"]]
```

### Simple Examples


- #### An example of processing an index request on the server to get a list of users of the User model in the route body in /routes/web.php :

```php
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Mammothcoding\LaravelEasyFilter\EasyFilter;

Route::get('/users', static function (Request $request) {
    $filter = new EasyFilter('App\Models\User', $request); // Creating a filter object, specifying the model, passing the request data
    $filter->filter(); // Applying a filter
    $filter->sort(); // Sorting
    $result = $filter->getResultArray(); // Get the result in the array
    return view('users', ['res' => $result]); // Controller returns the rendered view with result
});
```
- #### An example of a get request for a model list with a specific value of one column:
```
http://0.0.0.0/users?filter=[["name","=","Armani Harber"]]
```
- #### An example of a get-request for a model list with a specific ending in the value of one column:
```
http://0.0.0.0/users?filter=[["email","endswith","gmail.com"]]
```
Actions in code:
```php
namespace App\Http\Controllers;

use App\User;
use Mammothcoding\LaravelEasyFilter\EasyFilter;

class UsersController
{
    public function index()
    {
        $filter = new EasyFilter('App\Models\User', request()); // Creating a filter object, specifying the model, passing the request data
        $result = $filter->filter()->toArray(); // Apply the filter and convert the resulting collection into an array

        return view('users', ['$result' => $result]); // Controller returns the rendered view with result
    }
}
```
- #### An example of a get request for a model list with several filtering, sorting and pagination methods:
```
http://0.0.0.0/users?filter=[["email","endswith","gmail.com"],["created_at","<","2023-01-01"]]&sort=[["created_at","desc"]]
```

Actions in code:
```php
namespace App\Http\Controllers;

use App\User;
use Mammothcoding\LaravelEasyFilter\EasyFilter;

class UsersController
{
    public function index()
    {
        $filter = new EasyFilter('App\Models\User', request()); // Creating a filter object, specifying the model, passing the request data
        $filter->filter(); // Applying a filter
        $filter->sort(); // Sorting
        $result = $filter->getResultBuilder()
                ->paginate($this->request->input('perpage') ?? 1000)
                ->toArray();

        return view('users', ['$result' => $result]); // Controller returns the rendered view with result
    }
}
```
- #### An example of a query for a model list with filtering and multiple sorting, sorting will be applied sequentially:
```
http://0.0.0.0/users?filter=[["created_at","<","2023-01-01"]]&sort=[["name","desc"],["created_at","desc"]]
```

Actions in code:
```php
namespace App\Http\Controllers;

use App\User;
use Mammothcoding\LaravelEasyFilter\EasyFilter;

class UsersController
{
    public function index()
    {
        $filter = new EasyFilter('App\Models\User', request()); // Creating a filter object, specifying the model, passing the request data
        $filter->filter(); // Applying a filter
        $filter->sort(); // Sorting
        $result = $filter->getResultArray(); // Get the result as an array

        return view('users', ['$result' => $result]); // Controller returns the rendered view with result
    }
}
```



### All filtering methods
Methods in the 2nd value in the array of filtering rule:

Example: filter=[["created_at",`"<"`,"2023-01-01"]]

#
- ### ***Standard mathematical comparison operators***

=, <>, >, >=, <, <=

- ### ***in***

Selection of the values present in the specified.

Example: `filter=[["groups","in",["medics","programmers"]]]`

- ### ***notin***

Selecting values that are not present in the specified ones.

Example: `filter=[["groups","notin",["medics","programmers"]]]`

- ### ***between***

Selection values from the specified range.

Example: `filter=[["created_at","between",["2023-07-14T18:27:59.000000Z","2023-07-19T18:29:58.000000Z"]]]`

- ### ***notbetween***

Selects values that are not within the specified range.

Example: `filter=[["created_at","notbetween",["2023-07-14T18:27:59.000000Z","2023-07-19T18:29:58.000000Z"]]]`

- ### ***startswith***

Selects values that start with the specified value.

Example: `filter=[["created_at","startswith","2023-07-14"]]`

- ### ***endswith***

Selects values that end with the specified value.

Example: `filter=[["email","endswith",".com"]]`

- ### ***contains***

Selects values that contain the specified value.

Example: `filter=[["name","contains","Dr."]]`

- ### ***notcontains***

Selects values that do not contain the specified value.

Example: `filter=[["name","notcontains","Dr."]]`


## Methods

Methods of class EasyFilter:

- ### ***filter()***

Adds filtering conditions to the object's builder.

- ### ***sort()***

Adds sort conditions to the object's builder.

- ### ***getResultBuilder()***

Get the resulting object's builder directly as an Eloquent Builder.

- ### ***getResultCollection()***

Get result from object as Collection.

- ### ***getResultArray()***

Get the result from an object as array.

## License

[MIT](https://choosealicense.com/licenses/mit/)