Opauth-TheCity
=============
[Opauth][1] strategy for The City authentication.

Implemented based on https://developers.facebook.com/docs/authentication/

Getting started
----------------
1. Install Opauth-TheCity:
   ```bash
   cd path_to_opauth/Strategy
   git clone git@github.com:thecity/thecity-php.git TheCity
   ```

2. Create a plugin/app for The City (see [https://api.onthecity.org/docs/apps](https://api.onthecity.org/docs/apps) for steps)
   - Make sure that redirect URI is set to actual OAuth 2.0 callback URL, usually `http://path_to_your_app/thecity/int_callback`

3. Configure Opauth-TheCity strategy with at least `Client ID` and `Client Secret`.

4. Direct user to `http://path_to_opauth/thecity` to authenticate

Strategy configuration
----------------------

Required parameters:

```php
<?php
'TheCity' => array(
  'client_id' => 'YOUR APP ID',
  'client_secret' => 'YOUR APP SECRET',
  'scope' => 'SCOPE (ie user_basic)'
)
```

License
---------
Opauth-TheCity is MIT Licensed  
Copyright © 2013 City Dev Force

[1]: https://github.com/uzyn/opauth