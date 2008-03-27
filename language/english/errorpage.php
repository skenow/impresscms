<?php
/**
 * Apache Error Type Messages
 */
define( '_ERR_TITLE_400', '%ss Bad Request' );
define( '_ERR_TITLE_DESC_400', 'The request could not be understood by the server due to malformed syntax. The client should not repeat the request without modifications.' );
define( '_ERR_TITLE_401', '%ss Unauthorized' );
define( '_ERR_TITLE_DESC_401', 'The request requires user authentication. The client may repeat the request with a suitable Authorization. If the request already included Authorization credentials, then the this response indicates that authorization has been refused.' );
define( '_ERR_TITLE_402', '%s Payment Required' );
define( '_ERR_TITLE_DESC_402', 'This code is reserved for future use.' );
define( '_ERR_TITLE_403', '%s Forbidden' );
define( '_ERR_TITLE_DESC_403', 'The server understood the request, but is refusing to fulfill it. Authorization will not help and the request should not be repeated.' );
define( '_ERR_TITLE_404', '%s Page Not Found' );
define( '_ERR_TITLE_DESC_404', 'The server has not found anything matching the requested url %s and no indication is given of whether the condition is temporary or permanent.' );
define( '_ERR_TITLE_405', '%s Method Not Allowed' );
define( '_ERR_TITLE_DESC_405', 'The method specified in the Request-Line is not allowed for the resource identified by the requested url "%s" and the response must include an Allow header containing a list of valid methods for the requested resource.' );
define( '_ERR_TITLE_406', '%s Not Acceptable' );
define( '_ERR_TITLE_DESC_406', 'The server has found a resource matching the requested url "%s" but not one that satisfies the conditions identified by the Accept and Accept-Encoding request headers.' );
define( '_ERR_TITLE_407', '%s Proxy Authentication Required' );
define( '_ERR_TITLE_DESC_407', 'The client must first authenticate itself with the proxy. The proxy must return a Proxy-Authenticate header field containing a challenge applicable to the proxy for the requested resource. The client may repeat the request with a suitable Proxy-Authorization header field.' );
define( '_ERR_TITLE_408', '%s Request Timeout' );
define( '_ERR_TITLE_DESC_408', 'The client did not produce a request within the time that the server was prepared to wait. The client may repeat the request without modifications at any later time.' );
define( '_ERR_TITLE_409', '4%s Conflict' );
define( '_ERR_TITLE_DESC_409', 'The request could not be completed due to a conflict with the current state of the resource.' );
define( '_ERR_TITLE_410', '%s Gone' );
define( '_ERR_TITLE_DESC_410', 'The requested resource is no longer available at the server and no forwarding address is known. This condition is considered permanent. Clients with link editing capabilities delete references to the requested url "%s" after user approval.' );
define( '_ERR_TITLE_411', '%s Length Required' );
define( '_ERR_TITLE_DESC_411', 'The server refuses to accept the request without a defined Content-Length. The client may repeat the request if it adds a valid Content-Length header field containing the length of the entity body in the request message.' );
define( '_ERR_TITLE_412', '%s Unless True' );
define( '_ERR_TITLE_DESC_412', 'The condition given in the Unless request-header field evaluated to true when it was tested on the server' );
define( '_ERR_TITLE_413', '%s Request Entity Too Large' );
define( '_ERR_TITLE_DESC_413', 'The requested document is bigger than the server wants to handle now. If the server thinks it can handle it later, it should include a Retry-After header.' );
define( '_ERR_TITLE_414', '%s Request URI Too Long' );
define( '_ERR_TITLE_DESC_414', 'The URI is too long.' );
define( '_ERR_TITLE_415', '%s Unsupported Media Type' );
define( '_ERR_TITLE_DESC_415', 'Request is in an unknown format.' );
define( '_ERR_TITLE_416', '%s Requested Range Not Satisfiable' );
define( '_ERR_TITLE_DESC_416', 'Client included an unsatisfiable Range header in request.' );
define( '_ERR_TITLE_417', '%s Expectation Failed' );
define( '_ERR_TITLE_DESC_417', 'Value in the Expect request header could not be met.' );

/* Server Error 5xx */
define( '_ERR_TITLE_500', '%s Internal Server Error');
define( '_ERR_TITLE_DESC_500', 'The server encountered an unexpected condition which prevented it from fulfilling the request.');
define( '_ERR_TITLE_501', '%s Not Implemented');
define( '_ERR_TITLE_DESC_501', 'The server does not support the functionality required to fulfill the request. This is the appropriate response when the server does not recognize the request method and is not capable of supporting it for any resource.');
define( '_ERR_TITLE_502', '%s Bad Gateway');
define( '_ERR_TITLE_DESC_502', 'The server, while acting as a gateway or proxy, received an invalid response from the upstream server it accessed in attempting to fulfill the request.');
define( '_ERR_TITLE_503', '%s Service Unavailable');
define( '_ERR_TITLE_DESC_503', 'The server is currently unable to handle the request due to a temporary overloading or maintenance of the server. The implication is that this is a temporary condition which will be alleviated after some delay.');
define( '_ERR_TITLE_504', '%s Gateway Timeout');
define( '_ERR_TITLE_DESC_504', 'The server, while acting as a gateway or proxy, did not receive a timely response from the upstream server it accessed in attempting to complete the request.');
define( '_ERR_TITLE_505', '%s HTTP Version Not Supported');
define( '_ERR_TITLE_DESC_505', 'The server, while acting as a gateway or proxy, does not support version of HTTP indicated in request line.');

define('_ERR_TITLE_INFO','<ul>
  <li>If you typed the URL, check the spelling</li>
  <li>Start from the <a href="%s">Home Page</a> and look for links to the information you want.</li>
  <li>Click the <a href="javascript:history.back(1);">back</a> button and try another link. Please report the bad link to the page owner of the previous page. </li>
  <li>Search our website using the form below. </li>
</ul>');
define( '_ERR_CONTACT', 'If you still can\'t find the document you\'re looking for please contact <a href="#">%s</a>.<br /><br />We have logged this problem and this should help us better maintain our website in the future');
define( '_ERR_SEARCH', 'Search ');

define( '_ERR_WEBSITEMAILREPORT', 'Urgent: Website Error Report');

?>