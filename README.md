# coreConnect - Helper classes to communicate with InoCore

This composer package provides classes to easily communicate with an InoCore 
installation.

To make integration easy multiple classes are provided, each of them integrates
for a specific requirement.

Currently there are helper classes to integrate with session based PHP sites and
for CLI commands.

In addition you'll find implementations for other languages like Python or 
PureBasic inside the contrib folder.


## REQUIREMENTS

- PHP 8.0 or newer
- InoCore installation with valid credentials
- PureBasic must be at leat 5.73 or newer
- Python 3.6 or higher

## INSTALLATION

Best way is to install everything via composer by executing the following line
in your project directory:

``composer require inolares/coreConnect``

If you do not use composer or would like to install it manually just copy at
least the base class "coreConnectBase.php" and one of the support classes that
fit your environment. 
Of course you're completly free to implement your own class, just make sure that 
you extend from abstract class coreConnectBase.php.

## How to use

Please refer to the **ManualTests/** directory for some examples. The class should
be pretty self-explanatory.


