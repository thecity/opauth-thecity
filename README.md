Opauth-Google
=============
[Opauth][1] strategy for The City authentication.

Opauth is a multi-provider authentication framework for PHP.

Getting started
----------------
1. Install Opauth-Google:
   ```bash
   cd path_to_opauth/Strategy
   git clone git://github.com/thecity/opauth-thecity.git TheCity
   ```

2. Create a plugin/app for The City (see [https://api.onthecity.org/docs/apps](https://api.onthecity.org/docs/apps) for steps)
   - Make sure that redirect URI is set to actual OAuth 2.0 callback URL, usually `http://path_to_your_app/thecity/oauth2callback`

   
3. Configure Opauth-TheCity strategy.

4. Direct user to `http://path_to_your_app/thecity` to authenticate


Strategy configuration
----------------------

Required parameters:

```php
<?php
$config = array(
	'client_id' => 'YOUR APP ID',
	'client_secret' => 'YOUR APP SECRET',
  'redirect_uri' => 'YOUR REDIRECT URI (HAS TO MATCH EXACTLY)',
  'scope' => 'THE SCOPE TO ALLOW',
)
```

Optional parameters:
`scope`, `state`, `subdomain`

License
---------
Opauth-TheCity is MIT Licensed  
Copyright © 2013 City Dev Force

[1]: https://github.com/uzyn/opauth