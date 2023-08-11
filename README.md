# coreConnect - Helper classes to communicate with InoCore

This composer package provides classes to easily communicate with an InoCore 
installation.

To make integration easy multiple classes are provided, each of them integrates
for a specific requirement.

Currently there are helper classes to integrate with session based PHP sites, 
CLI commands and a Symfony 5.4+ compatible service class.

In addition you'll find implementations for other languages inside the contrib 
folder, currently only for PureBasic, but others are welcome! :)

Python users should take a look to our PIP package:
https://pypi.org/project/core-connect/


## REQUIREMENTS

- PHP 8.0 or newer
- InoCore installation with valid credentials
- PureBasic must be at leat 5.73 or newer
- For Symfony at least 5.4 must be used

## INSTALLATION

Best way is to install everything via composer by executing the following line
in your project directory:

``composer require inolares/coreconnect``

If you do not use composer or would like to install it manually just copy at
least the base class "coreConnectBase.php" and one of the support classes that
fit your environment. 
Of course you're completly free to implement your own class, just make sure that 
you extend from abstract class coreConnectBase.php.

## How to use

Please refer to the **ManualTests/** directory for some examples. The class should
be pretty self-explanatory.

Here's a short explanation how to use it:

1. Create an object
2. Call the method "init($user,$pass,$host)" to setup credentials for InoCore
3. Start calling API methods like get("v1/ping") etc.

## Symfony hints

Copy the supplied file "examples/coreConnectService.php" to your "src/Service"
folder.
Now you can use DI to get a reference to the coreConnectService, i.e.:
````
public function __construct(coreConnectService $coreConnect) {
  parent::__construct();
  $coreConnect = $coreConnect;
  $coreConnect->init($user,$pass,$url);
}
````

## Class methods

The following methods must be implemented by you:

- setApiUrl() / getApiUrl()
- setApiUser() / getApiUser()
- setApiPass() / getApiPass()
- setExpires() / getExpires()
- setToken() / getToken()
- setJwtUser() / getJwtUser()

In addition, the following methods are implemented:

- init()
- clearCredentials()
- HasValidToken()
- FetchToken()
- call()
- get()
- post()
- put()
- delete()
- prepareResponse()
- getLastUrl()
- setCurlOpts()           [2.1.0+]

## PHPUnit tests

Some tests for PHPUnit are provided, to run these tests do the following:

```
$> composer install
$> vendor/bin/phpunit tests
```

## Some hints

#### Only valid for coreConnect 2.1.0 or newer!

If you want to connect to an https-enabled InoCore instance using a self-signed
certificate make sure to disable SSL peer verification first! To do this call
this **BEFORE** init() is called:

````
$coreConnect->setCurlOpts([CURLOPT_SSL_VERIFYPEER => false]);
````
You can provide every CURLOPT_* constant here, but it is recommended to set only
specific parameters for your specific setup and leave everything else at classes'
default values!
See https://www.php.net/manual/de/function.curl-setopt.php for a complete list.

coreConnect would merge it's own config setup with your provided values before
performing the API request to InoCore. It is also possible to change parameters
between multiple API calls just by calling setCurlOpts() right before the API
call is performed.
