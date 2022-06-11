;
; coreConnect - PureBasic Version
; @author Sascha 'SieGeL' Pfalz <webmaster@in-ovation.de>
; @licence Bsd
;

DeclareModule coreConnect
  ;--- Use this as parameter for Call-->*params.Param
  ;--- CallParams.Params
  ;--- CallParams\Plist("foo")="bar"
  ;--- Call("/v1/api",#PB_HTTP_Get, #Null, @MCallParams)
  
  Structure Params
    Map Plist.s()  
  EndStructure
  
  ;--- Visible procedures ---
  Declare Init(Username.s,Password.s,Baseurl.s)
  Declare.s GetToken()
  Declare.s GetLastError()
  Declare.s Call(url.s, method, postdata.s = "", *params.Params = #Null)
  Declare.s Get(url.s, postdata.s = "", *params.Params = #Null)
  Declare.s Post(url.s, postdata.s = "", *params.Params = #Null)
  Declare.s Put(url.s, postdata.s = "", *params.Params = #Null)
  Declare.s Delete(url.s, postdata.s = "", *params.Params = #Null)
  Declare.q GetApiCallTime()
  Declare.s GetLastUrl()
EndDeclareModule

Module coreConnect
  
  #Core_Connect_Version = "1.0.0"
  
  ;--- "expires" block from JWT token
  Structure ICTExpires
    date.s
    timezone_type.l
    timezone.s
  EndStructure  
  ;--- "user" block from JWT token
  Structure ICTUser
    id.l
    email.s
    firstname.s
    lastname.s
    login_count.l
    last_login.s
    failed_login_date.s
    create_date.s
    is_daemon.l
  EndStructure  
  ;--- The token structure itself
  Structure ICT
    token.s
    expires.ICTExpires
    user.ICTUser
  EndStructure
  
  ; --- Module globals ---
  Global ict.ICT
  Global.s Username,Password,Baseurl,lastError,lastResult,lastUrl
  Global ExpireDate
  Global ApiCallTime.q                                    ; Runtime for one API call (measures HttpRequest() call)

  ; --- Prototypes ---
  Declare Init(Username.s,Password.s,Baseurl.s)
  Declare.s GetInoCoreError(HttpResponse.s)
  Declare.s EncodeLogin()
  Declare.s GetToken()
  Declare.s GetLastError()
  Declare ConvertJsonDate(json_date.s)
  Declare.s HasValidToken()
  Declare.s prepareResponse(result.s)

;--------------------------------------------------------------------------
; constructor function - sets required username/password/baseurl parameters
;--------------------------------------------------------------------------
Procedure Init(Un.s,Pw.s,Burl.s)
  Username  = Un
  Password  = Pw
  Baseurl   = RTrim(Burl.s,"/") + "/"
EndProcedure

;--------------------------------------------------------------------------
; Returns login data as Base64 encoded string
;--------------------------------------------------------------------------
Procedure.s EncodeLogin()
  Protected *Login
  *Login = UTF8(Username + ":" + Password)
  ProcedureReturn Base64Encoder(*Login, MemorySize(*Login) - 1)
EndProcedure

;--------------------------------------------------------------------------
; Tries to fetch Token from InoCore, sets lastError variable in case of an error
;--------------------------------------------------------------------------
Procedure.s GetToken()
  EncodedLogin.s = EncodeLogin()
  NewMap Header$()
  Header$("Content-Type") = "application/json"
  Header$("Charset") = "utf-8"
  Header$("Authorization") = "Basic " + EncodedLogin
  Header$("User-Agent") = "coreConnectPB/"+#Core_Connect_Version
  sd = ElapsedMilliseconds()
  HttpRequest = HTTPRequest(#PB_HTTP_Post,Baseurl+"token", "["+Chr(34)+"todo.all"+Chr(34)+"]", 0, Header$());
  ApiCallTime = ElapsedMilliseconds() - sd
  If HttpRequest
    HttpCode.i = Val(HTTPInfo(HTTPRequest, #PB_HTTP_StatusCode))
    HttpResponse.s = HTTPInfo(HTTPRequest, #PB_HTTP_Response)
    FinishHTTP(HTTPRequest)
    If HttpCode <> 201
      lastResult = HttpResponse
      lastError = HTTPInfo(HTTPRequest,#PB_HTTP_ErrorMessage)
      If lastError = ""
        lastError = GetInoCoreError(HttpResponse)  
      EndIf
      ProcedureReturn ""
    EndIf
    json = ParseJSON(#PB_Any,HttpResponse)
    If json = 0
      lastError = "Cannot parse JSON data?!"
      ProcedureReturn ""
    EndIf
    ObjectValue = JSONValue(json)
    ExtractJSONStructure(ObjectValue, @ict.ICT, ICT)
    FreeJSON(json)
    ExpireDate = ConvertJsonDate(ict\expires\date)
    lastError = ""
    ProcedureReturn ict\token
  EndIf
  lastError = "GetToken(): HttpRequest() failed?!"
  ProcedureReturn ""
EndProcedure

;--------------------------------------------------------------------------
; Checks if Token is valid and not expired, else fetches new token
;--------------------------------------------------------------------------
Procedure.s HasValidToken()
  If ict\token = "" Or Date() >= ExpireDate
    rc.s = GetToken()
    If rc.s = ""
      lastError = "HasValidToken(): Unable to get new token?!";
      ProcedureReturn ""
    EndIf 
  EndIf
  ProcedureReturn ict\token  
EndProcedure

;--------------------------------------------------------------------------
; Returns lastError message
;--------------------------------------------------------------------------
Procedure.s GetLastError()
  ProcedureReturn lastError
EndProcedure

;--------------------------------------------------------------------------
; Decodes InoCore error json structure
;--------------------------------------------------------------------------
Procedure.s GetInoCoreError(HttpResponse.s)
  NewMap einfo.s()
  json = ParseJSON(#PB_Any,HttpResponse,#PB_JSON_NoCase)
  If json = 0
    Debug lastResult
    ProcedureReturn "Invalid JSON - Unable to parse InoCore error json!"
  EndIf
  ov = JSONValue(json)  
  statusCode = GetJSONInteger(GetJSONMember(ov,"statusCode"))
  ExtractJSONMap(GetJSONMember(ov,"error"),einfo())
  FreeJSON(json)
  ProcedureReturn Str(statusCode) + " - " + einfo("description")    
EndProcedure    

;--------------------------------------------------------------------------
; Converts json date in format 2022-05-18 00:48:01.205576 to internal PB format
;--------------------------------------------------------------------------
Procedure ConvertJsonDate(json_date.s)
  dt_no_ms.s = Mid(json_date,1,FindString(json_date,".")-1)
  PB_Date = ParseDate("%yyyy-%mm-%dd %hh:%ii:%ss",dt_no_ms)
  ProcedureReturn PB_Date
EndProcedure

;--------------------------------------------------------------------------
; Builds a query based on given Map. Based on PHP's http_build_query() method
;--------------------------------------------------------------------------
Procedure.s HttpBuildQuery(Map Params.s())
  Protected returl.s = "?"
  If Params()
    ResetMap(Params())
    While(NextMapElement(Params()))  
      key.s = MapKey(Params()) 
      val.s = URLEncoder(Params(key.s))    
      returl=returl+URLEncoder(key)+"="+val+"&"  
    Wend
    ProcedureReturn RTrim(returl,"&")
  EndIf
EndProcedure

;--------------------------------------------------------------------------------------------------
; Outputs datatype of current json value
;--------------------------------------------------------------------------------------------------
Procedure.s JsonTypeText(jsonval)
  Select JSONType(jsonval)
    Case #PB_JSON_Null:    ProcedureReturn "null"
    Case #PB_JSON_String:  ProcedureReturn "String: " + GetJSONString(jsonval)
    Case #PB_JSON_Number:  ProcedureReturn "Number: " + StrD(GetJSONDouble(jsonval))    
    Case #PB_JSON_Boolean: ProcedureReturn "Bool:" + Str(GetJSONBoolean(jsonval))
    Case #PB_JSON_Array:   ProcedureReturn "array"
    Case #PB_JSON_Object:  ProcedureReturn "object"
  EndSelect
EndProcedure

;--------------------------------------------------------------------------
; Checks result from InoCore, sets lastError in case of an error.
;--------------------------------------------------------------------------
Procedure.s prepareResponse(result.s)
  json = ParseJSON(#PB_Any,result,#PB_JSON_NoCase)
  lastResult = result
  If IsJSON(json) = #Null
    lastError = "prepareResponse(): Invalid JSON - Unable To parse!"  
    FreeJSON(json)
    ProcedureReturn ""
  EndIf
  ov = JSONValue(json)  
  js_st = GetJSONMember(ov,"statusCode")
  If js_st = 0 
    lastError = "prepareResponse(): No statusCode field found - API response is invalid!"
      FreeJSON(json)
      ProcedureReturn ""
  EndIf
  statusCode = GetJSONInteger(js_st)
  js_data = GetJSONMember(ov,"data")
  If js_data = 0 
    lastError = "prepareResponse(): No data field found - API response is invalid!"
    FreeJSON(json)
    ProcedureReturn ""
  EndIf
  If JSONType(js_data) = #PB_JSON_Object
    js_error = GetJSONMember(js_data,"error")
    If js_error
      lastError = "prepareResponse(): "+GetJSONString(js_error)
      FreeJSON(json)  
      ProcedureReturn ""
    EndIf
  EndIf
  FreeJSON(json)
  lastError = ""  ; Clear last error field, as everything was working
  lastResult = ""
  ProcedureReturn result
EndProcedure

;--------------------------------------------------------------------------
; Generic HTTP call method.
;--------------------------------------------------------------------------
Procedure.s Call(url.s, method, postdata.s = "", *params.Params = #Null)
  If HasValidToken() = ""
    ProcedureReturn ""
  EndIf
  NewMap Header$()
  Header$("Content-Type") = "application/json"
  Header$("Charset") = "utf-8"
  Header$("Authorization") = "Bearer " + ict\token
  Header$("User-Agent") = "coreConnectPB/"+#Core_Connect_Version
  lastUrl = url;
  sendurl.s = Baseurl + url  
  If *params.Params 
    sendurl = sendurl + HttpBuildQuery(*params\Plist())
  EndIf
  ;Debug sendurl
  sd = ElapsedMilliseconds()
  HttpRequest = HTTPRequest(method,sendurl, postdata,0, Header$())
  ApiCallTime = ElapsedMilliseconds() - sd
  lastResult = HTTPInfo(HTTPRequest, #PB_HTTP_Response)
  If HttpRequest = #Null
    lastError = "Call(): HTTPrequest() failed?"
    ProcedureReturn ""
  EndIf
  HttpCode.i = Val(HTTPInfo(HTTPRequest, #PB_HTTP_StatusCode))
  HttpResponse.s = HTTPInfo(HTTPRequest, #PB_HTTP_Response)
  FinishHTTP(HTTPRequest)
  If HttpCode <> 200
    lastError = HTTPInfo(HTTPRequest,#PB_HTTP_ErrorMessage)
      If lastError = ""
        lastError = GetInoCoreError(HttpResponse)  
      EndIf
    ProcedureReturn ""
  EndIf
  ;Debug HttpResponse
  ProcedureReturn prepareResponse(HttpResponse)
EndProcedure

;--------------------------------------------------------------------------
; HTTP GET method.
;--------------------------------------------------------------------------
Procedure.s Get(url.s, postdata.s = "", *params.Params = #Null)
  ProcedureReturn Call(url, #PB_HTTP_Get, postdata, *params.Params)  
EndProcedure

;--------------------------------------------------------------------------
; HTTP POST method.
;--------------------------------------------------------------------------
Procedure.s Post(url.s, postdata.s = "", *params.Params = #Null)
  ProcedureReturn Call(url, #PB_HTTP_Post, postdata, *params.Params)  
EndProcedure

;--------------------------------------------------------------------------
; HTTP PUT method.
;--------------------------------------------------------------------------
Procedure.s Put(url.s, postdata.s = "", *params.Params = #Null)
  ProcedureReturn Call(url, #PB_HTTP_Put, postdata, *params.Params)  
EndProcedure

;--------------------------------------------------------------------------
; HTTP DELETE method.
;--------------------------------------------------------------------------
Procedure.s Delete(url.s, postdata.s = "", *params.Params = #Null)
  ProcedureReturn Call(url, #PB_HTTP_Delete, postdata, *params.Params)  
EndProcedure

;--------------------------------------------------------------------------
; Returns number of milliseconds required for an API call
;--------------------------------------------------------------------------
Procedure.q GetApiCallTime()
  ProcedureReturn ApiCallTime
EndProcedure

;--------------------------------------------------------------------------
; Returns last API url that was called
;--------------------------------------------------------------------------
Procedure.s GetLastUrl()
  ProcedureReturn lastUrl  
EndProcedure


EndModule
