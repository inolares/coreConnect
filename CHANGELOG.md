## 2.1.1

#### 06-Aug-2025

- Fixed check of data attribute, now array_key_exists() is used to allow to return NULL values


## 2.1.0

#### 11-Aug-2023

- Correct spelling of *Token() methods
- Changed method signature of delete(url,params[]) to delete(url,postdata[],params[]) 
- Implemented issue #2 (allow connection to self-signed certificates)
- Fixed wrong lastApiUrl value if first call to API failed (token was always returned). 

## 2.0.2

#### 04-Aug-2022

- Public release

#### 03-Aug-2022

- Added basic tests for phpUnit testing framework
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
