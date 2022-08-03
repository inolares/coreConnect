## 2.0.2

#### 03-Aug-2022

- Fixed bug whenever the token expires - the FetchToken() method had missed sending a normal HTTP header, instead it sends the Authorization: Bearer header, which is of course wrong.

#### 02-Aug-2022

- Added additional logging to php error_log()

## 2.0.1

#### 6-Jul-2022

- Added Symfony 5.4+ service as example
- Updated README.md with short docs about class usage and symfony integration

## 2.0.0

#### 5-Jul-2022

- First relase as composer package

#### 11-Jun-2022

- Added session-based class "coreConnectSession"
- Rewritten old coreConnect class as abstract class "coreConnectBase"
