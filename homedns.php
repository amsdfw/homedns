<?php

$key = 'digital ocean api key';
$sub = 'sub level domain, e.g. mail';
$domain = 'top level domain, e.g. google.com';

$url = "https://api.digitalocean.com/v2/domains";

$context = stream_context_create([
  'http' => [
    'header' => "Authorization: Bearer ".$key
  ]
]);

$data = file_get_contents($url.'/'.$domain.'/records', false, $context);

$data = json_decode($data);

$myIp = file_get_contents('http://myip.amsdfw.us');

foreach($data->domain_records as $record) {
  if (
    $record->type == 'A'
    && $record->name==$sub
    && $record->data!=$myIp
  ) {
    $postdata = json_encode([
      'data' => $myIp
     ]);
 
     $opts = [
       'http' => [
         'method'  => 'PUT',
         'header'  => [
           'Content-Type: application/json',
           "Authorization: Bearer ".$key
         ],
         'content' => $postdata
       ]
   ];

   $context  = stream_context_create($opts);
   $result = file_get_contents($url.'/'.$domain.'/records/'.$record->id, false, $context);
   die(print_r($result,true));
}

  echo "<pre>No match: \r\n".print_r($record,true).'</pre><br/>'."\r\n";
};

die('nothing updated');
