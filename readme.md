# Phoenix

### 2.4.2

## Project Description/Purpose

Core API will create a centralized API for all operations on MAD Database. We'll hand over the API docs to any external party who will be developing apps for us. 

Formats supported...

* REST API : https://makeadiff.in/api/api/swagger/doc.html
* GraphQL  : https://makeadiff.in/api/api/graphql/index.html

## Installation

Get the code from github...

```
git clone git@github.com:makeadiff/Phoenix.git
```

Copy the `.env.example` file to `.env` file. Edit it to fit your system. 

Install the libraries that are needed. You'll need `composer` installed in your system.

```
composer install
```

Create the database tables by running migrations...

```
php artisan migrate --seed
```

## Clean Up

After you code, make sure you have run the linter to clean up the code base - to ensure we are all using the same coding standards...

```
php-cs-fixer fix --rules=@PSR2
```

## Problem we are trying to solve

Outsourcing projects to an external company/interns/remote teams requires us to give them database access. This has multiple problems...

* Privacy issue for volunteers(Exposes names, phone numbers, email, etc.)
* Privacy issue for students(Exposes names, age, shelter, etc. Also legal implications(JJ ACT))
* Code integration back to our server is time consuming
* Lack of control over how they do on the backend
* Adds more projects that we have to maintain
* Requires external parties to have a good understanding of our database. Requires more hand-holding - more time consuming for us.

These problems will be solved if we create one centralized API system with which external parties can access MAD Data. All new apps can call this API to do all the operations to the database. 

## Why Core API?

* Faster Development of Mobile Applications(Need for creating the backend is gone)
* Easy Handover to Outsourcing Partners and/or Remote Developing Team.
* Clean & easy way to access MAD data
* Rapid development of any future apps
* Quicker learning time for anyone starting new
* Centralized authentication
* Clean up existing apps for maintainability
* Any future app will be using these APIs and extend APIs a needed

[Reference Document](https://docs.google.com/document/d/1YgDsgXaLp5HERyIkqpBCSs398C1xc54cE1Th6shs17o/) - Logic Cycle for why we choose the API Approach
